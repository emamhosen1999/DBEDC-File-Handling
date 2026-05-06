<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LetterResource extends JsonResource
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
            'reference' => $this->reference,
            'title' => $this->title,
            'description' => $this->description,
            'sender' => $this->sender,
            'recipient' => $this->recipient,
            'subject' => $this->subject,
            'letter_date' => $this->letter_date?->format('Y-m-d'),
            'due_date' => $this->due_date?->format('Y-m-d'),
            'priority' => $this->priority,
            'status' => $this->status,
            'department_id' => $this->department_id,
            'assigned_to' => $this->assigned_to,
            'stakeholder_id' => $this->stakeholder_id,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at?->format('Y-m-d H:i:s'),
            'updated_at' => $this->updated_at?->format('Y-m-d H:i:s'),
            'department' => DepartmentResource::make($this->whenLoaded('department')),
            'assignedTo' => UserResource::make($this->whenLoaded('assignedTo')),
            'stakeholder' => StakeholderResource::make($this->whenLoaded('stakeholder')),
            'createdBy' => UserResource::make($this->whenLoaded('createdBy')),
            'tasks' => TaskResource::collection($this->whenLoaded('tasks')),
        ];
    }
}
