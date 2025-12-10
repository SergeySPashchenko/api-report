<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\ProductItemFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class ProductItem extends Model
{
    /** @use HasFactory<ProductItemFactory> **/
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'product_id',
        'ItemID',
        'ProductName',
        'SKU',
        'Quantity',
        'upSell',
        'active',
        'offerProducts',
        'extraProduct',
    ];

    /** @return BelongsTo<Product, $this> */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class, 'product_id', 'id');
    }

    /** @return HasMany<OrderItems, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'product_item_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'upSell' => 'boolean',
            'active' => 'boolean',
            'extraProduct' => 'boolean',
        ];
    }
}
