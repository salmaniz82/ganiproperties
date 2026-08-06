<div class="media-picker" data-media-picker>
    <input type="hidden" name="image" value="{{ $selected }}" data-media-input>
    <div class="media-selected">
        <div class="media-preview">@if($selected)<img src="{{ asset($selected) }}" alt="">@else<span>No image selected</span>@endif</div>
        <div><b>Featured image</b><small data-media-path>{{ $selected ?: 'Choose from the media library below' }}</small></div>
        <button class="button secondary" type="button" data-media-clear>Clear</button>
    </div>
    <div class="media-choice-grid">
        @forelse($mediaItems as $item)
            <button type="button" class="media-choice {{ $selected === $item->asset_path ? 'selected' : '' }}" data-media-value="{{ $item->asset_path }}" title="{{ $item->title ?: $item->filename }}">
                @if(str_starts_with($item->mime_type, 'image/'))<img src="{{ $item->thumbnail_url }}" alt="{{ $item->alt_text }}">@else<span class="file-tile">PDF</span>@endif
            </button>
        @empty
            <div class="media-empty-action">
                <a class="button secondary" href="{{ route('admin.media') }}">Add image</a>
            </div>
        @endforelse
    </div>
    <label>Or enter asset path<input name="image_path_display" value="{{ $selected }}" data-media-text placeholder="storage/media/2026/06/image.jpg"></label>
</div>
