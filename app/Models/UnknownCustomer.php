<?php

declare(strict_types=1);

namespace App\Models;

use Database\Factories\UnknownCustomerFactory;
use Illuminate\Database\Eloquent\Concerns\HasUlids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

final class UnknownCustomer extends Model
{
    /** @use HasFactory<UnknownCustomerFactory> */
    use HasFactory;

    use HasUlids;
    use SoftDeletes;

    protected $fillable = [
        'address_hash',
        'city',
        'state',
        'zip',
        'country',
    ];

    /**
     * Generate hash from address components
     */
    public static function generateAddressHash(string $city, ?string $state, ?string $zip, ?string $country): string
    {
        return md5(mb_strtolower(mb_trim($city.'|'.$state.'|'.$zip.'|'.$country)));
    }

    /**
     * Find or create unknown customer by address hash
     */
    public static function findOrCreateByAddressHash(string $city, ?string $state, ?string $zip, ?string $country): Model
    {
        $hash = self::generateAddressHash($city, $state, $zip, $country);

        return self::query()->firstOrCreate(['address_hash' => $hash], [
            'city' => $city,
            'state' => $state,
            'zip' => $zip,
            'country' => $country,
        ]);
    }

    /** @return HasMany<Order, $this> */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /** @return HasMany<Address, $this> */
    public function addresses(): HasMany
    {
        return $this->hasMany(Address::class);
    }
}
