<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LandlordEnquiry extends Mailable
{
    use Queueable, SerializesModels;

    public function __construct(public array $details) {}

    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'New landlord service enquiry',
            replyTo: [new Address($this->details['email'], $this->details['name'])],
        );
    }

    public function content(): Content
    {
        return new Content(view: 'emails.landlord-enquiry');
    }
}
