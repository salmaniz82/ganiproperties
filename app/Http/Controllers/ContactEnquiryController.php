<?php

namespace App\Http\Controllers;

use App\Mail\ContactEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;
use Throwable;

class ContactEnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $details = $request->validate([
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['nullable', 'string', 'max:30', 'regex:/^[+0-9().\-\s]{7,30}$/'],
            'interest' => ['required', Rule::in(['Renting', 'Letting', 'Property management', 'Guaranteed rent', 'Other'])],
            'message' => ['required', 'string', 'max:3000'],
        ]);

        try {
            Mail::to(config('services.contact_enquiry.to'))->send(new ContactEnquiry($details));
        } catch (Throwable $exception) {
            report($exception);

            return redirect()->route('contact')->withInput()->withErrors([
                'contact' => 'We could not send your enquiry right now. Please try again later or contact us by phone or WhatsApp.',
            ]);
        }

        return redirect()->route('contact')->with('success', 'Thank you. Your enquiry has been sent to our team.');
    }
}
