@extends('layouts.store')
@section('title', $product->name)
@section('meta')
@if($product->meta_description)<meta name="description" content="{{ $product->meta_description }}">@endif
@if($product->meta_keywords)<meta name="keywords" content="{{ $product->meta_keywords }}">@endif
@endsection
@if($product->seo_schema)
@push('structured-data')
<script type="application/ld+json">{!! json_encode($product->seo_schema, JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT) !!}</script>
@endpush
@endif
@section('content')
@php($gallery = $product->gallery_sources ?: [['src' => 'images/prod-arch.jpg', 'fallback' => 'images/prod-arch.jpg']])
<section class="section product-detail">
<div class="product-gallery" aria-label="{{ $product->name }} image gallery">
    <div class="product-gallery__viewport">
        @foreach($gallery as $image)
            <figure id="product-image-{{ $loop->iteration }}"><img src="{{ asset($image['src']) }}" data-fallback="{{ asset($image['fallback']) }}" onerror="if(this.src !== this.dataset.fallback){this.src=this.dataset.fallback}" alt="{{ $product->name }}{{ $loop->first ? '' : ' image '.$loop->iteration }}"></figure>
        @endforeach
    </div>
    @if(count($gallery) > 1)
        <nav class="product-gallery__thumbs" aria-label="Choose product image">
            @foreach($gallery as $image)
                <a href="#product-image-{{ $loop->iteration }}"><img src="{{ asset(\App\Models\Media::variantAssetPath($image['fallback'], 'thumbnail') ?: $image['fallback']) }}" alt="View image {{ $loop->iteration }}"></a>
            @endforeach
        </nav>
        <small class="product-gallery__hint">Scroll or swipe to view all images</small>
    @endif
</div>
<div><small>{{ $product->category?->name }}</small><h1>{{ $product->name }}</h1><h2>{{ $product->formatted_price }}</h2><p>{{ $product->description }}</p><p class="{{ $product->available_stock > 0 ? 'stock' : 'out' }}">{{ $product->available_stock > 0 ? $product->available_stock.' available' : 'Out of stock' }}</p>
<form class="stack" method="post" action="{{ route('cart.add',$product) }}" enctype="multipart/form-data">@csrf
@if($product->customization_schema)<label>Personalized text<input name="custom_text" maxlength="250"></label><label>Style option<select name="custom_option"><option>Classic</option><option>Pastel</option><option>Premium</option></select></label><label>Reference image<input type="file" name="custom_image" accept="image/*"></label>@endif
<label>Quantity<input type="number" name="quantity" value="1" min="1" max="{{ max(1,$product->available_stock) }}"></label><button class="button" {{ $product->available_stock < 1 ? 'disabled' : '' }}>Add to cart</button></form></div></section>
@auth<form method="post" action="{{ route('wishlist.toggle',$product) }}" class="section narrow">@csrf<button class="link">Add or remove from wishlist</button></form>@endauth
@endsection
