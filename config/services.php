<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Third Party Services
    |--------------------------------------------------------------------------
    |
    | This file is for storing the credentials for third party services such
    | as Mailgun, Postmark, AWS and more. This file provides the de facto
    | location for this type of information, allowing packages to have
    | a conventional file to locate the various service credentials.
    |
    */

    'postmark' => [
        'key' => env('POSTMARK_API_KEY'),
    ],

    'resend' => [
        'key' => env('RESEND_API_KEY'),
    ],

    'mailchimp' => [
        'app' => env('MAILCHIMP_APP', 'Gani Property Services'),
        'key' => env('MAILCHIMP_API_KEY'),
        'audience_id' => env('MAILCHIMP_AUDIENCE_ID'),
        'ca_bundle' => env('MAILCHIMP_CA_BUNDLE'),
    ],

    'landlord_enquiry' => [
        'to' => env('LANDLORD_ENQUIRY_EMAIL', 'hello@ganipropertyservices.co.uk'),
    ],

    'ses' => [
        'key' => env('AWS_ACCESS_KEY_ID'),
        'secret' => env('AWS_SECRET_ACCESS_KEY'),
        'region' => env('AWS_DEFAULT_REGION', 'us-east-1'),
    ],

    'slack' => [
        'notifications' => [
            'bot_user_oauth_token' => env('SLACK_BOT_USER_OAUTH_TOKEN'),
            'channel' => env('SLACK_BOT_USER_DEFAULT_CHANNEL'),
        ],
    ],

];
