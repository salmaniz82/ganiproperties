<?php

namespace App\Http\Controllers;

use App\Models\{Media, Property};
use App\Services\ImageVariantService;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class AdminPropertyController extends Controller
{
    public function index(Request $request)
    {
        $properties = Property::query()
            ->when($request->q, fn ($query, $term) => $query->where(fn ($query) => $query
                ->where('title', 'like', "%$term%")
                ->orWhere('reference', 'like', "%$term%")
                ->orWhere('area', 'like', "%$term%")))
            ->latest()->paginate(20)->withQueryString();

        Media::primeVariantCache($properties->pluck('featured_image'));

        return view('admin.properties', compact('properties'));
    }

    public function create()
    {
        return view('admin.property-form', $this->formData());
    }

    public function store(Request $request, ImageVariantService $imageVariants)
    {
        $property = Property::create($this->validated($request, null, $imageVariants));

        return redirect()->route('admin.properties.edit', $property)->with('success', 'Property created.');
    }

    public function edit(Property $property)
    {
        return view('admin.property-form', $this->formData($property));
    }

    public function update(Request $request, Property $property, ImageVariantService $imageVariants)
    {
        $property->update($this->validated($request, $property, $imageVariants));

        return back()->with('success', 'Property updated.');
    }

    public function destroy(Property $property)
    {
        $property->delete();

        return redirect()->route('admin.properties.index')->with('success', 'Property deleted.');
    }

    private function validated(Request $request, ?Property $property, ImageVariantService $imageVariants): array
    {
        $data = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'slug' => ['required', 'string', 'max:180', Rule::unique('properties')->ignore($property)],
            'reference' => ['required', 'string', 'max:80', Rule::unique('properties')->ignore($property)],
            'listing_type' => ['required', Rule::in(['rent', 'sale'])],
            'is_commercial' => ['nullable', 'boolean'],
            'status' => ['required', 'string', 'max:80'],
            'type' => ['required', 'string', 'max:120'],
            'area' => ['required', 'string', 'max:120'],
            'postcode' => ['required', 'string', 'max:20'],
            'price' => ['required', 'integer', 'min:0'],
            'rent_period' => ['nullable', Rule::in(['Weekly', 'Monthly', 'Quarterly', 'Yearly'])],
            'bedrooms' => ['required', 'integer', 'min:0', 'max:99'],
            'bathrooms' => ['required', 'integer', 'min:0', 'max:99'],
            'receptions' => ['required', 'integer', 'min:0', 'max:99'],
            'tenure' => ['nullable', 'string', 'max:120'],
            'council_tax' => ['nullable', 'string', 'max:120'],
            'epc' => ['nullable', 'string', 'max:10'],
            'floor_area' => ['nullable', 'string', 'max:120'],
            'featured_image' => ['nullable', 'string', 'max:255'],
            'featured_image_upload' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'remove_featured_image' => ['nullable', 'boolean'],
            'gallery_images' => ['nullable', 'array', 'max:20'],
            'gallery_images.*' => ['string', 'max:255'],
            'gallery_image_uploads' => ['nullable', 'array', 'max:20'],
            'gallery_image_uploads.*' => ['image', 'mimes:jpg,jpeg,png,gif,webp', 'max:10240'],
            'remove_gallery_images' => ['nullable', 'array'],
            'remove_gallery_images.*' => ['string', 'max:255'],
            'summary' => ['required', 'string', 'max:1000'],
            'description_text' => ['nullable', 'string'],
            'features_text' => ['nullable', 'string'],
            'is_published' => ['nullable', 'boolean'],
            'published_at' => ['nullable', 'date'],
            'meta_title' => ['nullable', 'string', 'max:180'],
            'meta_description' => ['nullable', 'string', 'max:320'],
            'meta_keywords' => ['nullable', 'string', 'max:1000'],
            'seo_schema' => ['nullable', 'json'],
        ]);

        $data['is_commercial'] = $request->boolean('is_commercial');
        $data['intent'] = $data['is_commercial'] ? 'commercial' : ($data['listing_type'] === 'sale' ? 'buy' : 'rent');
        $data['description'] = $this->lines($data['description_text'] ?? null);
        $data['features'] = $this->lines($data['features_text'] ?? null);
        $removedGallery = $data['remove_gallery_images'] ?? [];
        $gallery = collect($data['gallery_images'] ?? [])->reject(fn (string $path) => in_array($path, $removedGallery, true));
        foreach ($request->file('gallery_image_uploads', []) as $file) {
            $gallery->push($this->storeUpload($request, $file, $imageVariants)->asset_path);
        }
        $data['gallery_images'] = $gallery->filter()->unique()->take(20)->values()->all();

        if ($request->hasFile('featured_image_upload')) {
            $data['featured_image'] = $this->storeUpload($request, $request->file('featured_image_upload'), $imageVariants)->asset_path;
        } elseif ($request->boolean('remove_featured_image')) {
            $data['featured_image'] = null;
        }

        $data['is_published'] = $request->boolean('is_published');
        $data['published_at'] = $data['is_published'] ? ($data['published_at'] ?? now()) : null;
        $data['rent_period'] = $data['is_commercial'] && $data['listing_type'] === 'rent' ? ($data['rent_period'] ?: 'Yearly') : null;
        $data['seo_schema'] = filled($data['seo_schema'] ?? null) ? json_decode($data['seo_schema'], true) : null;
        unset(
            $data['description_text'], $data['features_text'],
            $data['featured_image_upload'], $data['remove_featured_image'],
            $data['gallery_image_uploads'], $data['remove_gallery_images']
        );

        return $data;
    }

    private function storeUpload(Request $request, $file, ImageVariantService $imageVariants): Media
    {
        $path = $file->store('media/'.now()->format('Y/m'), 'public');
        $variants = $imageVariants->generate('public', $path, true);

        $media = Media::create([
            'user_id' => $request->user()->id,
            'disk' => 'public',
            'path' => $path,
            'filename' => $file->getClientOriginalName(),
            'mime_type' => $file->getMimeType() ?: $file->getClientMimeType(),
            'size' => $file->getSize(),
            'title' => pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME),
            ...$variants,
        ]);
        Media::flushVariantCache();

        return $media;
    }

    private function lines(?string $value): array
    {
        return collect(preg_split('/\r\n|\r|\n/', (string) $value))
            ->map(fn (string $line) => trim($line))->filter()->values()->all();
    }

    private function formData(?Property $property = null): array
    {
        return [
            'property' => $property,
            'mediaItems' => Media::where('mime_type', 'like', 'image/%')->latest()->limit(54)->get(),
        ];
    }
}
