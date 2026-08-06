<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class ProductVariant extends Model {
    protected $guarded = [];
    protected $casts = ['options'=>'array'];
    public function product() { return $this->belongsTo(Product::class); }
    public function getEffectivePriceAttribute(): int { return (int) ($this->discount_price ?: $this->price); }
}
