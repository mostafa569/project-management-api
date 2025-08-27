<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'nullable|exists:projects,id',
            'title' => 'nullable|string|max:255',
            'details' => 'nullable|string',
            'priority' => 'nullable|in:low,medium,high',
            'is_completed' => 'boolean',
            'assignee_ids' => 'nullable|array',  
            'assignee_ids.*' => 'exists:users,id',
        ];
    }
}