<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserResource extends JsonResource
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
            'name' => $this->name,
            'email' => $this->email,
            'department_id' => $this->department_id,
            'role' => $this->role,
            'phone' => $this->phone,
            'avatar_url' => $this->avatar_url,
            'is_active' => $this->is_active,
            'email_verified_at' => $this->email_verified_at?->format('Y-m-d H:i:s'),
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'preferences' => UserPreferenceResource::collection($this->whenLoaded('preferences')),
        ];
    }
}
