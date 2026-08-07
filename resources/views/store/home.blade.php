@extends('layouts.store')
@section('title', 'Gani Property Services | Balham Estate Agents')
@php
    $activePage = 'home';
@endphp
@section('content')
<main id="top">
    <section class="hero">
        <div class="hero-shade"></div>
        <div class="hero-content">
            <p class="eyebrow eyebrow-light">INDEPENDENT. LOCAL. BALHAM.</p>
            <h1>Local property people,<br>doing right by Balham</h1>
            <p class="hero-copy">Lettings, guaranteed rent and property management<br class="desktop-only"> from an independent team on Balham High Road.</p>
            <div class="hero-buttons">
                <a class="button" href="{{ route('rent') }}">Search rentals</a>
                <a class="button button-outline" href="{{ route('contact') }}">Book a free valuation</a>
            </div>
        </div>
    </section>

    <form class="property-search" id="search" action="{{ route('rent') }}" method="get">
        <fieldset class="search-tabs">
            <legend class="visually-hidden">Listing type</legend>
            <label><input type="radio" name="intent" value="rent" checked> <span>Rent</span></label>
        </fieldset>
        <label class="search-field"><span>Location</span><select name="location"><option value="">Any</option><option>Balham</option><option>Tooting</option><option>Streatham</option></select></label>
        <label class="search-field"><span>Property type</span><select name="type"><option value="">Any</option><option>Apartment</option><option>Maisonette</option><option>Terraced house</option></select></label>
        <label class="search-field"><span>Bedrooms</span><select name="bedrooms"><option value="">Any</option><option value="1">1+</option><option value="2">2+</option><option value="3">3+</option></select></label>
        <label class="search-field"><span>Max price</span><select name="max_price"><option value="">Any</option><option value="1800">&pound;1,800 pcm</option><option value="2500">&pound;2,500 pcm</option><option value="3500">&pound;3,500 pcm</option></select></label>
        <button class="button search-button" type="submit">Search</button>
    </form>

    <section class="section listings" id="listings">
        <div class="section-heading"><h2>Recently added rentals</h2><a href="{{ route('rent') }}">View all rentals &#8594;</a></div>
        <div class="property-grid">
            @forelse($featuredProperties as $property)
                <article class="property-card">
                    <a class="property-image" href="{{ route('property.show', $property['slug']) }}">
                        <img src="{{ $property['image'] }}" alt="{{ $property['title'] }} property" loading="lazy">
                        <span class="property-status">{{ strtoupper($property['status']) }}</span>
                    </a>
                    <div class="property-info">
                        <h3><a href="{{ route('property.show', $property['slug']) }}">{{ $property['title'] }}</a></h3>
                        <p>{{ $property['area'] }}, {{ $property['postcode'] }}</p>
                        <div class="details">
                            @if($property['bedrooms'] > 0)<span><svg><use href="#icon-bed"/></svg>{{ $property['bedrooms'] }}</span>@endif
                            <span><svg><use href="#icon-bath"/></svg>{{ $property['bathrooms'] }}</span>
                            @if($property['receptions'] > 0)<span><svg><use href="#icon-sofa"/></svg>{{ $property['receptions'] }}</span>@endif
                        </div>
                        <div class="price"><strong>{!! $property['price_label'] !!}</strong><a class="card-arrow" href="{{ route('property.show', $property['slug']) }}" aria-label="View {{ $property['title'] }}">&#8594;</a></div>
                    </div>
                </article>
            @empty
                <p>No rental properties are currently available. Please check back soon.</p>
            @endforelse
        </div>
    </section>

    <section class="reasons section">
        <h2>Why choose Gani?</h2>
        <div class="reason-grid">
            <article><span class="reason-icon"><svg><use href="#icon-pin"/></svg></span><h3>Local expertise</h3><p>We live and work on Balham High Road. We know the area inside and out.</p></article>
            <article><span class="reason-icon"><svg><use href="#icon-chat"/></svg></span><h3>Personal service</h3><p>You will deal with knowledgeable people who care about your move as much as you do.</p></article>
            <article><span class="reason-icon"><svg><use href="#icon-shield"/></svg></span><h3>Trusted advice</h3><p>Straightforward guidance for lettings, management and guaranteed rent.</p></article>
            <article><span class="reason-icon"><svg><use href="#icon-key"/></svg></span><h3>Proven results</h3><p>Strong local marketing, great presentation and practical negotiation get the best outcome.</p></article>
        </div>
    </section>

    <section class="about section" id="about">
        <div class="about-image"><img src="/assets/office-ref.jpg" alt="Gani independent estate agency on Balham High Road" loading="lazy"></div>
        <div class="about-copy">
            <p class="eyebrow">OUR HOME. YOUR NEIGHBOURHOOD.</p>
            <h2>Proudly independent on<br>Balham High Road</h2>
            <p>We have been part of the Balham community for years, helping landlords and tenants move with confidence.</p>
            <p>From Victorian terraces to modern apartments, we know the market and the people.</p>
            <ul><li><svg><use href="#icon-check"/></svg>Lettings</li><li><svg><use href="#icon-check"/></svg>Guaranteed rent</li><li><svg><use href="#icon-check"/></svg>Property management</li><li><svg><use href="#icon-check"/></svg>Landlord advice</li></ul>
        </div>
    </section>

    <section class="reviews section" id="reviews">
        <div class="section-heading"><h2>What our clients say</h2><a href="#reviews">View more reviews on Google &#8594;</a></div>
        <div class="review-grid">
            <blockquote><span>&ldquo;</span><p>Gani were excellent from start to finish. Great communication and a fantastic result on our letting. Highly recommend.</p><footer><strong>Sarah M.</strong><small>Balham, SW12</small></footer></blockquote>
            <blockquote><span>&ldquo;</span><p>The team are professional, friendly and always quick to respond. Our flat let within days.</p><footer><strong>James T.</strong><small>Tooting, SW17</small></footer></blockquote>
            <blockquote><span>&ldquo;</span><p>Honest advice and no hard sell. A refreshing experience with real local knowledge.</p><footer><strong>Priya S.</strong><small>Streatham, SW16</small></footer></blockquote>
        </div>
    </section>

    <section class="valuation" id="valuation">
        <div><h2>Your local property move starts here</h2><p>Book a free, no-obligation valuation with our Balham experts.</p></div>
        <a class="button button-white" href="{{ route('contact') }}">Book a free valuation</a>
        <a class="phone" href="tel:02086737778"><svg class="icon"><use href="#icon-phone"/></svg>020 8673 7778</a>
    </section>
</main>
@endsection
