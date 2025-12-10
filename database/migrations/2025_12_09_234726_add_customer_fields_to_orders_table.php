<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            // Foreign keys only - no duplicate data
            $table->foreignUuid('customer_id')->nullable()->after('product_id')->constrained('customers');
            $table->foreignUuid('unknown_customer_id')->nullable()->after('customer_id')->constrained('unknown_customers');
            $table->foreignUuid('billing_address_id')->nullable()->after('unknown_customer_id')->constrained('addresses');
            $table->foreignUuid('shipping_address_id')->nullable()->after('billing_address_id')->constrained('addresses');
        });
    }

    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table): void {
            $table->dropForeign(['customer_id']);
            $table->dropForeign(['unknown_customer_id']);
            $table->dropForeign(['billing_address_id']);
            $table->dropForeign(['shipping_address_id']);

            $table->dropColumn([
                'customer_id',
                'unknown_customer_id',
                'billing_address_id',
                'shipping_address_id',
            ]);
        });
    }
};
