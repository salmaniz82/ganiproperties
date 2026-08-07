<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class Property extends Model
{
    protected $guarded = [];

    protected function casts(): array
    {
        return [
            'gallery_images' => 'array',
            'description' => 'array',
            'features' => 'array',
            'seo_schema' => 'array',
            'is_published' => 'boolean',
            'is_commercial' => 'boolean',
            'published_at' => 'datetime',
        ];
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('is_published', true);
    }

    public function getImageAttribute(): string
    {
        return '/'.ltrim($this->featured_image ?: 'assets/property-1-ref.jpg', '/');
    }

    public function getGalleryAttribute(): array
    {
        return collect([$this->featured_image, ...($this->gallery_images ?? [])])
            ->filter()->unique()->map(fn (string $path) => '/'.ltrim($path, '/'))->whenEmpty(
                fn ($images) => $images->push('/assets/property-1-ref.jpg')
            )->values()->all();
    }

    public function getPriceLabelAttribute(): string
    {
        $suffix = match ($this->listing_type) {
            'rent' => ' pcm',
            default => '',
        };

        if ($this->listing_type === 'rent' && $this->is_commercial) {
            $suffix = match (strtolower((string) $this->rent_period)) {
                'weekly' => ' pw', 'monthly' => ' pcm', 'quarterly' => ' per quarter', default => ' pa',
            };
        }

        return '&pound;'.number_format($this->price).$suffix;
    }
}
