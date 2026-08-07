<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('wishlists');

        if (Schema::hasTable('order_items')) {
            $columns = collect(['product_id', 'product_variant_id'])
                ->filter(fn (string $column) => Schema::hasColumn('order_items', $column))->all();
            if ($columns !== []) {
                Schema::table('order_items', fn (Blueprint $table) => $table->dropColumn($columns));
            }
        }

        Schema::dropIfExists('product_variants');
        Schema::dropIfExists('products');
        Schema::dropIfExists('categories');

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // Product catalog removal is intentionally irreversible.
    }
};
