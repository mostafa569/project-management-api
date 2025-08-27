<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTaskRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'project_id' => 'required|exists:projects,id',
            'title' => 'required|string|max:255',
            'details' => 'nullable|string',
            'priority' => 'required|in:low,medium,high',
            'is_completed' => 'boolean',
            'assignee_ids' => 'nullable|array',  
            'assignee_ids.*' => 'exists:users,id',
        ];
    }
}