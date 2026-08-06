@extends('layouts.store')
@section('title', 'Register | Gani Property Services')
@php($activePage = 'login')
@section('content')
<main id="top">
    <section class="auth-hero">
        <div class="auth-panel">
            <p class="eyebrow">CREATE ACCOUNT</p>
            <h1>Register your details</h1>
            <p>Save rental searches, keep track of enquiries and hear from the team about suitable homes.</p>
            <form method="post" action="{{ route('register') }}" class="contact-form auth-form">
                @csrf
                <label class="form-field"><span>Name</span><input name="name" value="{{ old('name') }}" autocomplete="name" required></label>
                <label class="form-field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                <label class="form-field"><span>Phone number</span><input name="phone" value="{{ old('phone') }}" autocomplete="tel"></label>
                <label class="form-field"><span>Password</span><input type="password" name="password" autocomplete="new-password" required></label>
                <label class="form-field"><span>Confirm password</span><input type="password" name="password_confirmation" autocomplete="new-password" required></label>
                <button class="button" type="submit">Create account</button>
            </form>
            <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Login</a></p>
        </div>
        <div class="auth-aside">
            <p class="eyebrow eyebrow-light">PROPERTY ALERTS</p>
            <h2>Tell us what you need and we will keep you close to new rentals.</h2>
            <a class="button button-white" href="{{ route('rent') }}">Browse rentals</a>
        </div>
    </section>
</main>
@endsection
