<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class StoreFileRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'department_id' => ['required', 'exists:departments,id'],
            'folder_id' => ['nullable', 'exists:folders,id'],
            'file' => ['required', 'file', 'max:51200'], // max 50MB
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'File title is mandatory.',
            'department_id.required' => 'Department selection is mandatory.',
            'file.required' => 'Please select a file to upload.',
            'file.max' => 'File size cannot exceed 50 megabytes.',
        ];
    }
}
