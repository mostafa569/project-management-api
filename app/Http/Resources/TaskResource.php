<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TaskResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'details' => $this->details,
            'priority' => $this->priority,
            'is_completed' => $this->is_completed,
            'project' => new ProjectResource($this->whenLoaded('project')),
            'assignees' => UserResource::collection($this->whenLoaded('users')),
            'deleted_at' => $this->deleted_at,
        ];
    }
}