@extends('layouts.store')
@section('title', 'Commercial properties | Gani Property Services')
@php
    $activePage = 'commercial';
    $propertyCount = $properties->count();
@endphp
@section('content')
<main id="top">
    <section class="listing-masthead listing-masthead-commercial">
        <div>
            <nav class="breadcrumbs breadcrumbs-dark" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><span>Commercial</span></nav>
            <p class="eyebrow">COMMERCIAL PROPERTY</p>
            <h1>Commercial properties</h1>
            <p>Browse our latest commercial listings and investment opportunities with practical market guidance.</p>
        </div>
    </section>

    <x-property-filter page="commercial" :$filters :$locations />

    <section class="property-results section">
        <div class="results-toolbar">
            <div><strong>{{ $propertyCount }}</strong> {{ Str::plural('property', $propertyCount) }} found @if($filters['location'] !== '') in {{ $filters['location'] }} @endif</div>
            <form action="{{ route('commercial') }}" method="get">
                @foreach($filters as $name => $value)
                    @continue($name === 'sort')
                    @if($value !== '')<input type="hidden" name="{{ $name }}" value="{{ $value }}">@endif
                @endforeach
                <label>Sort by <select name="sort" onchange="this.form.submit()">
                    <option value="newest" @selected($filters['sort'] === 'newest')>Recently added</option>
                    <option value="price-low" @selected($filters['sort'] === 'price-low')>Price: low to high</option>
                    <option value="price-high" @selected($filters['sort'] === 'price-high')>Price: high to low</option>
                </select></label>
            </form>
        </div>

        @if($properties->isEmpty())
            <div class="no-results"><span><svg><use href="#icon-pin"/></svg></span><h2>No exact matches yet</h2><p>Try widening your search or speak with our team about properties coming to market.</p><a class="button" href="{{ route('commercial') }}">Clear all filters</a></div>
        @else
            <div class="listing-results-grid">
                @foreach($properties as $property)
                    <article class="property-card listing-card">
                        <a class="property-image" href="{{ route('property.show', $property['slug']) }}"><img src="{{ $property['image'] }}" alt="{{ $property['title'] }} in {{ $property['area'] }}" loading="lazy"><span class="property-status">{{ strtoupper($property['status']) }}</span></a>
                        <div class="property-info">
                            <p class="property-type-label">{{ $property->listing_type === 'sale' ? 'For sale' : 'To rent' }} · {{ $property['type'] }}</p>
                            <h3><a href="{{ route('property.show', $property['slug']) }}">{{ $property['title'] }}</a></h3>
                            <p>{{ $property['area'] }}, {{ $property['postcode'] }}</p>
                            <div class="details"><span>{{ $property['floor_area'] }}</span><span>{{ $property['tenure'] }}</span></div>
                            <p class="listing-summary">{{ $property['summary'] }}</p>
                            <div class="price"><strong>{!! $property['price_label'] !!}</strong><a class="card-arrow" href="{{ route('property.show', $property['slug']) }}" aria-label="View {{ $property['title'] }}">&#8594;</a></div>
                        </div>
                    </article>
                @endforeach
            </div>
        @endif
    </section>

    <section class="listing-help">
        <div><p class="eyebrow eyebrow-light">CAN'T FIND THE RIGHT ONE?</p><h2>Have a commercial requirement?</h2><p>Speak with our team about current instructions, investment opportunities and properties coming to market.</p></div>
        <a class="button button-white" href="{{ route('contact', ['interest' => 'Commercial']) }}">Register with us</a>
    </section>
</main>
@endsection
