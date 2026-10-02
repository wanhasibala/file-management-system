<?php

namespace App\Http\Requests\File;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFileRequest extends FormRequest
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
            'description' => ['nullable', 'string', 'max:2000'],
            'file' => ['nullable', 'file', 'max:51200'],
        ];
    }
}
