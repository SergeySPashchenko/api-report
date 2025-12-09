<?php

declare(strict_types=1);

namespace App\Http\Resources;

use App\Models\ExpenseType;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ExpenseType */
final class ExpenseTypeResource extends JsonResource
{
    /** @return array<string, mixed> */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'ExpenseID' => $this->ExpenseID,
            'Name' => $this->Name,
            'slug' => $this->slug,
            'Visible' => $this->Visible,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
