@extends('layouts.admin')
@section('title', 'Test email')
@section('heading', 'Test email')
@section('content')
    <section class="panel">
        <h1>Test email delivery</h1>
        <p>Send a test email to <strong>{{ $recipient }}</strong> using this server's configured mail service.</p>
        <p>Subject: <strong>Gani Laravel mail test</strong></p>
        <p>Environment: {{ app()->environment() }} · Mailer: {{ config('mail.default') }}</p>
        <p>From: {{ config('mail.from.address') }}</p>
        <form method="post" action="{{ route('test-email.send') }}">
            @csrf
            <button class="button" type="submit">Send test email</button>
        </form>
        <p>One test per minute. Check the inbox and spam folder after sending.</p>
    </section>
@endsection
