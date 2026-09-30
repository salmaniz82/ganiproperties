<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Mail\Message;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mime\Email;
use Tests\TestCase;

class TestEmailTest extends TestCase
{
    private function admin(): User
    {
        return (new User)->forceFill(['id' => 98765, 'name' => 'Test Admin', 'email' => 'admin@example.com', 'is_admin' => true]);
    }

    public function test_guests_and_non_admins_cannot_send(): void
    {
        Mail::shouldReceive('raw')->never();
        $this->get('/test-email')->assertRedirect('/login');
        $this->post('/test-email')->assertRedirect('/login');
        $this->actingAs($this->admin()->forceFill(['is_admin' => false]));
        $this->get('/test-email')->assertForbidden();
        $this->post('/test-email')->assertForbidden();
    }

    public function test_viewing_the_page_does_not_send(): void
    {
        Mail::shouldReceive('raw')->never();
        $this->actingAs($this->admin())->get('/test-email')->assertOk()
            ->assertSee('salmaniz.82@gmail.com')->assertSee('Send test email');
    }

    public function test_admin_can_send_only_to_the_fixed_recipient_and_is_rate_limited(): void
    {
        config(['mail.default' => 'sendmail']);
        Mail::shouldReceive('raw')->once()->withArgs(function ($body, $callback) {
            $email = new Email;
            $callback(new Message($email));
            $this->assertSame(['salmaniz.82@gmail.com'], array_map(fn ($address) => $address->getAddress(), $email->getTo()));
            $this->assertSame('Gani Laravel mail test', $email->getSubject());
            $this->assertStringContainsString('Environment: testing', $body);
            return true;
        });
        $this->actingAs($this->admin())->post('/test-email', ['to' => 'other@example.com'])
            ->assertRedirect('/test-email')->assertSessionHas('success');
        $this->post('/test-email')->assertStatus(429);
    }

    public function test_non_delivery_mailers_are_not_reported_as_success(): void
    {
        config(['mail.default' => 'log']);
        Mail::shouldReceive('raw')->never();
        $this->actingAs($this->admin())->post('/test-email')
            ->assertRedirect('/test-email')->assertSessionHasErrors('mail');
    }

    public function test_transport_failure_shows_an_error_without_exposing_details(): void
    {
        config(['mail.default' => 'sendmail']);
        Mail::shouldReceive('raw')->once()->andThrow(new \RuntimeException('Private transport details'));
        $this->actingAs($this->admin())->post('/test-email')
            ->assertRedirect('/test-email')->assertSessionHasErrors('mail')->assertSessionMissing('success');
        $this->get('/test-email')->assertDontSee('Private transport details');
    }
}
