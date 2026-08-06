@php($selectedGallery = collect($selected ?? [])->filter()->values()->all())
<div class="gallery-picker" data-gallery-picker>
    <div data-gallery-inputs>
        @foreach($selectedGallery as $image)
            <input type="hidden" name="gallery_images[]" value="{{ $image }}">
        @endforeach
    </div>
    <div class="gallery-preview-grid" data-gallery-preview>
        @forelse($selectedGallery as $image)
            <div class="gallery-preview-item"><img src="{{ asset($image) }}" alt=""><button type="button" data-gallery-remove="{{ $image }}">Remove</button></div>
        @empty
            <p class="empty-state">No gallery images selected.</p>
        @endforelse
    </div>
    <div class="gallery-choice-grid">
        @forelse($mediaItems as $item)
            @if(str_starts_with($item->mime_type, 'image/'))
                <button type="button" class="media-choice {{ in_array($item->asset_path, $selectedGallery, true) ? 'selected' : '' }}" data-gallery-value="{{ $item->asset_path }}" title="{{ $item->title ?: $item->filename }}">
                    <img src="{{ $item->thumbnail_url }}" alt="{{ $item->alt_text }}">
                </button>
            @endif
        @empty
            <div class="media-empty-action">
                <a class="button secondary" href="{{ route('admin.media') }}">Add gallery images</a>
            </div>
        @endforelse
    </div>
</div>
