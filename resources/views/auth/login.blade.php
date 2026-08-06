@extends('layouts.store')
@section('title', 'Login | Gani Property Services')
@php($activePage = 'login')
@section('content')
<main id="top">
    <section class="auth-hero">
        <div class="auth-panel">
            <p class="eyebrow">CLIENT ACCESS</p>
            <h1>Welcome back</h1>
            <p>Access your account details, saved properties and enquiry history.</p>
            <form method="post" action="{{ route('login.store') }}" class="contact-form auth-form">
                @csrf
                <label class="form-field"><span>Email address</span><input type="email" name="email" value="{{ old('email') }}" autocomplete="email" required></label>
                <label class="form-field"><span>Password</span><input type="password" name="password" autocomplete="current-password" required></label>
                <label class="auth-check"><input type="checkbox" name="remember" value="1"> <span>Remember me</span></label>
                <button class="button" type="submit">Login</button>
            </form>
            <p class="auth-switch">New to Gani? <a href="{{ route('register.show') }}">Create account</a></p>
        </div>
        <div class="auth-aside">
            <p class="eyebrow eyebrow-light">LOCAL SUPPORT</p>
            <h2>Speak with the Balham team any time.</h2>
            <a class="button button-white" href="{{ route('contact') }}">Contact us</a>
        </div>
    </section>
</main>
@endsection
