<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrderItemsFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

final class OrderItems extends Model
{
    /** @use HasFactory<OrderItemsFactory> **/
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'order_id',
        'product_item_id',
        'idOrderItem',
        'OrderID',
        'ItemID',
        'Price',
        'Qty',
    ];

    /** @return BelongsTo<Order, $this> */
    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'order_id', 'id');
    }

    /** @return BelongsTo<ProductItem, $this> */
    public function productItem(): BelongsTo
    {
        return $this->belongsTo(ProductItem::class, 'product_item_id', 'id');
    }
}
