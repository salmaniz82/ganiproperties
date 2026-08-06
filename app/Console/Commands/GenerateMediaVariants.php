<?php

namespace App\Console\Commands;

use App\Models\Media;
use App\Services\ImageVariantService;
use Illuminate\Console\Command;

class GenerateMediaVariants extends Command
{
    protected $signature = 'media:generate-variants {--force : Replace variants that already exist}';

    protected $description = 'Generate WebP and thumbnail variants for existing raster images';

    public function handle(ImageVariantService $imageVariants): int
    {
        $generated = 0;
        $skipped = 0;

        Media::query()
            ->whereIn('mime_type', ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])
            ->orderBy('id')
            ->chunkById(50, function ($mediaItems) use ($imageVariants, &$generated, &$skipped) {
                foreach ($mediaItems as $media) {
                    if (! $this->option('force') && $media->webp_path && $media->thumbnail_path) {
                        $skipped++;
                        continue;
                    }

                    $variants = $imageVariants->generate($media->disk, $media->path, true);
                    $media->update(array_filter($variants));
                    $generated += (int) ($variants['webp_path'] !== null || $variants['thumbnail_path'] !== null);
                }
            });

        Media::flushVariantCache();
        $this->info("Generated variants for {$generated} media item(s); skipped {$skipped} already complete item(s).");

        return self::SUCCESS;
    }
}
