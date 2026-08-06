@extends('layouts.admin')
@section('title', 'Products')
@section('heading', 'Products')
@section('content')
<div class="card">
    <div class="card-head">
        <div><h2>Products</h2><p>Manage your product catalog</p></div>
        <div class="action-row"><a class="button secondary" href="{{ route('admin.media') }}">Media library</a><a class="button" href="{{ route('admin.products.create') }}">Create product</a></div>
    </div>
    <form class="table-toolbar" method="get">
        <div class="tabs"><span class="active-tab">All products</span><span>{{ $products->total() }} total</span></div>
        <div class="toolbar-actions"><input class="compact-input" name="q" value="{{ request('q') }}" placeholder="Search name or SKU"><button>Search</button></div>
    </form>
    <div class="table-wrap"><table>
        <tr><th>Product</th><th>Category</th><th>Inventory</th><th>Price</th><th>Status</th><th></th></tr>
        @forelse($products as $product)
        <tr>
            <td class="product-cell"><img src="{{ asset($product->thumbnail_image) }}"><div><b>{{ $product->name }}</b><small>{{ $product->sku }}</small></div></td>
            <td>{{ $product->category?->name ?? 'Uncategorized' }}</td><td>{{ $product->stock }}</td><td>PKR {{ number_format($product->price) }}</td>
            <td><span class="badge {{ $product->is_active ? 'green' : 'gray' }}">{{ $product->is_active ? 'Published' : 'Draft' }}</span></td>
            <td class="row-actions"><a href="{{ route('admin.products.edit', $product) }}">Edit</a></td>
        </tr>
        @empty<tr><td colspan="6" class="empty-state">No products found.</td></tr>@endforelse
    </table></div>
    {{ $products->links() }}
</div>
@endsection
