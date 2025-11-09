<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class FinancialAccountResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     */
    public function toArray($request): array
    {
        return [
            'id' => $this->id,
            'parent_id' => $this->parent_id,
            'name' => $this->name,
            'type' => $this->type,
            'balance' => $this->balance,
            'initial_balance' => $this->initial_balance,
            'is_group' => (bool) $this->is_group,
            'is_active' => (bool) $this->is_active,
            'sort_order' => $this->sort_order,
            'level' => $this->level,
            'description' => $this->description,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'children' => self::collection($this->whenLoaded('children')),
        ];
    }
}
