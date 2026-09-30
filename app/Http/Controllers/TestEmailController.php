<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Illuminate\View\View;
use Throwable;

class TestEmailController extends Controller
{
    public const RECIPIENT = 'salmaniz.82@gmail.com';

    public function show(): View
    {
        return view('admin.test-email', ['recipient' => self::RECIPIENT]);
    }

    public function send(): RedirectResponse
    {
        $mailer = config('mail.default');
        $transport = config("mail.mailers.{$mailer}.transport");

        if (in_array($transport, ['log', 'array', 'failover'], true)) {
            return redirect()->route('test-email')->withErrors([
                'mail' => 'Select a delivery mailer such as sendmail or smtp and refresh the server configuration cache before testing. The current mailer could record the message without delivering it.',
            ]);
        }

        try {
            Mail::raw(
                'This is a Laravel email delivery test from '.config('app.url').".\nEnvironment: ".app()->environment()."\nSent at: ".now()->toIso8601String(),
                function (Message $message): void {
                    $message->to(self::RECIPIENT)->subject('Gani Laravel mail test');
                },
            );
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('test-email')->withErrors([
                'mail' => 'The mail service could not accept the test email. Check the server Laravel logs for details.',
            ]);
        }

        return redirect()->route('test-email')->with('success',
            'The mail service accepted the test email for '.self::RECIPIENT.'. Check the inbox and spam folder to confirm delivery.'
        );
    }
}
