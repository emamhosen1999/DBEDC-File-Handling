<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ActivityResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'action' => $this->action,
            'entity_type' => $this->entity_type,
            'entity_id' => $this->entity_id,
            'description' => $this->description,
            'metadata' => $this->metadata,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
