<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;

class StaticFrontendController extends Controller
{
    public function home()
    {
        return view('store.home');
    }

    public function rent(Request $request, ?string $area = null, ?string $type = null)
    {
        $properties = collect($this->properties());
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
            default => $filtered->sortByDesc('reference'),
        };

        return view('store.rent', [
            'properties' => $filtered->values(),
            'filters' => $filters,
            'locations' => $properties->pluck('area')->unique()->sort()->values(),
            'types' => $properties->pluck('type')->unique()->sort()->values(),
        ]);
    }

    public function property(string $slug)
    {
        $property = collect($this->properties())->firstWhere('slug', $slug);
        abort_unless($property, 404);

        $related = collect($this->properties())
            ->where('slug', '!=', $property['slug'])
            ->where('type', $property['type'])
            ->take(3)
            ->values();

        return view('store.property', compact('property', 'related'));
    }

    public function about()
    {
        return view('store.about');
    }

    public function landlords()
    {
        return view('store.landlords');
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

    private function properties(): array
    {
        return collect(config('gani_properties', []))
            ->filter(fn (array $property) => ($property['intent'] ?? null) === 'rent')
            ->values()
            ->all();
    }

    private function valueFromSlug(Collection $values, string $slug): string
    {
        return (string) $values->first(fn ($value) => Str::slug($value) === $slug, '');
    }
}
