@props([
    'page' => 'home',
    'filters' => [],
    'locations' => collect(),
    'types' => collect(),
])

@php
    $filters = array_merge([
        'location' => '',
        'type' => '',
        'bedrooms' => '',
        'max_price' => '',
        'listing_type' => '',
        'rent_period' => '',
    ], $filters);
    $section = in_array($page, ['rent', 'buy', 'commercial'], true) ? $page : 'all';
    $isCommercial = $section === 'commercial';
    $action = match ($section) {
        'rent' => route('rent'),
        'buy' => route('buy'),
        'commercial' => route('commercial'),
        default => route('home'),
    };
@endphp

<section @class(['listing-filter-wrap', 'property-filter-wrap-home' => $page === 'home']) aria-label="Filter properties" @if($page === 'home') id="search" @endif>
    <form
        class="listing-filter property-filter"
        action="{{ $action }}"
        method="get"
        data-property-filter
        data-home-url="{{ route('home') }}"
        data-rent-url="{{ route('rent') }}"
        data-buy-url="{{ route('buy') }}"
        data-commercial-url="{{ route('commercial') }}"
    >
        <label>
            <span>Section</span>
            <select name="section" data-filter-section>
                @if($page === 'home')<option value="all" @selected($section === 'all')>All properties</option>@endif
                <option value="rent" @selected($section === 'rent')>Rent</option>
                <option value="buy" @selected($section === 'buy')>Buy</option>
                <option value="commercial" @selected($section === 'commercial')>Commercial</option>
            </select>
        </label>

        <label>
            <span>Location</span>
            <select name="location"><option value="">Any location</option>
                @foreach($locations as $location)
                    <option value="{{ $location }}" @selected($filters['location'] === $location)>{{ $location }}</option>
                @endforeach
            </select>
        </label>

        <label data-filter-residential @if($isCommercial) hidden @endif>
            <span>Property type</span>
            <select name="type" @disabled($isCommercial)><option value="">Any type</option>
                @foreach($types as $type)
                    <option value="{{ $type }}" @selected($filters['type'] === $type)>{{ $type }}</option>
                @endforeach
            </select>
        </label>
        <label data-filter-residential @if($isCommercial) hidden @endif>
            <span>Min bedrooms</span>
            <select name="bedrooms" @disabled($isCommercial)><option value="">Any</option>
                @foreach([1, 2, 3, 4] as $bedrooms)
                    <option value="{{ $bedrooms }}" @selected($filters['bedrooms'] === (string) $bedrooms)>{{ $bedrooms }}{{ $bedrooms === 4 ? '+' : '' }}</option>
                @endforeach
            </select>
        </label>

        <label data-filter-commercial @if(!$isCommercial) hidden @endif>
            <span>Transaction</span>
            <select name="listing_type" @disabled(!$isCommercial)><option value="">Rent or buy</option><option value="rent" @selected($filters['listing_type'] === 'rent')>To rent</option><option value="sale" @selected($filters['listing_type'] === 'sale')>For sale</option></select>
        </label>
        <label data-filter-commercial @if(!$isCommercial) hidden @endif>
            <span>Rent period</span>
            <select name="rent_period" @disabled(!$isCommercial)><option value="">Any period</option>
                @foreach(['Weekly', 'Monthly', 'Quarterly', 'Yearly'] as $period)
                    <option value="{{ $period }}" @selected($filters['rent_period'] === $period)>{{ $period }}</option>
                @endforeach
            </select>
        </label>

        <label data-filter-price="rent" @if($section !== 'rent') hidden @endif>
            <span>Max price</span>
            <select name="max_price" @disabled($section !== 'rent')><option value="">No maximum</option>@foreach(['1800' => '&pound;1,800 pcm', '2500' => '&pound;2,500 pcm', '3500' => '&pound;3,500 pcm'] as $value => $label)<option value="{{ $value }}" @selected($filters['max_price'] === $value)>{!! $label !!}</option>@endforeach</select>
        </label>
        <label data-filter-price="buy" @if(!in_array($section, ['buy', 'all'], true)) hidden @endif>
            <span>Max price</span>
            <select name="max_price" @disabled(!in_array($section, ['buy', 'all'], true))><option value="">No maximum</option>@foreach(['350000' => '&pound;350,000', '550000' => '&pound;550,000', '750000' => '&pound;750,000', '1000000' => '&pound;1,000,000'] as $value => $label)<option value="{{ $value }}" @selected($filters['max_price'] === $value)>{!! $label !!}</option>@endforeach</select>
        </label>
        <label data-filter-price="commercial" @if($section !== 'commercial') hidden @endif>
            <span>Max price</span>
            <select name="max_price" @disabled($section !== 'commercial')><option value="">No maximum</option>@foreach(['30000' => '&pound;30,000', '50000' => '&pound;50,000', '100000' => '&pound;100,000'] as $value => $label)<option value="{{ $value }}" @selected($filters['max_price'] === $value)>{!! $label !!}</option>@endforeach</select>
        </label>

        <button class="button" type="submit">Update results</button>
    </form>
</section>
