<?php

namespace App\Services;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Throwable;

class ImageVariantService
{
    private const THUMBNAIL_SIZE = 480;
    private const WEBP_QUALITY = 82;

    /**
     * @return array{webp_path: ?string, thumbnail_path: ?string}
     */
    public function generate(string $disk, string $path, bool $optimizeWebp = true): array
    {
        $variants = ['webp_path' => null, 'thumbnail_path' => null];

        if (! extension_loaded('gd')) {
            return $variants;
        }

        try {
            $contents = Storage::disk($disk)->get($path);
            $image = @imagecreatefromstring($contents);

            if ($image === false) {
                return $variants;
            }

            try {
                if ($optimizeWebp && function_exists('imagewebp')) {
                    $webpPath = $this->variantPath($path, 'webp', 'webp');
                    if ($this->writeWebp($disk, $webpPath, $image)) {
                        $variants['webp_path'] = $webpPath;
                    }
                }

                $thumbnail = $this->thumbnail($image);
                try {
                    $extension = $optimizeWebp && function_exists('imagewebp')
                        ? 'webp'
                        : $this->thumbnailExtension($path);
                    $thumbnailPath = $this->variantPath($path, 'thumb', $extension);
                    if ($this->writeImage($disk, $thumbnailPath, $thumbnail, $extension)) {
                        $variants['thumbnail_path'] = $thumbnailPath;
                    }
                } finally {
                    imagedestroy($thumbnail);
                }
            } finally {
                imagedestroy($image);
            }
        } catch (Throwable $exception) {
            Log::warning('Image variant generation failed; the original upload will be used.', [
                'disk' => $disk,
                'path' => $path,
                'exception' => $exception->getMessage(),
            ]);
        }

        return $variants;
    }

    private function thumbnail(\GdImage $source): \GdImage
    {
        $width = imagesx($source);
        $height = imagesy($source);
        $scale = min(1, self::THUMBNAIL_SIZE / max($width, $height));
        $targetWidth = max(1, (int) round($width * $scale));
        $targetHeight = max(1, (int) round($height * $scale));
        $thumbnail = imagecreatetruecolor($targetWidth, $targetHeight);

        imagealphablending($thumbnail, false);
        imagesavealpha($thumbnail, true);
        $transparent = imagecolorallocatealpha($thumbnail, 0, 0, 0, 127);
        imagefill($thumbnail, 0, 0, $transparent);
        imagecopyresampled($thumbnail, $source, 0, 0, 0, 0, $targetWidth, $targetHeight, $width, $height);

        return $thumbnail;
    }

    private function writeWebp(string $disk, string $path, \GdImage $image): bool
    {
        ob_start();
        $written = imagewebp($image, null, self::WEBP_QUALITY);
        $contents = ob_get_clean();

        return $written && is_string($contents) && Storage::disk($disk)->put($path, $contents);
    }

    private function writeImage(string $disk, string $path, \GdImage $image, string $extension): bool
    {
        if ($extension === 'webp') {
            return $this->writeWebp($disk, $path, $image);
        }

        ob_start();
        $written = match ($extension) {
            'jpg' => imagejpeg($image, null, 82),
            default => imagepng($image, null, 8),
        };
        $contents = ob_get_clean();

        return $written && is_string($contents) && Storage::disk($disk)->put($path, $contents);
    }

    private function thumbnailExtension(string $path): string
    {
        return in_array(strtolower(pathinfo($path, PATHINFO_EXTENSION)), ['jpg', 'jpeg'], true) ? 'jpg' : 'png';
    }

    private function variantPath(string $path, string $suffix, string $extension): string
    {
        $directory = pathinfo($path, PATHINFO_DIRNAME);
        $filename = pathinfo($path, PATHINFO_FILENAME);

        return ($directory === '.' ? '' : $directory.'/').$filename.'-'.$suffix.'.'.$extension;
    }
}
