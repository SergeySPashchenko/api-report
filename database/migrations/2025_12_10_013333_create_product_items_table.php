<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('product_items', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUuid('product_id')->nullable()->constrained('products');
            $table->integer('ItemID')->index();
            $table->string('ProductName')->index();
            $table->string('SKU')->index();
            $table->integer('Quantity');
            $table->boolean('upSell')->default(false);
            $table->boolean('active')->default(true);
            $table->string('offerProducts')->nullable()->default(null);
            $table->boolean('extraProduct')->default(false);
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('product_items');
    }
};
