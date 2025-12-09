<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ExpensesFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

final class Expenses extends Model
{
    /** @use HasFactory<ExpensesFactory> **/
    use HasFactory;

    use HasUlids;
    use LogsActivity;
    use SoftDeletes;

    protected $fillable = [
        'external_id',
        'ExpenseID',
        'ProductID',
        'ExpenseDate',
        'Expense',
        'product_id',
        'brand_id',
        'expense_type_id',
    ];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    /** @return BelongsTo<ExpenseType, $this> */
    public function expenseType(): BelongsTo
    {
        return $this->belongsTo(ExpenseType::class, 'expense_type_id', 'id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('expense_types')
            ->logFillable()
            ->logOnlyDirty();
    }

    protected function casts(): array
    {
        return [
            'ExpenseDate' => 'date',
        ];
    }
}
