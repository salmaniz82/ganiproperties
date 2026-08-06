<?php
namespace App\Http\Controllers;

use App\Models\{Category, Media, Product};
use App\Services\ImageVariantService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminMediaController extends Controller
{
    public function index(Request $request)
    {
        $media = Media::when($request->q, fn ($query, $term) => $query->where(fn ($query) => $query->where('filename', 'like', "%$term%")->orWhere('title', 'like', "%$term%")->orWhere('alt_text', 'like', "%$term%")))
            ->latest()->paginate(30)->withQueryString();
        return view('admin.media', compact('media'));
    }

    public function store(Request $request, ImageVariantService $imageVariants)
    {
        $request->validate([
            'files' => ['required'],
            'files.*' => ['file', 'mimes:jpg,jpeg,png,gif,webp,svg,pdf', 'max:10240'],
            'optimize_webp' => ['nullable', 'boolean'],
        ]);
        foreach ($request->file('files', []) as $file) {
            $path = $file->store('media/'.now()->format('Y/m'), 'public');
            $variants = str_starts_with($file->getMimeType() ?: '', 'image/')
                ? $imageVariants->generate('public', $path, $request->boolean('optimize_webp', true))
                : ['webp_path' => null, 'thumbnail_path' => null];
            Media::create([
                'user_id' => $request->user()->id,
                'disk' => 'public',
                'path' => $path,
                'filename' => $file->getClientOriginalName(),
                'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
                'size' => $file->getSize(),
                'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
                ...$variants,
            ]);
        }
        Media::flushVariantCache();
        return back()->with('success', 'Media uploaded.');
    }

    public function update(Request $request, Media $media)
    {
        $data = $request->validate(['title' => ['nullable', 'max:180'], 'alt_text' => ['nullable', 'max:255'], 'caption' => ['nullable', 'max:1000']]);
        $media->update($data);
        return back()->with('success', 'Media details updated.');
    }

    public function destroy(Media $media)
    {
        $assetPath = $media->asset_path;
        abort_if(
            Product::where('image', $assetPath)->exists()
                || Product::whereJsonContains('gallery_images', $assetPath)->exists()
                || Category::where('image', $assetPath)->exists(),
            422,
            'This media item is currently in use.'
        );
        Storage::disk($media->disk)->delete(array_filter([$media->path, $media->webp_path, $media->thumbnail_path]));
        $media->delete();
        Media::flushVariantCache();
        return back()->with('success', 'Media deleted.');
    }
}
