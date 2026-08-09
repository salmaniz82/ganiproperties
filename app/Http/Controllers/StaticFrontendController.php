<?php

namespace App\Http\Controllers;

use App\Models\Property;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StaticFrontendController extends Controller
{
    public function home(Request $request)
    {
        $properties = Property::published()->get();
        $filters = [
            'location' => trim((string) $request->query('location', '')),
            'type' => trim((string) $request->query('type', '')),
            'bedrooms' => trim((string) $request->query('bedrooms', '')),
            'max_price' => trim((string) $request->query('max_price', '')),
        ];

        $featured = $properties
            ->when($filters['location'] !== '', fn ($items) => $items->where('area', $filters['location']))
            ->when($filters['type'] !== '', fn ($items) => $items->where('type', $filters['type']))
            ->when($filters['bedrooms'] !== '', fn ($items) => $items->filter(fn ($property) => $property['bedrooms'] >= (int) $filters['bedrooms']))
            ->when($filters['max_price'] !== '', fn ($items) => $items->filter(fn ($property) => $property['price'] <= (int) $filters['max_price']))
            ->sortByDesc(fn (Property $property) => $property->published_at?->timestamp ?? $property->created_at->timestamp)
            ->take(4);

        return view('store.home', [
            'featuredProperties' => $featured->values(),
            'filters' => $filters,
            'locations' => $properties->pluck('area')->unique()->sort()->values(),
            'types' => $properties->where('is_commercial', false)->pluck('type')->unique()->sort()->values(),
        ]);
    }

    public function rent(Request $request, ?string $area = null, ?string $type = null)
    {
        $properties = Property::published()->where('listing_type', 'rent')->where('is_commercial', false)->get();
        $areaName = $area ? $this->valueFromSlug($properties->pluck('area')->unique(), $area) : '';
        $typeName = $type ? $this->valueFromSlug($properties->pluck('type')->unique(), $type) : '';
        $filters = [
            'location' => $areaName ?: trim((string) $request->query('location', '')),
            'type' => $typeName ?: trim((string) $request->query('type', '')),
            'bedrooms' => trim((string) $request->query('bedrooms', '')),
            'max_price' => trim((string) $request->query('max_price', '')),
            'sort' => trim((string) $request->query('sort', 'newest')),
        ];

        $filtered = $properties
            ->when($filters['location'] !== '', fn ($items) => $items->where('area', $filters['location']))
            ->when($filters['type'] !== '', fn ($items) => $items->where('type', $filters['type']))
            ->when($filters['bedrooms'] !== '', fn ($items) => $items->filter(fn ($property) => $property['bedrooms'] >= (int) $filters['bedrooms']))
            ->when($filters['max_price'] !== '', fn ($items) => $items->filter(fn ($property) => $property['price'] <= (int) $filters['max_price']));

        $filtered = match ($filters['sort']) {
            'price-low' => $filtered->sortBy('price'),
            'price-high' => $filtered->sortByDesc('price'),
            'bedrooms' => $filtered->sortByDesc('bedrooms'),
            default => $filtered->sortByDesc(fn (Property $property) => $property->published_at?->timestamp ?? $property->created_at->timestamp),
        };

        return view('store.rent', [
            'properties' => $filtered->values(),
            'filters' => $filters,
            'locations' => $properties->pluck('area')->unique()->sort()->values(),
            'types' => $properties->pluck('type')->unique()->sort()->values(),
        ]);
    }

    public function sale(Request $request, ?string $area = null, ?string $type = null)
    {
        $properties = Property::published()->where('listing_type', 'sale')->where('is_commercial', false)->get();
        $areaName = $area ? $this->valueFromSlug($properties->pluck('area')->unique(), $area) : '';
        $typeName = $type ? $this->valueFromSlug($properties->pluck('type')->unique(), $type) : '';
        $filters = [
            'location' => $areaName ?: trim((string) $request->query('location', '')),
            'type' => $typeName ?: trim((string) $request->query('type', '')),
            'bedrooms' => trim((string) $request->query('bedrooms', '')),
            'max_price' => trim((string) $request->query('max_price', '')),
            'sort' => trim((string) $request->query('sort', 'newest')),
        ];

        $filtered = $properties
            ->when($filters['location'] !== '', fn ($items) => $items->where('area', $filters['location']))
            ->when($filters['type'] !== '', fn ($items) => $items->where('type', $filters['type']))
            ->when($filters['bedrooms'] !== '', fn ($items) => $items->filter(fn ($property) => $property['bedrooms'] >= (int) $filters['bedrooms']))
            ->when($filters['max_price'] !== '', fn ($items) => $items->filter(fn ($property) => $property['price'] <= (int) $filters['max_price']));

        $filtered = match ($filters['sort']) {
            'price-low' => $filtered->sortBy('price'),
            'price-high' => $filtered->sortByDesc('price'),
            'bedrooms' => $filtered->sortByDesc('bedrooms'),
            default => $filtered->sortByDesc(fn (Property $property) => $property->published_at?->timestamp ?? $property->created_at->timestamp),
        };

        return view('store.sale', [
            'properties' => $filtered->values(),
            'filters' => $filters,
            'locations' => $properties->pluck('area')->unique()->sort()->values(),
            'types' => $properties->pluck('type')->unique()->sort()->values(),
        ]);
    }

    public function commercial(Request $request, ?string $area = null, ?string $rentPeriod = null)
    {
        $properties = Property::published()->where('is_commercial', true)->get();
        $areaName = $area ? $this->valueFromSlug($properties->pluck('area')->unique(), $area) : '';
        $rentPeriodName = $rentPeriod ? $this->valueFromSlug($properties->pluck('rent_period')->unique(), $rentPeriod) : '';
        $filters = [
            'location' => $areaName ?: trim((string) $request->query('location', '')),
            'rent_period' => $rentPeriodName ?: trim((string) $request->query('rent_period', '')),
            'listing_type' => trim((string) $request->query('listing_type', '')),
            'max_price' => trim((string) $request->query('max_price', '')),
            'sort' => trim((string) $request->query('sort', 'newest')),
        ];

        $filtered = $properties
            ->when($filters['location'] !== '', fn ($items) => $items->where('area', $filters['location']))
            ->when($filters['rent_period'] !== '', fn ($items) => $items->where('rent_period', $filters['rent_period']))
            ->when($filters['listing_type'] !== '', fn ($items) => $items->where('listing_type', $filters['listing_type']))
            ->when($filters['max_price'] !== '', fn ($items) => $items->filter(fn ($property) => $property['price'] <= (int) $filters['max_price']));

        $filtered = match ($filters['sort']) {
            'price-low' => $filtered->sortBy('price'),
            'price-high' => $filtered->sortByDesc('price'),
            default => $filtered->sortByDesc(fn (Property $property) => $property->published_at?->timestamp ?? $property->created_at->timestamp),
        };

        return view('store.commercial', [
            'properties' => $filtered->values(),
            'filters' => $filters,
            'locations' => $properties->pluck('area')->unique()->sort()->values(),
        ]);
    }

    public function property(string $slug)
    {
        $property = Property::published()->where('slug', $slug)->firstOrFail();

        $related = Property::published()
            ->whereKeyNot($property->getKey())
            ->where('listing_type', $property->listing_type)
            ->where('is_commercial', $property->is_commercial)
            ->where('type', $property->type)
            ->latest('published_at')->take(3)->get();

        return view('store.property', compact('property', 'related'));
    }

    public function about()
    {
        return view('store.about');
    }

    public function contact(Request $request)
    {
        return view('store.contact', [
            'interest' => $request->query('interest', ''),
            'message' => $request->query('property')
                ? 'I am interested in '.$request->query('property').'. Please contact me to arrange a viewing.'
                : '',
        ]);
    }

    private function valueFromSlug(Collection $values, string $slug): string
    {
        return (string) $values->first(fn ($value) => Str::slug($value) === $slug, '');
    }
}
