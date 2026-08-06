@extends('layouts.admin')
@section('title', 'Categories')
@section('heading', 'Categories')
@section('content')
<div class="card">
    <div class="card-head"><div><h2>Categories</h2><p>Organize products with multi-level parent and child categories.</p></div><a class="button" href="{{ route('admin.categories.create') }}">Create category</a></div>
    <div class="table-wrap"><table>
        <tr><th>Name</th><th>Slug</th><th>Products</th><th>Position</th><th>Status</th><th></th></tr>
        @forelse($categories as $category)
        <tr>
            <td><div class="tree-name" style="--depth:{{ $category->depth }}"><span class="tree-line"></span>@if($category->image)<img src="{{ asset($category->image) }}" alt="">@endif<div><b>{{ $category->name }}</b><small>{{ $category->depth ? 'Level '.($category->depth + 1) : 'Top level' }}</small></div></div></td>
            <td>{{ $category->slug }}</td><td>{{ $category->products_count }}</td><td>{{ $category->position }}</td><td><span class="badge {{ $category->is_active ? 'green' : 'gray' }}">{{ $category->is_active ? 'Active' : 'Inactive' }}</span></td>
            <td class="row-actions"><a href="{{ route('admin.categories.edit', $category) }}">Edit</a></td>
        </tr>
        @empty<tr><td colspan="6" class="empty-state">No categories yet.</td></tr>@endforelse
    </table></div>
</div>
@endsection
