<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUuid('brand_id')->nullable()->constrained('brands');
            $table->foreignUuid('product_id')->nullable()->constrained('products');
            $table->integer('external_id')->index();
            $table->string('Agent')->index();
            $table->timestamp('Created');
            $table->date('OrderDate');
            $table->string('OrderNum')->index();
            $table->string('OrderN')->index();
            $table->float('ProductTotal');
            $table->float('GrandTotal');
            $table->string('Shipping')->nullable();
            $table->string('PaymentGateway')->nullable();
            $table->string('ShippingMethod')->nullable();
            $table->string('Refund')->nullable()->index();
            $table->float('RefundAmount')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
