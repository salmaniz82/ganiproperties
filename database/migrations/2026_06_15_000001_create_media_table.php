<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('media', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('disk')->default('public');
            $table->string('path')->unique();
            $table->string('filename');
            $table->string('mime_type', 120);
            $table->unsignedBigInteger('size');
            $table->string('title')->nullable();
            $table->string('alt_text')->nullable();
            $table->text('caption')->nullable();
            $table->timestamps();
            $table->index(['mime_type', 'created_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('media');
    }
};
