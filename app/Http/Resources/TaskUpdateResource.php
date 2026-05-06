<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskUpdateResource extends JsonResource
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
            'task_id' => $this->task_id,
            'user_id' => $this->user_id,
            'old_status' => $this->old_status,
            'new_status' => $this->new_status,
            'comment' => $this->comment,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'user' => UserResource::make($this->whenLoaded('user')),
        ];
    }
}
