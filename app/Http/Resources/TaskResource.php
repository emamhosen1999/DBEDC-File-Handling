<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
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
            'letter_id' => $this->letter_id,
            'title' => $this->title,
            'description' => $this->description,
            'assigned_to' => $this->assigned_to,
            'department_id' => $this->department_id,
            'status' => $this->status,
            'priority' => $this->priority,
            'due_date' => $this->due_date?->format('Y-m-d'),
            'completed_at' => $this->completed_at?->format('Y-m-d H:i:s'),
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'letter' => LetterResource::make($this->whenLoaded('letter')),
            'assignedTo' => UserResource::make($this->whenLoaded('assignedTo')),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'createdBy' => UserResource::make($this->whenLoaded('createdBy')),
            'updates' => TaskUpdateResource::collection($this->whenLoaded('updates')),
        ];
    }
}
