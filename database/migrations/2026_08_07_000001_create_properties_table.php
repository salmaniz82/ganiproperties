<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('properties', function (Blueprint $table) {
            $table->id();
            $table->string('title', 180);
            $table->string('slug', 180)->unique();
            $table->string('reference', 80)->unique();
            $table->string('intent', 30)->index();
            $table->string('listing_type', 20)->default('rent')->index();
            $table->boolean('is_commercial')->default(false)->index();
            $table->string('status', 80);
            $table->string('type', 120);
            $table->string('area', 120)->index();
            $table->string('postcode', 20);
            $table->unsignedBigInteger('price');
            $table->string('rent_period', 30)->nullable();
            $table->unsignedTinyInteger('bedrooms')->default(0);
            $table->unsignedTinyInteger('bathrooms')->default(0);
            $table->unsignedTinyInteger('receptions')->default(0);
            $table->string('tenure', 120)->nullable();
            $table->string('council_tax', 120)->nullable();
            $table->string('epc', 10)->nullable();
            $table->string('floor_area', 120)->nullable();
            $table->string('featured_image', 255)->nullable();
            $table->json('gallery_images')->nullable();
            $table->text('summary');
            $table->json('description')->nullable();
            $table->json('features')->nullable();
            $table->string('meta_title', 180)->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->json('seo_schema')->nullable();
            $table->boolean('is_published')->default(false)->index();
            $table->timestamp('published_at')->nullable();
            $table->timestamps();
        });

        $now = now();
        $properties = collect(config('gani_properties', []))
            ->whereIn('intent', ['rent', 'commercial'])
            ->map(fn (array $row) => [
                'title' => $row['title'],
                'slug' => $row['slug'],
                'reference' => $row['reference'],
                'intent' => $row['intent'],
                'listing_type' => $row['intent'] === 'rent' ? 'rent' : (str_contains(strtolower($row['status']), 'sale') ? 'sale' : 'rent'),
                'is_commercial' => $row['intent'] === 'commercial',
                'status' => $row['status'],
                'type' => $row['type'],
                'area' => $row['area'],
                'postcode' => $row['postcode'],
                'price' => $row['price'],
                'rent_period' => $row['rent_period'] ?? null,
                'bedrooms' => $row['bedrooms'],
                'bathrooms' => $row['bathrooms'],
                'receptions' => $row['receptions'],
                'tenure' => $row['tenure'] ?? null,
                'council_tax' => $row['council_tax'] ?? null,
                'epc' => $row['epc'] ?? null,
                'floor_area' => $row['floor_area'] ?? null,
                'featured_image' => ltrim($row['image'], '/'),
                'gallery_images' => json_encode([]),
                'summary' => $row['summary'],
                'description' => json_encode($row['description'] ?? []),
                'features' => json_encode($row['features'] ?? []),
                'is_published' => true,
                'published_at' => $now,
                'created_at' => $now,
                'updated_at' => $now,
            ])->values()->all();

        if ($properties !== []) {
            DB::table('properties')->insert($properties);
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('properties');
    }
};
