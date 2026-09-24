<?php

namespace App\Http\Controllers;

use Illuminate\Http\Client\ConnectionException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class UpdatesRegistrationController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[+0-9().\-\s]{7,30}$/'],
        ]);
        if ($validator->fails()) {
            return response()->json([
                'message' => 'Please check the details you entered.',
                'errors' => $validator->errors(),
            ], 422);
        }
        $data = $validator->validated();

        $apiKey = (string) config('services.mailchimp.key');
        $audienceId = (string) config('services.mailchimp.audience_id');
        if (! preg_match('/-([a-z]{2}\d+)$/i', $apiKey, $matches) || ! preg_match('/^[a-zA-Z0-9]+$/', $audienceId)) {
            Log::warning('Mailchimp registration is not configured.');

            return response()->json(['message' => 'Registration is unavailable right now. Please try again later.'], 503);
        }

        $email = strtolower(trim($data['email']));
        $nameParts = preg_split('/\s+/', trim($data['name']), 2);
        $memberUrl = 'https://'.strtolower($matches[1]).'.api.mailchimp.com/3.0/lists/'.$audienceId.'/members/'.md5($email);
        $mailchimp = Http::withBasicAuth('site', $apiKey)
            ->acceptJson()
            ->withHeaders(['User-Agent' => (string) config('services.mailchimp.app')])
            ->timeout(10);
        $caBundle = (string) config('services.mailchimp.ca_bundle');
        if ($caBundle !== '') {
            $mailchimp = $mailchimp->withOptions(['verify' => base_path($caBundle)]);
        }

        try {
            $member = $mailchimp->put($memberUrl, [
                'email_address' => $email,
                'status_if_new' => 'pending',
                'merge_fields' => [
                    'FNAME' => $nameParts[0],
                    'LNAME' => $nameParts[1] ?? '',
                    'PHONE' => trim($data['phone']),
                ],
            ]);

            if (! $member->successful()) {
                Log::warning('Mailchimp contact update failed.', ['status' => $member->status()]);

                return $this->unavailable();
            }

            $status = $member->json('status');
            if (! in_array($status, ['pending', 'subscribed'], true)) {
                $confirmation = $mailchimp->patch($memberUrl, ['status' => 'pending']);
                if (! $confirmation->successful() || $confirmation->json('status') !== 'pending') {
                    Log::warning('Mailchimp contact confirmation failed.', ['status' => $confirmation->status()]);

                    return $this->unavailable();
                }
                $status = 'pending';
            }

            $tag = $mailchimp->post($memberUrl.'/tags', [
                'tags' => [['name' => 'Registered for updates', 'status' => 'active']],
            ]);
            if (! $tag->successful()) {
                Log::warning('Mailchimp contact tagging failed.', ['status' => $tag->status()]);

                return $this->unavailable();
            }

            return response()->json([
                'message' => $status === 'pending'
                    ? 'Thanks! Please check your email to confirm your registration.'
                    : 'Thanks! You are registered for updates.',
            ]);
        } catch (ConnectionException $exception) {
            Log::warning('Mailchimp registration request failed.', ['exception' => $exception::class]);

            return $this->unavailable();
        }
    }

    private function unavailable(): JsonResponse
    {
        return response()->json(['message' => 'We could not complete your registration right now. Please try again.'], 502);
    }
}
