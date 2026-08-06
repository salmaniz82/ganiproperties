<!doctype html><html lang="en"><head>
<meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<meta name="csrf-token" content="{{ csrf_token() }}">
@yield('meta')
@stack('structured-data')
<title>@yield('title','Party Poppers')</title><link rel="preconnect" href="https://fonts.googleapis.com"><link rel="preconnect" href="https://fonts.gstatic.com" crossorigin><link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Poppins:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<link rel="stylesheet" href="{{ asset('css/store.css') }}">
</head><body>
<div class="topbar">Same Day Delivery in Lahore, Karachi & Islamabad <span>100% Premium Quality</span><span>Secure Packaging</span><span>+92 300 1234567</span></div>
<header><a href="{{ route('home') }}"><img src="{{ asset('images/logo.jpg') }}" alt="Party Poppers"></a>
<form action="{{ route('shop') }}" class="search"><input name="q" value="{{ request('q') }}" placeholder="Search balloons, gifts, cakes..."><button>Search</button></form>
@php($headerCart = app(\App\Services\CartService::class)->summary(request()))
<nav>@auth <a href="{{ route('account') }}">Account</a>@else<a href="{{ route('login') }}">Login</a>@endauth <a href="{{ route('track') }}">Track Order</a><a class="cart-link js-cart-trigger" href="{{ route('cart') }}" aria-controls="quickCart" aria-expanded="false">Cart <span data-cart-count>{{ $headerCart['item_count'] }}</span></a></nav></header>
<div class="nav"><a href="{{ route('home') }}">Home</a><a href="{{ route('shop') }}">Shop all</a><a href="{{ route('category.show','birthday-decorations') }}">Birthday Decorations</a><a href="{{ route('category.show','balloons') }}">Balloons</a><a href="{{ route('category.show','flowers') }}">Flowers</a><a href="{{ route('track') }}">Track Order</a></div>
@if(session('success'))<div class="flash">{{ session('success') }}</div>@endif
@if($errors->any())<div class="flash error">{{ $errors->first() }}</div>@endif
<main>@yield('content')</main>
<footer class="site-footer" id="footer">
    <div class="footer-clouds" aria-hidden="true"></div>
    <div class="footer-container footer-grid">
        <div class="footer-about">
            <img class="footer-logo" src="{{ asset('images/footer-logo-transparent.png') }}" alt="Party Poppers Decoration Service">
            <p>We make every celebration special with beautiful decorations, gifts, flowers, balloons and much more.</p>
            <div class="footer-socials" aria-label="Social links"><a href="#" aria-label="Facebook">f</a><a href="#" aria-label="Instagram">ig</a><a href="#" aria-label="WhatsApp">wa</a><a href="#" aria-label="Pinterest">p</a></div>
        </div>
        <div class="footer-links">
            <h3>Quick Links</h3>
            <a href="{{ route('pages.show','about') }}">About Us</a><a href="{{ route('pages.show','services') }}">Services</a><a href="{{ route('pages.show','events') }}">Events</a><a href="{{ route('shop') }}">Our Products</a><a href="{{ route('pages.show','faq') }}">FAQ</a><a href="{{ route('track') }}">Track Order</a><a href="{{ route('account') }}">My Account</a>
        </div>
        <div class="footer-links">
            <h3>Top Categories</h3>
            <a href="{{ route('category.show','birthday-decorations') }}">Birthday Decorations</a><a href="{{ route('category.show','balloons') }}">Balloons</a><a href="{{ route('category.show','gift-baskets') }}">Gift Baskets</a><a href="{{ route('category.show','flowers') }}">Flower Bouquets</a><a href="{{ route('category.show','chocolate-bouquets') }}">Chocolate Bouquets</a>
        </div>
        <div class="footer-subscribe">
            <h3>Subscribe Us</h3><p>Subscribe to get special offers, discounts and updates.</p>
            <form action="{{ route('home') }}" method="get"><input type="email" name="newsletter_email" placeholder="Enter your email" aria-label="Email address"><button type="submit">Subscribe</button></form>
            <div class="footer-policy-links"><a href="{{ route('pages.show','privacy-policy') }}">Privacy Policy</a><a href="{{ route('pages.show','terms') }}">Terms &amp; Conditions</a><a href="{{ route('pages.show','refund-policy') }}">Refund Policy</a><a href="{{ route('pages.show','return-policy') }}">Return Policy</a></div>
        </div>
        <img class="footer-mascot" src="{{ asset('images/footer-mascot-transparent.png') }}" alt="">
    </div>
    <div class="footer-bottom">
        <div class="footer-container footer-bottom__inner"><span>© {{ date('Y') }} Party Poppers Decoration Service. All Rights Reserved.</span><img class="footer-payments" src="{{ asset('images/footer-payment-logos.png') }}" alt="Accepted payment methods"></div>
    </div>
</footer>
<aside class="quick-cart" id="quickCart" aria-hidden="true">
    <div class="quick-cart__header"><div><small>YOUR CART</small><h3>Shopping Cart (<span data-drawer-count>{{ $headerCart['item_count'] }}</span>)</h3></div><button class="quick-cart__close" type="button" aria-label="Close cart">×</button></div>
    <div class="quick-cart__message" data-cart-message hidden></div>
    <div class="quick-cart__body" data-cart-items>
        @forelse($headerCart['items'] as $item)
            <article class="quick-cart__item" data-cart-key="{{ $item['key'] }}" data-update-url="{{ route('cart.update',$item['key']) }}" data-remove-url="{{ route('cart.remove',$item['key']) }}" data-quantity="{{ $item['quantity'] }}" data-max="{{ $item['product']->track_inventory ? $item['product']->available_stock : 99 }}">
                <a href="{{ route('products.show',$item['product']) }}"><img src="{{ asset($item['product']->thumbnail_image) }}" alt="{{ $item['product']->name }}"></a>
                <div><a href="{{ route('products.show',$item['product']) }}"><h4>{{ $item['product']->name }}</h4></a><p>PKR {{ number_format($item['unit_price']) }}</p><div class="quick-cart__quantity"><button data-drawer-quantity="-1" type="button">−</button><span>{{ $item['quantity'] }}</span><button data-drawer-quantity="1" type="button">+</button></div></div>
                <div class="quick-cart__line"><strong>PKR {{ number_format($item['line_total']) }}</strong><button data-drawer-remove type="button" aria-label="Remove item">×</button></div>
            </article>
        @empty
            <div class="quick-cart__empty">Your cart is currently empty.</div>
        @endforelse
    </div>
    <div class="quick-cart__footer"><div><span>Subtotal</span><strong data-cart-subtotal>PKR {{ number_format($headerCart['subtotal']) }}</strong></div><small>Delivery is calculated at checkout.</small><a class="button" href="{{ route('checkout') }}">Proceed to checkout</a><a href="{{ route('cart') }}">View full cart</a></div>
</aside>
<div class="quick-cart-overlay" data-cart-overlay></div>
<script src="{{ asset('js/store-cart.js') }}" defer></script>
</body></html>
