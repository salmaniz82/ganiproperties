<?php

namespace Tests\Feature;

use App\Mail\ContactEnquiry;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class ContactEnquiryTest extends TestCase
{
    private function details(): array
    {
        return ['name' => 'Aisha Khan', 'email' => 'aisha@example.com', 'phone' => '', 'interest' => 'Renting', 'message' => "Please arrange a viewing.\nThank you."];
    }

    public function test_form_posts_to_laravel_and_keeps_property_prefill(): void
    {
        $this->get('/contact?interest=Renting&property=Balham%20flat')->assertOk()
            ->assertSee('action="'.route('contact.send').'"', false)
            ->assertSee('name="_token"', false)->assertSee('I am interested in Balham flat.');
    }

    public function test_enquiry_uses_configured_recipient_and_visitor_reply_to(): void
    {
        Mail::fake();
        config(['services.contact_enquiry.to' => 'team@example.com']);
        $this->post('/contact', $this->details())->assertRedirect('/contact')->assertSessionHas('success');
        Mail::assertSent(ContactEnquiry::class, function ($mail) {
            return $mail->hasTo('team@example.com')
                && $mail->envelope()->replyTo[0]->address === 'aisha@example.com'
                && $mail->details['phone'] === null
                && $mail->details['interest'] === 'Renting';
        });
        Mail::assertSentCount(1);
    }

    public function test_invalid_input_is_retained_without_sending(): void
    {
        Mail::fake();
        $this->from('/contact')->post('/contact', array_replace($this->details(), ['email' => 'invalid', 'interest' => 'Unknown', 'message' => str_repeat('x', 3001)]))
            ->assertRedirect('/contact')->assertSessionHasErrors(['email', 'interest', 'message'])
            ->assertSessionHasInput('name', 'Aisha Khan');
        $this->get('/contact')->assertSee('value="Aisha Khan"', false);
        Mail::assertNothingSent();
    }

    public function test_delivery_failure_retains_input_and_shows_no_success(): void
    {
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Private transport details'));
        $this->post('/contact', $this->details())->assertRedirect('/contact')
            ->assertSessionHasErrors(['contact' => 'We could not send your enquiry right now. Please try again later or contact us by phone or WhatsApp.'])->assertSessionHasInput('message', $this->details()['message'])
            ->assertSessionMissing('success');
    }

    public function test_delivery_error_is_visible_after_redirect(): void
    {
        Mail::shouldReceive('to')->once()->andReturnSelf();
        Mail::shouldReceive('send')->once()->andThrow(new \RuntimeException('Private transport details'));
        $this->followingRedirects()->post('/contact', $this->details())
            ->assertOk()->assertSee('We could not send your enquiry')->assertDontSee('Private transport details');
    }
    public function test_repeated_submissions_are_rate_limited(): void
    {
        Mail::fake();
        for ($attempt = 0; $attempt < 5; $attempt++) {
            $this->post('/contact', $this->details())->assertRedirect('/contact');
        }
        $this->post('/contact', $this->details())->assertStatus(429);
        Mail::assertSentCount(5);
    }

    public function test_email_includes_all_fields_and_escapes_html(): void
    {
        $mail = new ContactEnquiry(array_replace($this->details(), ['message' => "<script>alert(1)</script>\nSecond line", 'phone' => '+44 1234567890']));
        $html = $mail->render();
        $this->assertStringContainsString('Aisha Khan', $html);
        $this->assertStringContainsString('+44 1234567890', $html);
        $this->assertStringContainsString('Renting', $html);
        $this->assertStringContainsString('&lt;script&gt;', $html);
        $this->assertStringNotContainsString('<script>', $html);
        $this->assertStringContainsString('<br', $html);
    }
}
