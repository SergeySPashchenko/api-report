<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('order_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUuid('order_id')->constrained('orders');
            $table->foreignUuid('product_item_id')->constrained('product_items');
            $table->integer('idOrderItem')->index();
            $table->bigInteger('OrderID');
            $table->string('ItemID');
            $table->float('Price');
            $table->string('Qty');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('order_items');
    }
};
