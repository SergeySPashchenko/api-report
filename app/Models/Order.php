<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
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
        'customer_id',
        'unknown_customer_id',
        'billing_address_id',
        'shipping_address_id',
        'status',
        'external_id',
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

    /** @return BelongsTo<Customer, $this> */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    /** @return BelongsTo<UnknownCustomer, $this> */
    public function unknownCustomer(): BelongsTo
    {
        return $this->belongsTo(UnknownCustomer::class);
    }

    /** @return BelongsTo<Address, $this> */
    public function billingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'billing_address_id');
    }

    /** @return BelongsTo<Address, $this> */
    public function shippingAddress(): BelongsTo
    {
        return $this->belongsTo(Address::class, 'shipping_address_id');
    }

    /** @return HasMany<OrderItems, $this> */
    public function orderItems(): HasMany
    {
        return $this->hasMany(OrderItems::class, 'order_id', 'id');
    }

    protected function casts(): array
    {
        return [
            'Created' => 'timestamp',
            'OrderDate' => 'date',
        ];
    }
}
