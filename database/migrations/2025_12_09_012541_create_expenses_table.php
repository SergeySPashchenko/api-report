<?php

declare(strict_types=1);

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('expenses', function (Blueprint $table): void {
            $table->ulid('id')->primary();
            $table->integer('external_id')->unique(); // Maps to JSON 'id'
            $table->integer('ExpenseID'); // Maps to JSON 'ExpenseID' (not unique)
            $table->integer('ProductID');
            $table->date('ExpenseDate');
            $table->string('Expense');
            $table->foreignUuid('product_id')->nullable()->index()->constrained('products'); // Made nullable in case sync fails to find local product
            $table->foreignUuid('brand_id')->nullable()->index()->constrained('brands');
            $table->foreignUuid('expense_type_id')->nullable()->index()->constrained('expense_types');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('expenses');
    }
};
