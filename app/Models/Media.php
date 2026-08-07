<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Media extends Model
{
    protected $guarded = [];

    private static array $variantCache = [];

    public function user() { return $this->belongsTo(User::class); }

    public function getUrlAttribute(): string
    {
        return '/'.ltrim($this->asset_path, '/');
    }

    public function getAssetPathAttribute(): string
    {
        return 'storage/'.$this->path;
    }

    public function getWebpUrlAttribute(): ?string
    {
        return $this->webp_path ? '/storage/'.ltrim($this->webp_path, '/') : null;
    }

    public function getThumbnailUrlAttribute(): string
    {
        return '/storage/'.ltrim($this->thumbnail_path ?: $this->webp_path ?: $this->path, '/');
    }

    public static function variantAssetPath(?string $assetPath, string $variant = 'webp'): ?string
    {
        if (! $assetPath || ! str_starts_with($assetPath, 'storage/')) {
            return $assetPath;
        }

        $path = substr($assetPath, 8);
        if (! array_key_exists($path, self::$variantCache)) {
            $media = self::query()->where('path', $path)->first(['path', 'webp_path', 'thumbnail_path']);
            self::$variantCache[$path] = $media ? [
                'webp' => $media->webp_path,
                'thumbnail' => $media->thumbnail_path,
            ] : null;
        }

        $variantPath = self::$variantCache[$path][$variant] ?? null;

        return $variantPath ? 'storage/'.$variantPath : $assetPath;
    }

    public static function primeVariantCache(iterable $assetPaths): void
    {
        $paths = collect($assetPaths)
            ->filter(fn ($path) => is_string($path) && str_starts_with($path, 'storage/'))
            ->map(fn ($path) => substr($path, 8))
            ->unique()->values();

        $missing = $paths->reject(fn ($path) => array_key_exists($path, self::$variantCache));
        foreach ($missing as $path) {
            self::$variantCache[$path] = null;
        }

        if ($missing->isEmpty()) {
            return;
        }

        self::query()->whereIn('path', $missing)->get(['path', 'webp_path', 'thumbnail_path'])
            ->each(function (Media $media) {
                self::$variantCache[$media->path] = [
                    'webp' => $media->webp_path,
                    'thumbnail' => $media->thumbnail_path,
                ];
            });
    }

    public static function flushVariantCache(): void
    {
        self::$variantCache = [];
    }
}
