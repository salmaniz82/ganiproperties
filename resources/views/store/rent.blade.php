@extends('layouts.store')
@section('title', 'Properties to rent | Gani Property Services')
@php
    $activePage = 'rent';
    $propertyCount = $properties->count();
@endphp
@section('content')
<main id="top">
    <section class="listing-masthead listing-masthead-rent">
        <div>
            <nav class="breadcrumbs breadcrumbs-dark" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>Rent</span></nav>
            <p class="eyebrow">PROPERTY TO RENT</p>
            <h1>Properties to rent</h1>
            <p>Explore our latest rental listings with responsive guidance from our lettings team.</p>
        </div>
    </section>

    <section class="listing-filter-wrap" aria-label="Filter properties">
        <form class="listing-filter" action="{{ route('rent') }}" method="get">
            <label><span>Section</span><select aria-label="Current property section" disabled><option>Rent</option></select></label>
            <label><span>Location</span><select name="location"><option value="">Any location</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}" @selected($filters['location'] === $location)>{{ $location }}</option>
                @endforeach
            </select></label>
            <label><span>Property type</span><select name="type"><option value="">Any type</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ $type }}</option>
                @endforeach
            </select></label>
            <label><span>Min bedrooms</span><select name="bedrooms"><option value="">Any</option>
                @foreach([1, 2, 3, 4] as $bedrooms)
                    <option value="{{ $bedrooms }}" @selected($filters['bedrooms'] === (string) $bedrooms)>{{ $bedrooms }}{{ $bedrooms === 4 ? '+' : '' }}</option>
                @endforeach
            </select></label>
            <label><span>Max price</span><select name="max_price"><option value="">No maximum</option>
                @foreach(['1800' => '&pound;1,800 pcm', '2500' => '&pound;2,500 pcm', '3500' => '&pound;3,500 pcm'] as $value => $label)
                    <option value="{{ $value }}" @selected($filters['max_price'] === $value)>{!! $label !!}</option>
                @endforeach
            </select></label>
            <button class="button" type="submit">Update results</button>
        </form>
    </section>

    <section class="property-results section">
        <div class="results-toolbar">
            <div><strong>{{ $propertyCount }}</strong> {{ Str::plural('property', $propertyCount) }} found @if($filters['location'] !== '') in {{ $filters['location'] }} @endif</div>
            <form action="{{ route('rent') }}" method="get">
                @foreach($filters as $name => $value)
                    @continue($name === 'sort')
                    @if($value !== '')<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
                @endforeach
                <label>Sort by <select name="sort" onchange="this.form.submit()">
                    <option value="newest" @selected($filters['sort'] === 'newest')>Recently added</option>
                    <option value="price-low" @selected($filters['sort'] === 'price-low')>Price: low to high</option>
                    <option value="price-high" @selected($filters['sort'] === 'price-high')>Price: high to low</option>
                    <option value="bedrooms" @selected($filters['sort'] === 'bedrooms')>Most bedrooms</option>
                </select></label>
            </form>
        </div>
        @if($properties->isEmpty())
            <div class="no-results"><span><svg><use href="#icon-pin"/></svg></span><h2>No exact matches yet</h2><p>Try widening your search or speak with our team about properties coming to market.</p><a class="button" href="{{ route('rent') }}">Clear all filters</a></div>
        @else
        <div class="listing-results-grid">
            @foreach($properties as $property)
                <article class="property-card listing-card">
                    <a class="property-image" href="{{ route('property.show', $property['slug']) }}"><img src="{{ $property['image'] }}" alt="{{ $property['title'] }} in {{ $property['area'] }}" loading="lazy"><span class="property-status">{{ strtoupper($property['status']) }}</span></a>
                    <div class="property-info">
                        <p class="property-type-label">{{ $property['type'] }}</p>
                        <h3><a href="{{ route('property.show', $property['slug']) }}">{{ $property['title'] }}</a></h3>
                        <p>{{ $property['area'] }}, {{ $property['postcode'] }}</p>
                        <div class="details">
                            @if($property['bedrooms'] > 0)<span><svg><use href="#icon-bed"/></svg>{{ $property['bedrooms'] }} bed</span>@endif
                            <span><svg><use href="#icon-bath"/></svg>{{ $property['bathrooms'] }} bath</span>
                            @if($property['receptions'] > 0)<span><svg><use href="#icon-sofa"/></svg>{{ $property['receptions'] }} reception</span>@endif
                        </div>
                        <p class="listing-summary">{{ $property['summary'] }}</p>
                        <div class="price"><strong>{!! $property['price_label'] !!}</strong><a class="card-arrow" href="{{ route('property.show', $property['slug']) }}" aria-label="View {{ $property['title'] }}">&#8594;</a></div>
                    </div>
                </article>
            @endforeach
        </div>
        @endif
    </section>

    <section class="listing-help">
        <div><p class="eyebrow eyebrow-light">CAN'T FIND THE RIGHT ONE?</p><h2>Searching for the right rental?</h2><p>Tell us what you need and we will keep you updated with suitable homes and availability.</p></div>
        <a class="button button-white" href="{{ route('contact') }}">Register with us</a>
    </section>
</main>
@endsection
