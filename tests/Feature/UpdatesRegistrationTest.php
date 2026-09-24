<?php

namespace Tests\Feature;

use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class UpdatesRegistrationTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        config()->set('services.mailchimp.app', 'Gani Test');
        config()->set('services.mailchimp.key', 'test-key-us22');
        config()->set('services.mailchimp.audience_id', 'abc123');
    }

    public function test_registration_adds_a_pending_contact_and_updates_tag(): void
    {
        Http::fakeSequence()
            ->push(['status' => 'pending'], 200)
            ->push([], 204);

        $this->postJson(route('updates.register'), [
            'name' => 'Aisha Khan',
            'email' => 'AISHA@example.com',
            'phone' => '+44 7828 454111',
        ])->assertOk()->assertJsonPath('message', 'Thanks! Please check your email to confirm your registration.');

        $memberUrl = 'https://us22.api.mailchimp.com/3.0/lists/abc123/members/'.md5('aisha@example.com');
        Http::assertSent(fn (Request $request) => $request->method() === 'PUT'
            && $request->url() === $memberUrl
            && $request['status_if_new'] === 'pending'
            && $request['merge_fields'] === [
                'FNAME' => 'Aisha',
                'LNAME' => 'Khan',
                'PHONE' => '+44 7828 454111',
            ]);
        Http::assertSent(fn (Request $request) => $request->method() === 'POST'
            && $request->url() === $memberUrl.'/tags'
            && $request['tags'] === [['name' => 'Registered for updates', 'status' => 'active']]);
        Http::assertSentCount(2);
    }

    public function test_registration_rejects_invalid_fields_before_contacting_mailchimp(): void
    {
        Http::fake();

        $this->postJson(route('updates.register'), [
            'name' => '',
            'email' => 'not-an-email',
            'phone' => 'abc',
        ])->assertUnprocessable()->assertJsonValidationErrors(['name', 'email', 'phone']);

        Http::assertNothingSent();

    }

    public function test_registration_reports_a_mailchimp_failure_without_claiming_success(): void
    {
        Http::fakeSequence()->push(['detail' => 'Unavailable'], 500);

        $this->postJson(route('updates.register'), [
            'name' => 'Aisha Khan',
            'email' => 'aisha@example.com',
            'phone' => '+44 7828 454111',
        ])->assertStatus(502)->assertJsonPath('message', 'We could not complete your registration right now. Please try again.');

        Http::assertSentCount(1);
    }

    public function test_registration_requires_the_updates_tag_to_succeed(): void
    {
        Http::fakeSequence()
            ->push(['status' => 'subscribed'], 200)
            ->push(['detail' => 'Unavailable'], 500);

        $this->postJson(route('updates.register'), [
            'name' => 'Aisha Khan',
            'email' => 'aisha@example.com',
            'phone' => '+44 7828 454111',
        ])->assertStatus(502);

        Http::assertSentCount(2);
    }
}
