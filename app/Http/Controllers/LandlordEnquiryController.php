<?php

namespace App\Http\Controllers;

use App\Mail\LandlordEnquiry;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Throwable;

class LandlordEnquiryController extends Controller
{
    public function store(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:254'],
            'phone' => ['required', 'string', 'max:30', 'regex:/^[+0-9().\-\s]{7,30}$/'],
            'address' => ['required', 'string', 'max:500'],
            'property_type' => ['required', Rule::in(['Flat / apartment', 'House', 'Commercial property', 'Other'])],
        ]);
        if ($validator->fails()) {
            return redirect()->to(route('landlords').'#landlord-enquiry')
                ->withErrors($validator)
                ->withInput();
        }
        $details = $validator->validated();

        try {
            Mail::to(config('services.landlord_enquiry.to'))->send(new LandlordEnquiry($details));
        } catch (Throwable $exception) {
            Log::error('Landlord enquiry email could not be sent.', ['exception' => $exception::class]);

            return redirect()->to(route('landlords').'#landlord-enquiry')->withInput()->withErrors([
                'landlord_enquiry' => 'We could not send your enquiry right now. Please try again later.',
            ]);
        }

        return redirect()->to(route('landlords').'#landlord-enquiry')
            ->with('landlord_enquiry_sent', 'Thank you. Your property enquiry has been recorded.');
    }
}
