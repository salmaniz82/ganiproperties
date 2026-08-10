<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <title>Login | Gani Property Services</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/login.css') }}">
</head>
<body>
    <main class="login-page">
        <section class="login-panel" aria-labelledby="login-title">
            <a class="login-logo" href="{{ route('home') }}" aria-label="Gani Property Services home">
                <img src="{{ asset('images/Gani-Logo.svg') }}" alt="Gani Property Services">
            </a>

            <div class="login-heading">
                <h1 id="login-title">Sign in</h1>
                <p>Enter your details to access the dashboard.</p>
            </div>

            @if($errors->any())
                <div class="login-error" role="alert">{{ $errors->first() }}</div>
            @endif

            <form method="post" action="{{ route('login.store') }}" class="login-form">
                @csrf
                <label>
                    <span>Email address</span>
                    <input type="email" name="email" value="{{ old('email', 'app@ganiproperties.co.uk') }}" autocomplete="email" required autofocus>
                </label>
                <label>
                    <span>Password</span>
                    <input type="password" name="password" autocomplete="current-password" required>
                </label>
                <label class="remember-field">
                    <input type="checkbox" name="remember" value="1">
                    <span>Remember me</span>
                </label>
                <button type="submit">Login</button>
            </form>
        </section>
    </main>
</body>
</html>
