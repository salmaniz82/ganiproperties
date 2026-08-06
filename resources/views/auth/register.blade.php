@extends('layouts.store')
@section('title','Register')
@section('content')
<section class="section auth-single">
    <form method="post" action="{{ route('register') }}" class="panel stack auth-card">
        @csrf
        <div><small>NEW CUSTOMER</small><h1>Create account</h1><p>Register as a customer to track orders and save wishlist items.</p></div>
        <label>Name<input name="name" value="{{ old('name') }}" required></label>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Phone<input name="phone" value="{{ old('phone') }}"></label>
        <label>Password<input type="password" name="password" required></label>
        <label>Confirm password<input type="password" name="password_confirmation" required></label>
        <button class="button">Register</button>
        <p class="auth-switch">Already have an account? <a href="{{ route('login') }}">Login</a></p>
    </form>
</section>
@endsection
