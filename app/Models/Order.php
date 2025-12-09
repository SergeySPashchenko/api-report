<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Order extends Model
{
    /** @use HasFactory<OrderFactory> **/
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'brand_id',
        'product_id',
        'Agent',
        'Created',
        'OrderDate',
        'OrderNum',
        'OrderN',
        'ProductTotal',
        'GrandTotal',
        'Shipping',
        'PaymentGateway',
        'ShippingMethod',
        'Refund',
        'RefundAmount',
    ];

    /** @return BelongsTo<Brand, $this> */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class, 'brand_id', 'id');
    }

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'Created' => 'timestamp',
            'OrderDate' => 'date',
        ];
    }
}
