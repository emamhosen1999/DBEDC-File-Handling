<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DepartmentResource extends JsonResource
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
            'description' => $this->description,
            'parent_id' => $this->parent_id,
            'manager_id' => $this->manager_id,
            'display_order' => $this->display_order,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'manager' => UserResource::make($this->whenLoaded('manager')),
            'parent' => DepartmentResource::make($this->whenLoaded('parent')),
            'children' => DepartmentResource::collection($this->whenLoaded('children')),
            'users' => UserResource::collection($this->whenLoaded('users')),
            'users_count' => $this->whenCounted('users'),
            'letters_count' => $this->whenCounted('letters'),
            'tasks_count' => $this->whenCounted('tasks'),
        ];
    }
}
