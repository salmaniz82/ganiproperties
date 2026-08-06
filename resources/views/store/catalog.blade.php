@extends('layouts.store') @section('title',($category?->name ?? 'Shop').' | Party Poppers') @section('content')
<section class="section"><div class="page-head"><div><small>OUR CATALOG</small><h1>{{ $category?->name ?? (request('q') ? 'Search results' : 'Shop All') }}</h1></div><span>{{ $products->total() }} products</span></div><div class="product-grid">@forelse($products as $product) @include('store.partials.product-card') @empty <p>No products found.</p> @endforelse</div>{{ $products->links('store.partials.pagination', ['category' => $category]) }}</section>
@endsection
