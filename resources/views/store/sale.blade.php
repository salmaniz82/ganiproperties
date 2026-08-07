@extends('layouts.store')
@section('title', 'Properties for sale | Gani Property Services')
@php
    $activePage = 'buy';
    $propertyCount = $properties->count();
@endphp
@section('content')
<main id="top">
    <section class="listing-masthead listing-masthead-buy">
        <div>
            <nav class="breadcrumbs breadcrumbs-dark" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>Buy</span></nav>
            <p class="eyebrow">PROPERTY FOR SALE</p>
            <h1>Properties for sale</h1>
            <p>Explore residential and commercial property for sale with practical local guidance from our team.</p>
        </div>
    </section>

    <section class="listing-filter-wrap" aria-label="Filter properties for sale">
        <form class="listing-filter" action="{{ route('buy') }}" method="get">
            <div class="listing-section-field"><span>Section</span><strong>Buy</strong></div>
            <label><span>Location</span><select name="location"><option value="">Any location</option>@foreach($locations as $location)<option value="{{ $location }}" @selected($filters['location'] === $location)>{{ $location }}</option>@endforeach</select></label>
            <label><span>Property type</span><select name="type"><option value="">Any type</option>@foreach($types as $type)<option value="{{ $type }}" @selected($filters['type'] === $type)>{{ $type }}</option>@endforeach</select></label>
            <label><span>Min bedrooms</span><select name="bedrooms"><option value="">Any</option>@foreach([1, 2, 3, 4] as $bedrooms)<option value="{{ $bedrooms }}" @selected($filters['bedrooms'] === (string) $bedrooms)>{{ $bedrooms }}{{ $bedrooms === 4 ? '+' : '' }}</option>@endforeach</select></label>
            <label><span>Max price</span><select name="max_price"><option value="">No maximum</option>@foreach(['350000' => '&pound;350,000', '550000' => '&pound;550,000', '750000' => '&pound;750,000', '1000000' => '&pound;1,000,000'] as $value => $label)<option value="{{ $value }}" @selected($filters['max_price'] === $value)>{!! $label !!}</option>@endforeach</select></label>
            <button class="button" type="submit">Update results</button>
        </form>
    </section>

    <section class="property-results section">
        <div class="results-toolbar">
            <div><strong>{{ $propertyCount }}</strong> {{ Str::plural('property', $propertyCount) }} found @if($filters['location'] !== '') in {{ $filters['location'] }} @endif</div>
            <form action="{{ route('buy') }}" method="get">
                @foreach($filters as $name => $value) @continue($name === 'sort') @if($value !== '')<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif @endforeach
                <label>Sort by <select name="sort" onchange="this.form.submit()"><option value="newest" @selected($filters['sort'] === 'newest')>Recently added</option><option value="price-low" @selected($filters['sort'] === 'price-low')>Price: low to high</option><option value="price-high" @selected($filters['sort'] === 'price-high')>Price: high to low</option><option value="bedrooms" @selected($filters['sort'] === 'bedrooms')>Most bedrooms</option></select></label>
            </form>
        </div>
        @if($properties->isEmpty())
            <div class="no-results"><span><svg><use href="#icon-pin"/></svg></span><h2>No exact matches yet</h2><p>Try widening your search or speak with our team about properties coming to market.</p><a class="button" href="{{ route('buy') }}">Clear all filters</a></div>
        @else
            <div class="listing-results-grid">
                @foreach($properties as $property)
                    <article class="property-card listing-card">
                        <a class="property-image" href="{{ route('property.show', $property['slug']) }}"><img src="{{ $property['image'] }}" alt="{{ $property['title'] }} in {{ $property['area'] }}" loading="lazy"><span class="property-status">{{ strtoupper($property['status']) }}</span></a>
                        <div class="property-info"><p class="property-type-label">{{ $property->is_commercial ? 'Commercial · ' : '' }}{{ $property['type'] }}</p><h3><a href="{{ route('property.show', $property['slug']) }}">{{ $property['title'] }}</a></h3><p>{{ $property['area'] }}, {{ $property['postcode'] }}</p><div class="details">@if($property['bedrooms'] > 0)<span><svg><use href="#icon-bed"/></svg>{{ $property['bedrooms'] }} bed</span>@endif<span><svg><use href="#icon-bath"/></svg>{{ $property['bathrooms'] }} bath</span>@if($property['receptions'] > 0)<span><svg><use href="#icon-sofa"/></svg>{{ $property['receptions'] }} reception</span>@endif</div><p class="listing-summary">{{ $property['summary'] }}</p><div class="price"><strong>{!! $property['price_label'] !!}</strong><a class="card-arrow" href="{{ route('property.show', $property['slug']) }}" aria-label="View {{ $property['title'] }}">&#8594;</a></div></div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>
    <section class="listing-help"><div><p class="eyebrow eyebrow-light">LOOKING TO BUY OR SELL?</p><h2>Plan your next property move</h2><p>Speak with our team about current opportunities, valuations and properties coming to market.</p></div><a class="button button-white" href="{{ route('contact') }}">Contact us</a></section>
</main>
@endsection
