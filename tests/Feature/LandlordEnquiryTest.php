<?php

namespace Tests\Feature;

use App\Mail\LandlordEnquiry;
use App\Models\Page;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class LandlordEnquiryTest extends TestCase
{
    use RefreshDatabase;

    public function test_landlord_page_shows_enquiry_form(): void
    {
        Page::create([
            'slug' => 'landlords',
            'title' => 'Landlords',
            'customizer_template' => 'page-customizer/templates/landlords.json',
            'is_active' => true,
        ]);

        $this->get(route('landlords'))->assertOk()
            ->assertSee('Tell us about your property')
            ->assertSee('Property address')
            ->assertSee('Property type')
            ->assertSee(route('landlords.enquiry'));
    }

    public function test_enquiry_is_addressed_to_the_team_with_customer_reply_to(): void
    {
        Mail::fake();

        $this->from(route('landlords'))->post(route('landlords.enquiry'), [
            'name' => 'Aisha Khan',
            'email' => 'aisha@example.com',
            'phone' => '+44 7828 454111',
            'address' => "10 High Street\nLondon SW12 9BW",
            'property_type' => 'Flat / apartment',
        ])->assertRedirect(route('landlords').'#landlord-enquiry')
            ->assertSessionHas('landlord_enquiry_sent');

        Mail::assertSent(LandlordEnquiry::class, function (LandlordEnquiry $mail) {
            return $mail->hasTo('hello@ganipropertyservices.co.uk')
                && $mail->envelope()->replyTo[0]->address === 'aisha@example.com'
                && $mail->details['address'] === "10 High Street\nLondon SW12 9BW";
        });
    }

    public function test_invalid_enquiry_is_not_sent(): void
    {
        Mail::fake();

        $this->from(route('landlords'))->post(route('landlords.enquiry'), [
            'name' => '',
            'email' => 'invalid',
            'phone' => 'abc',
            'address' => '',
            'property_type' => 'Invalid',
        ])->assertSessionHasErrors(['name', 'email', 'phone', 'address', 'property_type']);

        Mail::assertNothingSent();
    }
}
