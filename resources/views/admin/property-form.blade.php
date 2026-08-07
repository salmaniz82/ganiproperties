@extends('layouts.admin')
@section('title', $property ? 'Edit property' : 'Create property')
@section('heading', 'Properties')
@section('content')
@php
    $editing = (bool) $property;
    $description = old('description_text', $property ? implode("\n", $property->description ?? []) : '');
    $features = old('features_text', $property ? implode("\n", $property->features ?? []) : '');
@endphp
<div class="editor-head"><div><a class="back-link" href="{{ route('admin.properties.index') }}">&#8592; Property listings</a><h1>{{ $editing ? 'Edit property' : 'Create property' }}</h1><p>Keep listing details and photography ready for the website.</p></div>@if($editing && $property->is_published)<a class="button secondary" href="{{ route('property.show', $property->slug) }}" target="_blank">View listing</a>@endif</div>
<form method="post" action="{{ $editing ? route('admin.properties.update', $property) : route('admin.properties.store') }}" enctype="multipart/form-data">
    @csrf @if($editing) @method('PUT') @endif
    <div class="editor-layout">
        <div class="editor-primary">
            <section class="card form-section">
                <div class="section-heading"><h2>Listing details</h2><p>The main information visitors use when browsing.</p></div>
                <label>Title<input name="title" value="{{ old('title', $property?->title) }}" required maxlength="180"></label>
                <div class="two"><label>Slug<input name="slug" value="{{ old('slug', $property?->slug) }}" required maxlength="180"></label><label>Reference<input name="reference" value="{{ old('reference', $property?->reference) }}" required maxlength="80"></label></div>
                <fieldset class="listing-type-options"><legend>Transaction</legend><label><input type="radio" name="listing_type" value="rent" @checked(old('listing_type', $property?->listing_type ?? 'rent') === 'rent') required><span><b>To rent</b><small>Lettings and rental listings</small></span></label><label><input type="radio" name="listing_type" value="sale" @checked(old('listing_type', $property?->listing_type) === 'sale') required><span><b>For sale</b><small>Residential or commercial sales</small></span></label></fieldset>
                <label class="check"><input type="hidden" name="is_commercial" value="0"><input type="checkbox" name="is_commercial" value="1" @checked(old('is_commercial', $property?->is_commercial ?? false))> This is a commercial property</label>
                <div class="two"><label>Marketing status<input name="status" value="{{ old('status', $property?->status ?? 'To let') }}" required></label><label>Property type<input name="type" value="{{ old('type', $property?->type) }}" required placeholder="Apartment"></label></div>
                <div class="three"><label>Area<input name="area" value="{{ old('area', $property?->area) }}" required placeholder="Balham"></label><label>Postcode<input name="postcode" value="{{ old('postcode', $property?->postcode) }}" required placeholder="SW12"></label><label>Price (&pound;)<input type="number" min="0" name="price" value="{{ old('price', $property?->price) }}" required></label></div>
                <label>Commercial rent period<select name="rent_period"><option value="">Not applicable</option>@foreach(['Weekly','Monthly','Quarterly','Yearly'] as $period)<option value="{{ $period }}" @selected(old('rent_period', $property?->rent_period) === $period)>{{ $period }}</option>@endforeach</select></label>
                <label>Summary<textarea name="summary" rows="3" required maxlength="1000">{{ old('summary', $property?->summary) }}</textarea></label>
                <label>Description <small>One paragraph per line</small><textarea name="description_text" rows="7">{{ $description }}</textarea></label>
                <label>Key features <small>One feature per line</small><textarea name="features_text" rows="7">{{ $features }}</textarea></label>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Property facts</h2><p>Details shown on the listing and property page.</p></div>
                <div class="three"><label>Bedrooms<input type="number" min="0" max="99" name="bedrooms" value="{{ old('bedrooms', $property?->bedrooms ?? 0) }}" required></label><label>Bathrooms<input type="number" min="0" max="99" name="bathrooms" value="{{ old('bathrooms', $property?->bathrooms ?? 0) }}" required></label><label>Receptions<input type="number" min="0" max="99" name="receptions" value="{{ old('receptions', $property?->receptions ?? 0) }}" required></label></div>
                <div class="two"><label>Tenure / terms<input name="tenure" value="{{ old('tenure', $property?->tenure) }}" placeholder="Long let or New lease"></label><label>Council tax / rates<input name="council_tax" value="{{ old('council_tax', $property?->council_tax) }}"></label></div>
                <div class="two"><label>EPC rating<input name="epc" value="{{ old('epc', $property?->epc) }}" maxlength="10"></label><label>Floor area<input name="floor_area" value="{{ old('floor_area', $property?->floor_area) }}" placeholder="824 sq ft / 76.6 sq m"></label></div>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Gallery images</h2><p>Keep existing images or upload multiple new property photos.</p></div>
                @php($selectedGallery = collect(old('gallery_images', $property?->gallery_images ?? []))->filter()->values())
                <div class="direct-gallery-grid" data-existing-gallery>
                    @forelse($selectedGallery as $image)
                        <label class="direct-gallery-item"><img src="/{{ ltrim($image, '/') }}" alt=""><input type="hidden" name="gallery_images[]" value="{{ $image }}"><span><input type="checkbox" name="remove_gallery_images[]" value="{{ $image }}"> Remove</span></label>
                    @empty
                        <p class="empty-state">No gallery images uploaded yet.</p>
                    @endforelse
                </div>
                <label>Add gallery images<input type="file" name="gallery_image_uploads[]" accept="image/jpeg,image/png,image/gif,image/webp" multiple data-gallery-upload-input><small>Select up to 20 JPG, PNG, GIF, or WebP images. Each image may be up to 10 MB.</small></label>
                <div class="direct-gallery-grid" data-gallery-upload-preview hidden></div>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>SEO</h2><p>Search metadata for the public property detail page.</p></div>
                <label>Meta title<input name="meta_title" maxlength="180" value="{{ old('meta_title', $property?->meta_title) }}" placeholder="{{ $property?->title ?: 'Property title' }} | Gani Property Services"></label>
                <label>Meta description<textarea name="meta_description" rows="3" maxlength="320" placeholder="A concise description for search results.">{{ old('meta_description', $property?->meta_description) }}</textarea></label>
                <label>Meta keywords<input name="meta_keywords" maxlength="1000" value="{{ old('meta_keywords', $property?->meta_keywords) }}" placeholder="property to rent, Balham, SW12"></label>
                <label>Schema JSON<textarea name="seo_schema" rows="10" class="code-input" placeholder='Paste valid RealEstateListing schema JSON here'>{{ old('seo_schema', $property?->seo_schema ? json_encode($property->seo_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></label>
            </section>
        </div>
        <aside class="editor-aside">
            <section class="card form-section">
                <div class="section-heading"><h2>Publish</h2><p>Draft listings remain hidden from the public website.</p></div>
                <label class="check"><input type="hidden" name="is_published" value="0"><input type="checkbox" name="is_published" value="1" @checked(old('is_published', $property?->is_published ?? false))> Published</label>
                <button class="button" type="submit">{{ $editing ? 'Save property' : 'Create property' }}</button>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Featured image</h2><p>Used on listing cards and as the first gallery image.</p></div>
                @php($featuredImage = old('featured_image', $property?->featured_image))
                <input type="hidden" name="featured_image" value="{{ $featuredImage }}">
                <div class="direct-featured-preview" data-featured-upload-preview>@if($featuredImage)<img src="/{{ ltrim($featuredImage, '/') }}" alt="Current featured image">@else<span>No featured image uploaded</span>@endif</div>
                <label>Upload featured image<input type="file" name="featured_image_upload" accept="image/jpeg,image/png,image/gif,image/webp" data-featured-upload-input><small>JPG, PNG, GIF, or WebP up to 10 MB.</small></label>
                @if($featuredImage)<label class="check"><input type="checkbox" name="remove_featured_image" value="1"> Remove current featured image</label>@endif
            </section>
            @if($editing)<section class="card danger-zone"><h2>Delete property</h2><p>This permanently removes the listing record. Media files remain in the library.</p><button class="button danger" type="submit" form="delete-property" onclick="return confirm('Delete this property?')">Delete property</button></section>@endif
        </aside>
    </div>
</form>
@if($editing)<form id="delete-property" method="post" action="{{ route('admin.properties.destroy', $property) }}">@csrf @method('DELETE')</form>@endif
@endsection
