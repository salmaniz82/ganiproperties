<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class Product extends Model {
    protected $guarded = [];
    protected $casts = ['is_active'=>'boolean','is_featured'=>'boolean','track_inventory'=>'boolean','variant_same_pricing'=>'boolean','customization_schema'=>'array','seo_schema'=>'array','gallery_images'=>'array','variant_options'=>'array'];
    public function category() { return $this->belongsTo(Category::class); }
    public function variants() { return $this->hasMany(ProductVariant::class); }
    public function getAvailableStockAttribute(): int { return $this->stock - $this->reserved_stock; }
    public function getEffectivePriceAttribute(): int { return (int) ($this->discount_price ?: $this->price); }
    public function getFormattedPriceAttribute(): string { return 'PKR '.number_format($this->effective_price); }
    public function getThumbnailImageAttribute(): string { return Media::variantAssetPath($this->image, 'thumbnail') ?: 'images/prod-arch.jpg'; }
    public function getDisplayImageAttribute(): string { return Media::variantAssetPath($this->image) ?: 'images/prod-arch.jpg'; }
    public function getGallerySourcesAttribute(): array {
        return collect([$this->image, ...($this->gallery_images ?? [])])
            ->filter()->unique()->values()
            ->map(fn (string $path) => [
                'src' => Media::variantAssetPath($path) ?: $path,
                'fallback' => $path,
            ])->all();
    }
}
