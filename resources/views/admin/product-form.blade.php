@extends('layouts.admin')
@section('title', $product ? 'Edit product' : 'Create product')
@section('heading', $product ? 'Edit product' : 'Create product')
@section('content')
@php($editing = (bool) $product)
@php($productType = old('product_type', $product?->product_type ?? 'simple'))
@php($variantOptions = old('variant_options', $product?->variant_options ?? [['name' => '', 'values' => '']]))
@php($variantRows = old('variants', $product?->variants?->map(fn($variant) => ['name' => $variant->name, 'sku' => $variant->sku, 'price' => $variant->price, 'discount_price' => $variant->discount_price, 'stock' => $variant->stock, 'options' => json_encode($variant->options ?? [])])->values()->all() ?? []))
<form method="post" action="{{ $editing ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="editor-head">
        <div><a class="back-link" href="{{ route('admin.products') }}">Products /</a><h1>{{ $editing ? $product->name : 'Create product' }}</h1><p>{{ $editing ? 'Update catalog information, pricing, media, and availability.' : 'Add a new item to your catalog.' }}</p></div>
        <div class="action-row"><a class="button secondary" href="{{ route('admin.products') }}">Cancel</a><button class="button">{{ $editing ? 'Save changes' : 'Create product' }}</button></div>
    </div>
    <div class="editor-layout">
        <div class="editor-primary">
            <section class="card form-section">
                <div class="section-heading"><h2>General</h2><p>Basic product information shown to customers.</p></div>
                <label>Title<input name="name" value="{{ old('name', $product?->name) }}" required></label>
                <label>Slug<input name="slug" value="{{ old('slug', $product?->slug) }}" required></label>
                <label>Description<textarea name="description" rows="7">{{ old('description', $product?->description) }}</textarea></label>
            </section>
            <section class="card product-data-card" data-variant-builder data-product-tabs>
                <div class="product-data-head">
                    <h2>Product data</h2>
                    <select name="product_type" data-product-type><option value="simple" @selected($productType === 'simple')>Simple product</option><option value="variable" @selected($productType === 'variable')>Variable product</option></select>
                    <label class="inline-check"><input type="checkbox" name="variant_same_pricing" value="1" data-same-pricing @checked(old('variant_same_pricing', $product?->variant_same_pricing ?? false))> Keep variant prices same</label>
                </div>
                <div class="product-data-body">
                    <nav class="product-data-tabs">
                        <button type="button" class="active" data-product-tab="general">General</button>
                        <button type="button" data-product-tab="inventory">Inventory</button>
                        <button type="button" data-product-tab="attributes">Attributes</button>
                        <button type="button" data-product-tab="variants">Variants</button>
                    </nav>
                    <div class="product-data-panels">
                        <div class="product-data-panel active" data-product-panel="general">
                            <label>Regular price (PKR)<input type="number" name="price" min="0" value="{{ old('price', $product?->price ?? 0) }}" required></label>
                            <label>Discount price (PKR)<input type="number" name="discount_price" min="0" value="{{ old('discount_price', $product?->discount_price) }}"></label>
                            <label>Compare at price<input type="number" name="compare_at_price" min="0" value="{{ old('compare_at_price', $product?->compare_at_price) }}"></label>
                        </div>
                        <div class="product-data-panel" data-product-panel="inventory">
                            <div class="two"><label>Inventory<input type="number" name="stock" min="0" value="{{ old('stock', $product?->stock ?? 0) }}" required></label><label>SKU<input name="sku" value="{{ old('sku', $product?->sku) }}" required></label></div>
                            <label class="check"><input type="checkbox" name="track_inventory" value="1" @checked(old('track_inventory', $product?->track_inventory ?? true))> Track inventory</label>
                        </div>
                        <div class="product-data-panel" data-product-panel="attributes">
                            <p class="panel-help">Enter the attribute name and add values below it, then use Generate variants to create every combination.</p>
                            <div class="variant-attribute-entry">
                                <label>Attribute name<input placeholder="Color" data-attribute-name-entry></label>
                                <label>Value<input placeholder="Red" data-option-value-entry></label>
                                <button type="button" class="button secondary" data-add-value>Add value</button>
                            </div>
                            <div data-variant-options>
                                @foreach($variantOptions as $index => $option)
                                    <input type="hidden" name="variant_options[{{ $index }}][name]" value="{{ $option['name'] ?? '' }}" data-option-name-hidden>
                                    <input type="hidden" name="variant_options[{{ $index }}][values]" value="{{ $option['values'] ?? '' }}" data-option-values-hidden>
                                @endforeach
                            </div>
                            <div class="attribute-list" data-attribute-list></div>
                        </div>
                        <div class="product-data-panel" data-product-panel="variants">
                            <div class="action-row"><button type="button" class="button" data-generate-variants>Generate variants</button></div>
                            <div class="variant-table-wrap">
                                <table class="variant-table">
                                    <thead><tr><th>Variant</th><th>SKU</th><th>Regular price</th><th>Discount</th><th>Stock</th><th></th></tr></thead>
                                    <tbody data-variant-rows>
                                        @foreach($variantRows as $index => $variant)
                                        <tr>
                                            <td><input name="variants[{{ $index }}][name]" value="{{ $variant['name'] ?? '' }}" required><input type="hidden" name="variants[{{ $index }}][options]" value="{{ $variant['options'] ?? '{}' }}"></td>
                                            <td><input name="variants[{{ $index }}][sku]" value="{{ $variant['sku'] ?? '' }}" required></td>
                                            <td><input type="number" min="0" name="variants[{{ $index }}][price]" value="{{ $variant['price'] ?? old('price', $product?->price ?? 0) }}" required data-variant-price></td>
                                            <td><input type="number" min="0" name="variants[{{ $index }}][discount_price]" value="{{ $variant['discount_price'] ?? old('discount_price', $product?->discount_price) }}" data-variant-discount></td>
                                            <td><input type="number" min="0" name="variants[{{ $index }}][stock]" value="{{ $variant['stock'] ?? 0 }}" required></td>
                                            <td><button type="button" class="text-danger" data-remove-variant>Remove</button></td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                <p class="variant-empty" data-variant-empty @if($variantRows) hidden @endif>No variants generated yet.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Search engine optimization</h2><p>Control how this product is described to search engines and structured-data consumers.</p></div>
                <label>Meta description<textarea name="meta_description" rows="3" maxlength="320" placeholder="A concise description of this product for search results.">{{ old('meta_description', $product?->meta_description) }}</textarea></label>
                <label>Meta keywords<input name="meta_keywords" value="{{ old('meta_keywords', $product?->meta_keywords) }}" placeholder="birthday balloons, party decorations, gifts"></label>
                <label>Schema JSON<textarea name="seo_schema" rows="10" class="code-input" placeholder='Paste valid Product schema JSON here'>{{ old('seo_schema', $product?->seo_schema ? json_encode($product->seo_schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></label>
            </section>
        </div>
        <aside class="editor-aside">
            <section class="card form-section">
                <div class="section-heading"><h2>Status</h2></div>
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $product?->is_active ?? true))> Published</label>
                <label class="check"><input type="checkbox" name="is_featured" value="1" @checked(old('is_featured', $product?->is_featured ?? false))> Featured product</label>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Organization</h2></div>
                <label>Category<select name="category_id"><option value="">Uncategorized</option>@foreach($categories as $category)<option value="{{ $category->id }}" @selected(old('category_id', $product?->category_id) == $category->id)>{{ str_repeat('- ', $category->depth) }}{{ $category->name }}</option>@endforeach</select></label>
            </section>
            @if($editing)
            <section class="card danger-zone"><h2>Delete product</h2><p>Permanently remove this product from the catalog.</p><button class="button danger" form="delete-product">Delete product</button></section>
            @endif
            <section class="card form-section">
                <div class="section-heading"><h2>Featured image</h2><p>Select the main product image shown in listings and product details.</p></div>
                @include('admin.partials.media-picker', ['selected' => old('image', $product?->image)])
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>Gallery images</h2><p>Select additional images. Selected gallery items appear below in a 3-column grid.</p></div>
                @include('admin.partials.gallery-picker', ['selected' => old('gallery_images', $product?->gallery_images ?? [])])
            </section>
        </aside>
    </div>
</form>
@if($editing)<form id="delete-product" method="post" action="{{ route('admin.products.destroy', $product) }}" onsubmit="return confirm('Delete this product?')">@csrf @method('DELETE')</form>@endif
@endsection
