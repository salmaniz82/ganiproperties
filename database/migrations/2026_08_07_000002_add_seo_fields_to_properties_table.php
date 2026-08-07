<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('properties', function (Blueprint $table) {
            if (! Schema::hasColumn('properties', 'meta_title')) {
                $table->string('meta_title', 180)->nullable()->after('features');
            }
            if (! Schema::hasColumn('properties', 'meta_description')) {
                $table->text('meta_description')->nullable()->after('meta_title');
            }
            if (! Schema::hasColumn('properties', 'meta_keywords')) {
                $table->text('meta_keywords')->nullable()->after('meta_description');
            }
            if (! Schema::hasColumn('properties', 'seo_schema')) {
                $table->json('seo_schema')->nullable()->after('meta_keywords');
            }
        });
    }

    public function down(): void
    {
        $columns = collect(['meta_title', 'meta_description', 'meta_keywords', 'seo_schema'])
            ->filter(fn (string $column) => Schema::hasColumn('properties', $column))->all();

        if ($columns !== []) {
            Schema::table('properties', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
