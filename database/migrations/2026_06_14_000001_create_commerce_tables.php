<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('parent_id')->nullable()->constrained('categories')->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->boolean('is_active')->default(true);
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();
        });

        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->foreignId('category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('slug')->unique();
            $table->string('sku')->unique();
            $table->text('description')->nullable();
            $table->string('image')->nullable();
            $table->unsignedBigInteger('price');
            $table->unsignedBigInteger('compare_at_price')->nullable();
            $table->integer('stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->boolean('track_inventory')->default(true);
            $table->boolean('is_active')->default(true);
            $table->boolean('is_featured')->default(false);
            $table->json('customization_schema')->nullable();
            $table->timestamps();
            $table->index(['is_active', 'is_featured']);
        });

        Schema::create('product_variants', function (Blueprint $table) {
            $table->id();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('sku')->unique();
            $table->unsignedBigInteger('price');
            $table->integer('stock')->default(0);
            $table->integer('reserved_stock')->default(0);
            $table->json('options')->nullable();
            $table->timestamps();
        });

        Schema::create('delivery_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('city')->index();
            $table->unsignedBigInteger('fee')->default(0);
            $table->boolean('same_day_enabled')->default(false);
            $table->time('same_day_cutoff')->nullable();
            $table->unsignedInteger('minimum_days')->default(1);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('label')->default('Home');
            $table->string('recipient');
            $table->string('phone');
            $table->string('line1');
            $table->string('line2')->nullable();
            $table->string('city');
            $table->string('postal_code')->nullable();
            $table->timestamps();
        });

        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('number')->unique();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('delivery_zone_id')->nullable()->constrained()->nullOnDelete();
            $table->string('customer_name');
            $table->string('customer_email')->nullable()->index();
            $table->string('customer_phone')->index();
            $table->json('shipping_address');
            $table->unsignedBigInteger('subtotal');
            $table->unsignedBigInteger('delivery_fee')->default(0);
            $table->unsignedBigInteger('discount_total')->default(0);
            $table->unsignedBigInteger('total');
            $table->string('currency', 3)->default('PKR');
            $table->string('status')->default('pending');
            $table->string('payment_status')->default('pending');
            $table->string('fulfillment_status')->default('unfulfilled');
            $table->string('payment_method')->default('cod');
            $table->date('delivery_date')->nullable();
            $table->text('customer_note')->nullable();
            $table->text('internal_note')->nullable();
            $table->timestamps();
        });

        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('product_variant_id')->nullable()->constrained()->nullOnDelete();
            $table->string('name');
            $table->string('sku');
            $table->string('image')->nullable();
            $table->unsignedBigInteger('unit_price');
            $table->unsignedInteger('quantity');
            $table->unsignedBigInteger('total');
            $table->json('customizations')->nullable();
            $table->timestamps();
        });

        Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider');
            $table->string('reference')->nullable()->index();
            $table->string('status')->default('pending');
            $table->unsignedBigInteger('amount');
            $table->json('provider_data')->nullable();
            $table->timestamps();
        });

        Schema::create('shipments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->cascadeOnDelete();
            $table->string('provider')->default('internal');
            $table->string('tracking_number')->nullable()->index();
            $table->string('status')->default('pending');
            $table->json('provider_data')->nullable();
            $table->timestamps();
        });

        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('code')->nullable()->unique();
            $table->string('type')->default('fixed');
            $table->unsignedBigInteger('value');
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('wishlists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('product_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
            $table->unique(['user_id', 'product_id']);
        });

        Schema::create('audit_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action');
            $table->string('auditable_type');
            $table->unsignedBigInteger('auditable_id')->nullable();
            $table->json('before')->nullable();
            $table->json('after')->nullable();
            $table->string('ip_address', 45)->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        foreach (['audit_logs','wishlists','promotions','shipments','payments','order_items','orders','addresses','delivery_zones','product_variants','products','categories'] as $table) {
            Schema::dropIfExists($table);
        }
    }
};
