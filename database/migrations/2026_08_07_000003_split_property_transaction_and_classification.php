<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        $needsListingType = ! Schema::hasColumn('properties', 'listing_type');
        $needsCommercial = ! Schema::hasColumn('properties', 'is_commercial');

        if ($needsListingType || $needsCommercial) {
            Schema::table('properties', function (Blueprint $table) use ($needsListingType, $needsCommercial) {
                if ($needsListingType) {
                    $table->string('listing_type', 20)->default('rent')->index()->after('intent');
                }
                if ($needsCommercial) {
                    $table->boolean('is_commercial')->default(false)->index()->after('listing_type');
                }
            });
        }

        DB::table('properties')->where('intent', 'commercial')->update(['is_commercial' => true]);
        DB::table('properties')->where(fn ($query) => $query
            ->where('intent', 'buy')->orWhere('status', 'like', '%sale%'))
            ->update(['listing_type' => 'sale']);
    }

    public function down(): void
    {
        $columns = collect(['listing_type', 'is_commercial'])
            ->filter(fn (string $column) => Schema::hasColumn('properties', $column))->all();
        if ($columns !== []) {
            Schema::table('properties', fn (Blueprint $table) => $table->dropColumn($columns));
        }
    }
};
