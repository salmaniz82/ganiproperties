@extends('layouts.admin')
@section('title', 'Properties')
@section('heading', 'Properties')
@section('content')
<div class="card">
    <div class="card-head">
        <div><h2>Property listings</h2><p>Manage rental and commercial listings</p></div>
        <div class="action-row"><a class="button" href="{{ route('admin.properties.create') }}">Create property</a></div>
    </div>
    <form class="table-toolbar" method="get">
        <div class="tabs"><span class="active-tab">All properties</span><span>{{ $properties->total() }} total</span></div>
        <div class="toolbar-actions"><input class="compact-input" name="q" value="{{ request('q') }}" placeholder="Search title, area or ref"><button>Search</button></div>
    </form>
    <div class="table-wrap"><table>
        <tr><th>Property</th><th>Listing</th><th>Location</th><th>Price</th><th>Status</th><th></th></tr>
        @forelse($properties as $property)
        <tr>
            <td class="property-cell"><img src="/{{ ltrim(\App\Models\Media::variantAssetPath($property->featured_image, 'thumbnail') ?: 'assets/property-1-ref.jpg', '/') }}" alt=""><div><b>{{ $property->title }}</b><small>{{ $property->reference }}</small></div></td>
            <td>{{ $property->listing_type === 'sale' ? 'For sale' : 'To rent' }}<small>{{ $property->is_commercial ? 'Commercial' : 'Residential' }} · {{ $property->type }}</small></td>
            <td>{{ $property->area }}<small>{{ $property->postcode }}</small></td>
            <td>{!! $property->price_label !!}</td>
            <td><span class="badge {{ $property->is_published ? 'green' : 'gray' }}">{{ $property->is_published ? 'Published' : 'Draft' }}</span></td>
            <td class="row-actions">@if($property->is_published)<a class="preview-action" href="{{ route('property.show', $property->slug) }}" target="_blank" title="View property">&#8599;</a>@endif<a href="{{ route('admin.properties.edit', $property) }}">Edit</a></td>
        </tr>
        @empty<tr><td colspan="6" class="empty-state">No properties found. <a href="{{ route('admin.properties.create') }}">Create the first listing</a>.</td></tr>@endforelse
    </table></div>
    {{ $properties->links() }}
</div>
@endsection
