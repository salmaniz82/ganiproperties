@extends('layouts.store')
@section('title','Login')
@section('content')
<section class="section auth-single">
    <form method="post" action="{{ route('login.store') }}" class="panel stack auth-card">
        @csrf
        <div><small>WELCOME BACK</small><h1>Login</h1><p>Access your account, orders, and wishlist.</p></div>
        <label>Email<input type="email" name="email" value="{{ old('email') }}" required></label>
        <label>Password<input type="password" name="password" required></label>
        <label class="auth-check"><input type="checkbox" name="remember" value="1"> Remember me</label>
        <button class="button">Login</button>
        <p class="auth-switch">Don't have an account? <a href="{{ route('register.show') }}">Create account</a></p>
    </form>
</section>
@endsection
