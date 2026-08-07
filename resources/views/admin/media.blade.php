@extends('layouts.admin')
@section('title', 'Media library')
@section('heading', 'Media')
@section('content')
<div class="editor-head"><div><h1>Media library</h1><p>Upload, search, and manage reusable store assets.</p></div></div>
<div class="media-layout">
    <div class="card">
        <div class="card-head"><div><h2>Library</h2><p>{{ $media->total() }} items</p></div><form method="get"><input class="compact-input" name="q" value="{{ request('q') }}" placeholder="Search media"><button class="button secondary">Search</button></form></div>
        <div class="media-library-grid">
            @forelse($media as $item)
            <details class="media-card">
                <summary>@if(str_starts_with($item->mime_type, 'image/'))<img src="{{ $item->thumbnail_url }}" alt="{{ $item->alt_text }}">@else<span class="file-tile">PDF</span>@endif<div><b>{{ $item->title ?: $item->filename }}</b><small>{{ number_format($item->size / 1024, 1) }} KB{{ $item->webp_path ? ' · WebP optimized' : '' }}</small></div></summary>
                <div class="media-details">
                    <label>Public path<input value="{{ $item->asset_path }}" readonly></label>
                    <form class="form compact-form" method="post" action="{{ route('admin.media.update', $item) }}">@csrf @method('PUT')<label>Title<input name="title" value="{{ $item->title }}"></label><label>Alt text<input name="alt_text" value="{{ $item->alt_text }}"></label><label>Caption<textarea name="caption">{{ $item->caption }}</textarea></label><button class="button">Save details</button></form>
                    <form method="post" action="{{ route('admin.media.destroy', $item) }}" onsubmit="return confirm('Delete this media item?')">@csrf @method('DELETE')<button class="text-danger">Delete permanently</button></form>
                </div>
            </details>
            @empty<p class="empty-state">Your media library is empty.</p>@endforelse
        </div>
        {{ $media->links() }}
    </div>
    <form class="card form upload-card" method="post" action="{{ route('admin.media.store') }}" enctype="multipart/form-data">@csrf<h2>Upload new media</h2><div class="drop-zone"><b>Choose files</b><span>JPG, PNG, GIF, WebP, SVG, or PDF up to 10 MB</span><input type="file" name="files[]" multiple required data-media-upload-input></div><div class="upload-preview-grid" data-media-upload-preview hidden></div><label class="check-row"><input type="hidden" name="optimize_webp" value="0"><input type="checkbox" name="optimize_webp" value="1" checked><span><b>Optimize images as WebP</b><small>Recommended. The original is kept and used automatically if conversion is unavailable.</small></span></label><button class="button">Upload files</button></form>
</div>
@endsection
