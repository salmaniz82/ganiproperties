<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        foreach (['shipments', 'payments', 'order_items', 'orders', 'addresses', 'delivery_zones', 'promotions'] as $table) {
            Schema::dropIfExists($table);
        }

        DB::table('users')->where('is_admin', false)->delete();

        Schema::enableForeignKeyConstraints();
    }

    public function down(): void
    {
        // This destructive commerce cleanup is intentionally irreversible.
    }
};
