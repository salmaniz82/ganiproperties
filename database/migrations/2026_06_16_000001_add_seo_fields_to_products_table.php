<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->text('meta_description')->nullable()->after('description');
            $table->text('meta_keywords')->nullable()->after('meta_description');
            $table->json('seo_schema')->nullable()->after('meta_keywords');
        });
    }

    public function down(): void
    {
        Schema::table('products', function (Blueprint $table) {
            $table->dropColumn(['meta_description', 'meta_keywords', 'seo_schema']);
        });
    }
};
