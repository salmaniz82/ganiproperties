@extends('layouts.store')
@section('title', 'Party Poppers Decoration Service')
@section('content')
@php
    $categoryUrl = fn (string $slug) => isset($categoryMap[$slug]) ? route('category.show', $categoryMap[$slug]) : route('shop');
@endphp

<section class="landing-hero" aria-label="Celebration collection">
    <div class="landing-hero__panel">
        <div class="landing-container">
            <div class="landing-hero__copy">
                <p>Make Every</p>
                <h1><span>Celebration</span> Memorable</h1>
                <ul>
                    <li>Birthday Supplies</li>
                    <li>Gift Baskets</li>
                    <li>Flower Bouquets</li>
                    <li>Balloons & Chocolate Bouquets</li>
                </ul>
                <a class="landing-btn" href="{{ route('shop') }}">Shop Now</a>
            </div>
        </div>
    </div>
</section>

<section class="landing-section landing-curated">
    <div class="landing-container">
        <div class="landing-head">
            <h2><span>★</span>Bestsellers You'll Love<span class="pink">✣</span></h2>
            <a href="{{ route('shop') }}">View All</a>
        </div>
        <div class="landing-bestsellers">
            @foreach($featured as $product)
                <article class="landing-store-card">
                    @if($loop->first)<span class="landing-badge">Best Seller</span>@endif
                    <a href="{{ route('products.show', $product) }}"><img src="{{ asset($product->thumbnail_image) }}" alt="{{ $product->name }}" loading="lazy"></a>
                    <div>
                        <a href="{{ route('products.show', $product) }}"><h3>{{ $product->name }}</h3></a>
                        <p>{{ $product->category?->name }}</p>
                        <strong>{{ $product->formatted_price }}</strong>
                        <form class="js-ajax-add-cart" method="post" action="{{ route('cart.add', $product) }}">@csrf<input type="hidden" name="quantity" value="1"><button aria-label="Add {{ $product->name }} to cart">+</button></form>
                    </div>
                </article>
            @endforeach
        </div>
    </div>
</section>

<section class="landing-section landing-categories" aria-labelledby="category-title">
    <div class="landing-container">
        <h2 id="category-title" class="landing-title">Shop By <span>Category</span></h2>
        <div class="landing-category-grid">
            <a class="landing-category-card category-birthday tone-pink" href="{{ $categoryUrl('birthday-decorations') }}"><div class="landing-card-copy"><h3>Birthday Decorations</h3><p>Make Your Party Extra Special</p><span class="landing-btn">Explore</span></div></a>
            <a class="landing-category-card category-balloons tone-blue" href="{{ $categoryUrl('balloons') }}"><div class="landing-card-copy"><h3>Balloons</h3><p>Brighten Every Moment</p><span class="landing-btn blue">Shop Now</span></div></a>
            <a class="landing-category-card category-basket tone-blush" href="{{ $categoryUrl('gift-baskets') }}"><div class="landing-card-copy"><h3>Gift Baskets</h3><p>Happiness in a Basket</p><span class="landing-btn">Shop Now</span></div></a>
            <a class="landing-category-card category-flowers tone-green" href="{{ $categoryUrl('flowers') }}"><div class="landing-card-copy"><h3>Flower Bouquets</h3><p>Fresh Flowers, Lasting Smiles</p><span class="landing-btn green">Shop Now</span></div></a>
            <a class="landing-category-card category-chocolate tone-warm" href="{{ $categoryUrl('chocolate-bouquets') }}"><div class="landing-card-copy"><h3>Chocolate Bouquets</h3><p>Sweetness Wrapped with Love</p><span class="landing-btn brown">Shop Now</span></div></a>
            <a class="landing-category-card category-cake tone-blue" href="{{ $categoryUrl('cakes') }}"><div class="landing-card-copy"><h3>Cakes & Sweet Treats</h3><p>Delicious Creations for Every Occasion</p><span class="landing-btn blue">Shop Now</span></div></a>
            <a class="landing-category-card category-party tone-pink" href="{{ $categoryUrl('party-supplies') }}"><div class="landing-card-copy"><h3>Party Supplies</h3><p>Everything You Need to Party</p><span class="landing-btn">Explore</span></div></a>
            <a class="landing-category-card category-occasion tone-green" href="{{ route('shop') }}"><div class="landing-card-copy"><h3>Occasion Based Gifts</h3><p>Gifts for Every Occasion</p><span class="landing-btn green">Shop Now</span></div></a>
            <a class="landing-category-card category-custom tone-blue" href="{{ $categoryUrl('customized-items') }}"><div class="landing-card-copy"><h3>Customized Items</h3><p>Make It Personal, Make It Special</p><span class="landing-btn blue">Shop Now</span></div></a>
        </div>
    </div>
