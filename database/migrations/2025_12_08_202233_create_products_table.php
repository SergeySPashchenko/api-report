<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->foreignUlid('brand_id')->nullable()->constrained('brands');
            $table->integer('ProductID')->index();
            $table->string('Product');
            $table->string('ProductName');
            $table->boolean('newSystem')->default(false);
            $table->boolean('Visible')->default(true);
            $table->boolean('flyer')->default(false);
            $table->string('slug');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
