@extends('layouts.admin')
@section('title', $category ? 'Edit category' : 'Create category')
@section('heading', $category ? 'Edit category' : 'Create category')
@section('content')
@php($editing = (bool) $category)
<form method="post" action="{{ $editing ? route('admin.categories.update', $category) : route('admin.categories.store') }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="editor-head"><div><a class="back-link" href="{{ route('admin.categories') }}">Categories /</a><h1>{{ $editing ? $category->name : 'Create category' }}</h1><p>Build nested catalog navigation with as many parent and child levels as needed.</p></div><div class="action-row"><a class="button secondary" href="{{ route('admin.categories') }}">Cancel</a><button class="button">{{ $editing ? 'Save changes' : 'Create category' }}</button></div></div>
    <div class="editor-layout">
        <div class="editor-primary">
            <section class="card form-section">
                <div class="section-heading"><h2>General</h2><p>Name and describe this category.</p></div>
                <label>Name<input name="name" value="{{ old('name', $category?->name) }}" required></label>
                <label>Description<textarea name="description" rows="6">{{ old('description', $category?->description) }}</textarea></label>
            </section>
            <section class="card form-section"><div class="section-heading"><h2>Category image</h2></div>@include('admin.partials.media-picker', ['selected' => old('image', $category?->image)])</section>
        </div>
        <aside class="editor-aside">
            <section class="card form-section">
                <div class="section-heading"><h2>Organization</h2></div>
                <label>Parent category<select name="parent_id"><option value="">None (top level)</option>@foreach($parentOptions as $option)<option value="{{ $option->id }}" @selected(old('parent_id', $category?->parent_id) == $option->id)>{{ str_repeat('— ', $option->depth) }}{{ $option->name }}</option>@endforeach</select></label>
                <label>Slug<input name="slug" value="{{ old('slug', $category?->slug) }}" required></label>
                <label>Position<input type="number" name="position" min="0" value="{{ old('position', $category?->position ?? 0) }}" required></label>
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $category?->is_active ?? true))> Active</label>
            </section>
            @if($editing)<section class="card danger-zone"><h2>Delete category</h2><p>Products become uncategorized and child categories move up one level.</p><button class="button danger" form="delete-category">Delete category</button></section>@endif
        </aside>
    </div>
</form>
@if($editing)<form id="delete-category" method="post" action="{{ route('admin.categories.destroy', $category) }}" onsubmit="return confirm('Delete this category?')">@csrf @method('DELETE')</form>@endif
@endsection