</section>

@if($birthdayProducts->isNotEmpty())
<section class="landing-section landing-showcase">
    <div class="landing-container">
        <div class="landing-section-head"><h2>Birthday Decorations</h2><a href="{{ $categoryUrl('birthday-decorations') }}">View All</a></div>
        <div class="landing-product-row">
            @foreach($birthdayProducts as $product)
                <a class="landing-product-card {{ $loop->iteration === 3 ? 'featured' : '' }}" href="{{ route('products.show', $product) }}"><img src="{{ asset($product->thumbnail_image) }}" alt="{{ $product->name }}" loading="lazy"><h3>{{ $product->name }}</h3><p>{{ $product->formatted_price }}</p></a>
            @endforeach
        </div>
    </div>
</section>
@endif

<section class="landing-section landing-compact-products">
    <div class="landing-container landing-product-columns">
        @foreach([['Flower Bouquets', $flowerProducts, 'flowers', 'blue-title'], ['Chocolate Bouquets', $chocolateProducts, 'chocolate-bouquets', 'warm-title'], ['Cakes & Sweet Treats', $cakeProducts, 'cakes', 'blue-title']] as [$title, $items, $slug, $tone])
            <div>
                <div class="landing-section-head compact {{ $tone }}"><h2>{{ $title }}</h2><a href="{{ $categoryUrl($slug) }}">View All</a></div>
                <div class="landing-mini-grid">
                    @foreach($items as $product)<a class="landing-product-card" href="{{ route('products.show', $product) }}"><img src="{{ asset($product->thumbnail_image) }}" alt="{{ $product->name }}" loading="lazy"><h3>{{ $product->name }}</h3><p>{{ $product->formatted_price }}</p></a>@endforeach
                </div>
            </div>
        @endforeach
    </div>
</section>

<section class="landing-section landing-promos">
    <div class="landing-container landing-promo-grid">
        <a href="{{ route('shop') }}"><img src="{{ asset('images/promo-combo.jpg') }}" alt="Best offers"></a>
        <a href="{{ route('shop') }}"><img src="{{ asset('images/promo-delivery.jpg') }}" alt="Same day delivery"></a>
        <a href="{{ $categoryUrl('customized-items') }}"><img src="{{ asset('images/promo-custom.jpg') }}" alt="Custom creations"></a>
    </div>
</section>

<section class="landing-section landing-why">
    <div class="landing-container">
        <h2 class="landing-title">Why Choose <span>Party Poppers?</span></h2>
        <div class="landing-why-grid">
            <div><span>🎁</span><h3>Wide Range of Products</h3><p>Everything for Every Occasion</p></div>
            <div><span>🏷</span><h3>Affordable Prices</h3><p>Best Value for Money</p></div>
            <div><span>💌</span><h3>Custom & Personalization</h3><p>Make it Special</p></div>
            <div><span>🚚</span><h3>On-Time Delivery</h3><p>Guaranteed On-Time</p></div>
            <div><span>🎧</span><h3>24/7 Customer Support</h3><p>We're Here to Help</p></div>
        </div>
    </div>
</section>
@endsection
