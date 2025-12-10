<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\UnknownCustomer;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Carbon;

/**
 * @property int $id
 * @property string $address_hash
 * @property string|null $city
 * @property string|null $state
 * @property string|null $zip
 * @property string|null $country
 * @property Carbon $created_at
 * @property Carbon $updated_at
 *
 * @mixin UnknownCustomer
 */
final class UnknownCustomerResource extends JsonResource
{
    /**
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'address_hash' => $this->address_hash,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

            // Counts
            'orders_count' => $this->whenCounted('orders'),
            'addresses_count' => $this->whenCounted('addresses'),
        ];
    }
}
