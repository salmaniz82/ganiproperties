<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow, noarchive, nosnippet, noimageindex">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @yield('meta')
    @stack('structured-data')
    <title>@yield('title', 'Gani Property Services')</title>
    <link rel="stylesheet" href="{{ asset('styles.css') }}">
</head>
<body>
    <svg class="svg-sprite" aria-hidden="true" focusable="false">
        <symbol id="icon-phone" viewBox="0 0 24 24"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6A19.79 19.79 0 0 1 2.12 4.18 2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72c.13.96.36 1.9.69 2.8a2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45c.91.33 1.85.56 2.81.69A2 2 0 0 1 22 16.92Z"/></symbol>
        <symbol id="icon-bed" viewBox="0 0 24 24"><path d="M2 4v16M2 14h20v6M5 14V8h6a3 3 0 0 1 3 3v3M14 10h5a3 3 0 0 1 3 3v1"/></symbol>
        <symbol id="icon-bath" viewBox="0 0 24 24"><path d="M4 12h16v2a6 6 0 0 1-6 6h-4a6 6 0 0 1-6-6v-2ZM7 12V5a3 3 0 0 1 5.4-1.8L14 5M4 20l-1 2M20 20l1 2"/></symbol>
        <symbol id="icon-sofa" viewBox="0 0 24 24"><path d="M5 11V7a3 3 0 0 1 3-3h8a3 3 0 0 1 3 3v4M4 10a2 2 0 0 0-2 2v6h20v-6a2 2 0 0 0-4 0v2H6v-2a2 2 0 0 0-2-2ZM4 18v2M20 18v2"/></symbol>
        <symbol id="icon-heart" viewBox="0 0 24 24"><path d="M20.84 4.61a5.5 5.5 0 0 0-7.78 0L12 5.67l-1.06-1.06a5.5 5.5 0 0 0-7.78 7.78L12 21.23l8.84-8.84a5.5 5.5 0 0 0 0-7.78Z"/></symbol>
        <symbol id="icon-pin" viewBox="0 0 48 48"><path d="M36 20c0 9-12 21-12 21S12 29 12 20a12 12 0 1 1 24 0Z"/><circle cx="24" cy="20" r="4"/><path d="M8 38c-3 1-5 2-5 4 0 3 9 5 21 5s21-2 21-5c0-2-2-3-5-4"/></symbol>
        <symbol id="icon-chat" viewBox="0 0 48 48"><rect x="5" y="7" width="31" height="25" rx="6"/><path d="m12 32-3 7 9-7M18 17v1M29 17v1M17 24c3 3 7 3 10 0M29 35h8a6 6 0 0 0 6-6V16"/></symbol>
        <symbol id="icon-shield" viewBox="0 0 48 48"><path d="M24 4c5 4 10 6 16 7v12c0 11-7 18-16 22C15 41 8 34 8 23V11c6-1 11-3 16-7Z"/><path d="m24 15 2.2 4.5 5 .7-3.6 3.5.9 5-4.5-2.4-4.5 2.4.9-5-3.6-3.5 5-.7L24 15Z"/></symbol>
        <symbol id="icon-key" viewBox="0 0 48 48"><circle cx="31" cy="15" r="9"/><path d="m25 21-19 19M12 34l5 5M17 29l5 5M30 15h2"/></symbol>
        <symbol id="icon-check" viewBox="0 0 24 24"><circle cx="12" cy="12" r="9"/><path d="m8 12 2.5 2.5L16 9"/></symbol>
        <symbol id="icon-instagram" viewBox="0 0 24 24"><rect x="3" y="3" width="18" height="18" rx="5"/><circle cx="12" cy="12" r="4"/><path d="M17.5 6.5h.01"/></symbol>
        <symbol id="icon-whatsapp" viewBox="0 0 24 24"><path d="M20.5 11.5a8.5 8.5 0 0 1-12.4 7.5L3 20.5l1.5-5.1A8.5 8.5 0 1 1 20.5 11.5Z"/><path d="M8.5 7.5c-.6.2-1.1 1.1-1.1 1.8 0 2.2 3.1 5.4 5.5 6.1.9.3 1.7.1 2.3-.4l.7-1.1-2.4-1.1-1.1 1c-1.4-.6-2.5-1.7-3.2-3.1l.9-1.1-1.1-2.1-.5.1Z"/></symbol>
        <symbol id="icon-facebook" viewBox="0 0 24 24"><path d="M14 8h4V3h-4c-4 0-6 2-6 6v3H4v5h4v4h5v-4h4l1-5h-5V9c0-.7.3-1 1-1Z"/></symbol>
    </svg>

    @php($activePage = $activePage ?? '')
    <header class="site-header">
        <a class="brand" href="{{ route('home') }}" aria-label="Gani Property Services home">
            <img class="brand-logo" src="{{ asset('images/Gani-Logo.svg') }}" alt="Gani Property Services">
        </a>
        <nav class="desktop-nav" aria-label="Main navigation">
            <a href="{{ route('home') }}" @class(['is-active' => $activePage === 'home'])>Home</a>
            <a href="{{ route('rent') }}" @class(['is-active' => $activePage === 'rent'])>Rent</a>
            <a href="{{ route('landlords') }}" @class(['is-active' => $activePage === 'landlords'])>Landlords</a>
            <a href="{{ route('about') }}" @class(['is-active' => $activePage === 'about'])>About</a>
            <a href="{{ route('contact') }}" @class(['is-active' => $activePage === 'contact'])>Contact</a>
            
        </nav>
        <div class="header-actions">
            <a href="tel:02086737778"><svg class="icon"><use href="#icon-phone"/></svg>020 8673 7778</a>
            <a class="button button-small" href="{{ route('contact') }}">Book a free valuation</a>
        </div>
        <button class="menu-toggle" type="button" aria-expanded="false" aria-controls="mobile-navigation" aria-label="Open navigation">&#9776;</button>
        <nav class="mobile-nav" id="mobile-navigation" aria-label="Mobile navigation">
            <a href="{{ route('home') }}" @class(['is-active' => $activePage === 'home'])>Home</a>
            <a href="{{ route('rent') }}" @class(['is-active' => $activePage === 'rent'])>Rent</a>
            <a href="{{ route('landlords') }}" @class(['is-active' => $activePage === 'landlords'])>Landlords</a>
            <a href="{{ route('about') }}" @class(['is-active' => $activePage === 'about'])>About</a>
            <a href="{{ route('contact') }}" @class(['is-active' => $activePage === 'contact'])>Contact</a>
            
        </nav>
    </header>

    @if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
    @if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif

    @yield('content')

    <footer class="footer" id="contact-footer">
        <div class="footer-brand">
            <a class="brand brand-inverse" href="{{ route('home') }}"><strong>GANI</strong><span>PROPERTY SERVICES</span></a>
            <p>Independent estate agents on Balham High Road, covering Balham, Tooting, Streatham and surrounding areas in South London.</p>
            <div class="socials"><a href="https://www.instagram.com/ganipropertyservices/" aria-label="Gani Property Services on Instagram"><svg><use href="#icon-instagram"/></svg></a><a href="#" aria-label="Facebook"><svg><use href="#icon-facebook"/></svg></a><a href="#" class="google" aria-label="Google">G</a></div>
        </div>
        <div class="footer-column"><h3>Navigate</h3><a href="{{ route('rent') }}">Rent</a><a href="{{ route('buy') }}">Buy</a><a href="{{ route('commercial') }}">Commercial</a><a href="{{ route('landlords') }}">Landlords</a><a href="{{ route('pages.show', 'services') }}">Services</a><a href="{{ route('pages.show', 'events') }}">Events</a><a href="{{ route('about') }}">About</a><a href="{{ route('contact') }}">Contact</a></div>
        <div class="footer-column"><h3>Helpful links</h3><a href="{{ route('contact') }}">Book a free valuation</a><button class="footer-action" type="button" data-updates-open aria-haspopup="dialog" aria-controls="updates-dialog">Register for updates</button><a href="{{ route('about') }}#coverage">Area guides</a><a href="{{ route('pages.show', 'faq') }}">FAQ</a><a href="{{ route('pages.show', 'privacy-policy') }}">Privacy policy</a><a href="{{ route('pages.show', 'terms') }}">Terms &amp; conditions</a></div>
        <div class="footer-column"><h3>Contact</h3><p>142 Balham High Road<br>London SW12 9BW</p><a href="tel:02086737778">020 8673 7778</a><a href="mailto:hello@ganipropertyservices.co.uk">hello@ganipropertyservices.co.uk</a><p>Mon-Fri: 9am-6pm<br>Sat: 9am-4pm</p></div>
        <div class="copyright">&copy; GANI Property Services. All rights reserved <span id="year">{{ date('Y') }}</span></div>
    </footer>

    <dialog class="updates-dialog" id="updates-dialog" aria-labelledby="updates-title">
        <button class="updates-close" type="button" data-updates-close aria-label="Close registration form">&times;</button>
        <div data-updates-form-content>
            <p class="eyebrow">PROPERTY UPDATES</p>
            <h2 id="updates-title">Register for updates</h2>
            <p>Receive property news and updates from Gani Property Services.</p>
            <form action="{{ route('updates.register') }}" method="post" data-updates-form>
                @csrf
                <label>Name<input name="name" type="text" autocomplete="name" maxlength="100" required></label>
                <label>Email address<input name="email" type="email" autocomplete="email" maxlength="254" required></label>
                <label>Phone number<input name="phone" type="tel" autocomplete="tel" maxlength="30" required></label>
                <p class="updates-privacy">By registering, you agree to receive property updates by email. You can unsubscribe at any time. Read our <a href="{{ route('pages.show', 'privacy-policy') }}">privacy policy</a>.</p>
                <p class="updates-feedback" data-updates-feedback role="alert" hidden></p>
                <button class="button" type="submit">Register for updates</button>
            </form>
        </div>
        <div class="updates-success" data-updates-success role="status" hidden>
            <h2>Thank you</h2>
            <p data-updates-success-message></p>
        </div>
    </dialog>

    <a class="whatsapp-float" href="https://wa.me/447828454111" aria-label="Chat with Gani Property Services on WhatsApp" title="Chat on WhatsApp"><svg aria-hidden="true"><use href="#icon-whatsapp"/></svg></a>
    <div class="search-notice" role="status" aria-live="polite"></div>
    <script src="{{ asset('script.js') }}"></script>
</body>
</html>
