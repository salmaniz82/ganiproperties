@extends('layouts.admin')
@section('title', $page ? 'Edit page' : 'Create page')
@section('heading', $page ? 'Edit page' : 'Create page')
@section('content')
@php($editing = (bool) $page)
<form method="post" action="{{ $editing ? route('admin.pages.update', $page) : route('admin.pages.store') }}">
    @csrf @if($editing) @method('PUT') @endif
    <div class="editor-head">
        <div><a class="back-link" href="{{ route('admin.pages') }}">Pages /</a><h1>{{ $editing ? $page->title : 'Create page' }}</h1><p>Manage page content and search metadata.</p></div>
        <div class="action-row"><a class="button secondary" href="{{ route('admin.pages') }}">Cancel</a><button class="button">{{ $editing ? 'Save changes' : 'Create page' }}</button></div>
    </div>
    <div class="editor-layout">
        <div class="editor-primary">
            <section class="card form-section">
                <div class="section-heading"><h2>Content</h2></div>
                <label>Title<input name="title" value="{{ old('title', $page?->title) }}" required></label>
                <label>Slug<input name="slug" value="{{ old('slug', $page?->slug) }}" required></label>
                <label>Body content<textarea name="content" rows="14">{{ old('content', $page?->content) }}</textarea></label>
            </section>
            <section class="card form-section">
                <div class="section-heading"><h2>SEO</h2></div>
                <label>Meta title<input name="meta_title" value="{{ old('meta_title', $page?->meta_title) }}"></label>
                <label>Meta keywords<input name="meta_keywords" value="{{ old('meta_keywords', $page?->meta_keywords) }}"></label>
                <label>Schema JSON<textarea name="schema" rows="9" class="code-input">{{ old('schema', $page?->schema ? json_encode($page->schema, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES) : '') }}</textarea></label>
            </section>
        </div>
        <aside class="editor-aside">
            <section class="card form-section">
                <div class="section-heading"><h2>Publishing</h2></div>
                <label>Page template
                    <select name="customizer_template">
                        <option value="">Default content editor</option>
                        @foreach($customizerTemplates as $path => $name)
                            <option value="{{ $path }}" @selected(old('customizer_template', $page?->customizer_template) === $path)>Customizer: {{ $name }}</option>
                        @endforeach
                    </select>
                </label>
                <p class="panel-help">Default pages render the body content field. Customizer pages render the selected JSON template instead, while slug and SEO fields still come from this page.</p>
                <label>Position<input type="number" name="position" min="0" value="{{ old('position', $page?->position ?? 0) }}" required></label>
                <label class="check"><input type="checkbox" name="is_active" value="1" @checked(old('is_active', $page?->is_active ?? true))> Published</label>
                @if($editing && $page->customizer_template)<a class="button secondary" href="{{ route('admin.pages.customizer', $page) }}">Open page customizer</a>@endif
                @if($editing)<a class="button secondary" href="{{ route('pages.show', $page->slug) }}" target="_blank">View page</a>@endif
            </section>
        </aside>
    </div>
</form>
@endsection
