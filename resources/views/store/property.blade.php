@extends('layouts.store')
@section('title', $property['title'].', '.$property['area'].' | Gani Property Services')
@php
    $isCommercial = $property['intent'] === 'commercial';
    $activePage = $isCommercial ? 'commercial' : 'rent';
    $listingRoute = $isCommercial ? route('commercial') : route('rent');
    $listingLabel = $isCommercial ? 'Commercial' : 'Rent';
    $priceCaption = $isCommercial ? 'Annual rent' : 'Monthly rent';
    $gallery = collect([$property['image'], '/assets/property-1-ref.jpg', '/assets/property-2-ref.jpg', '/assets/property-3-ref.jpg', '/assets/property-4-ref.jpg'])->unique()->take(4)->values();
    $contactUrl = route('contact', ['property' => $property['title'].', '.$property['area'], 'interest' => $isCommercial ? 'Commercial' : 'Renting']);
@endphp
@section('content')
<main id="top">
    <section class="property-title-section section">
        <nav class="property-breadcrumbs" aria-label="Breadcrumb"><a href="{{ route('home') }}">Home</a><span>/</span><a href="{{ $listingRoute }}">{{ $listingLabel }}</a><span>/</span><span>{{ $property['title'] }}</span></nav>
        <div class="property-title-row">
            <div><span class="property-status property-title-status">{{ strtoupper($property['status']) }}</span><p>{{ $property['type'] }}</p><h1>{{ $property['title'] }}</h1><p class="property-address"><svg><use href="#icon-pin"/></svg>{{ $property['area'] }}, {{ $property['postcode'] }}</p></div>
            <div class="property-price-block"><span>{{ $priceCaption }}</span><strong>{!! $property['price_label'] !!}</strong><small>Ref: {{ $property['reference'] }}</small></div>
        </div>
    </section>

    <section class="property-gallery section" aria-label="Property gallery">
        <button class="gallery-main gallery-thumb" type="button" data-gallery-image="{{ $gallery[0] }}" aria-label="View main property image"><img src="{{ $gallery[0] }}" alt="Main view of {{ $property['title'] }}"></button>
        <div class="gallery-side">
            @foreach($gallery->slice(1) as $image)
                <button class="gallery-thumb" type="button" data-gallery-image="{{ $image }}" aria-label="View property image"><img src="{{ $image }}" alt="Additional view of {{ $property['title'] }}" loading="lazy">@if($loop->last)<span>View gallery</span>@endif</button>
            @endforeach
        </div>
    </section>

    <section class="property-detail-layout section">
        <div class="property-detail-main">
            <div class="property-facts" aria-label="Key property facts">
                @if($property['bedrooms'] > 0)<div><svg><use href="#icon-bed"/></svg><strong>{{ $property['bedrooms'] }}</strong><span>Bedrooms</span></div>@endif
                <div><svg><use href="#icon-bath"/></svg><strong>{{ $property['bathrooms'] }}</strong><span>{{ $property['bathrooms'] === 1 ? 'Bathroom' : 'Bathrooms' }}</span></div>
                @if($property['receptions'] > 0)<div><svg><use href="#icon-sofa"/></svg><strong>{{ $property['receptions'] }}</strong><span>{{ $property['receptions'] === 1 ? 'Reception' : 'Receptions' }}</span></div>@endif
                <div><svg><use href="#icon-key"/></svg><strong>{{ $property['tenure'] }}</strong><span>Tenure</span></div>
                <div><svg><use href="#icon-shield"/></svg><strong>{{ $property['epc'] }}</strong><span>EPC rating</span></div>
            </div>

            <article class="property-copy-block"><p class="eyebrow">PROPERTY OVERVIEW</p><h2>{{ $property['summary'] }}</h2>@foreach($property['description'] as $paragraph)<p>{{ $paragraph }}</p>@endforeach</article>
            <article class="property-copy-block"><h2>Key features</h2><ul class="feature-list">@foreach($property['features'] as $feature)<li><svg><use href="#icon-check"/></svg>{{ $feature }}</li>@endforeach</ul></article>
            <article class="property-copy-block"><h2>Property information</h2><dl class="property-information"><div><dt>Property type</dt><dd>{{ $property['type'] }}</dd></div><div><dt>Tenure</dt><dd>{{ $property['tenure'] }}</dd></div><div><dt>Approx. floor area</dt><dd>{{ $property['floor_area'] }}</dd></div><div><dt>Council tax</dt><dd>{{ $property['council_tax'] }}</dd></div><div><dt>EPC rating</dt><dd><span class="epc-badge epc-{{ strtolower($property['epc']) }}">{{ $property['epc'] }}</span></dd></div><div><dt>Reference</dt><dd>{{ $property['reference'] }}</dd></div></dl><p class="property-disclaimer">These particulars are intended as a guide only. Measurements are approximate and tenants should verify important information independently.</p></article>
            <article class="property-location-block"><div><p class="eyebrow">THE LOCATION</p><h2>{{ $property['area'] }}, {{ $property['postcode'] }}</h2><p>Well placed for local shops, cafes, green spaces and transport connections across South London and into central London.</p></div><div class="location-map-placeholder" aria-label="Map placeholder"><svg><use href="#icon-pin"/></svg><span>Map location</span><small>Exact position available from the agent</small></div></article>
        </div>

        <aside class="property-enquiry-card">
            <p class="eyebrow">INTERESTED IN THIS PROPERTY?</p><h2>Arrange a viewing</h2><p>Speak with our local team for availability, viewing times and any questions about the property.</p>
            <a class="button" href="{{ $contactUrl }}">Request a viewing</a>
            <a class="enquiry-phone" href="tel:02086737778"><svg class="icon"><use href="#icon-phone"/></svg><span><small>Call the team</small>020 8673 7778</span></a>
            <a class="enquiry-email" href="mailto:hello@ganipropertyservices.co.uk?subject={{ rawurlencode('Enquiry about '.$property['title']) }}">Email this property</a>
            <div class="enquiry-actions"><button class="save-property" type="button" aria-pressed="false"><svg><use href="#icon-heart"/></svg> Save</button><button class="share-property" type="button" data-share-title="{{ $property['title'] }}"><svg><use href="#icon-chat"/></svg> Share</button></div>
        </aside>
    </section>
</main>
@endsection
