<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\AddressFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class Address extends Model
{
    /** @use HasFactory<AddressFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'customer_id',
        'unknown_customer_id',
        'type',
        'name',
        'address',
        'address2',
        'city',
        'state',
        'zip',
        'country',
        'phone',
        'address_hash',
    ];

    /**
     * Generate hash from address fields
     */
    public static function generateHash(?string $name, ?string $address, ?string $address2, ?string $city, ?string $state, ?string $zip, ?string $country, ?string $phone): string
    {
        $data = mb_strtolower(mb_trim(
            ($name ?? '').'|'.
            ($address ?? '').'|'.
            ($address2 ?? '').'|'.
            ($city ?? '').'|'.
            ($state ?? '').'|'.
            ($zip ?? '').'|'.
            ($country ?? '').'|'.
            ($phone ?? '')
        ));

        return md5($data);
    }

    /**
     * Find existing address by hash for customer
     */
    public static function findByHashForCustomer(int|string|null $customerId, int|string|null $unknownCustomerId, string $hash): ?self
    {
        return self::query()
            ->where('address_hash', $hash)
            ->where(function ($query) use ($customerId, $unknownCustomerId): void {
                if ($customerId) {
                    $query->where('customer_id', $customerId);
                }

                if ($unknownCustomerId) {
                    $query->orWhere('unknown_customer_id', $unknownCustomerId);
                }
            })
            ->first();
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

    /** @return HasMany<Order, $this> */
    public function billingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'billing_address_id');
    }

    /** @return HasMany<Order, $this> */
    public function shippingOrders(): HasMany
    {
        return $this->hasMany(Order::class, 'shipping_address_id');
    }
}
