<?php

namespace App\Http\Requests\Department;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateDepartmentRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $department = $this->route('department');
        $departmentId = is_object($department) ? $department->id : $department;

        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('departments', 'name')->ignore($departmentId),
            ],
            'code' => [
                'required',
                'string',
                'max:20',
                Rule::unique('departments', 'code')->ignore($departmentId),
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:30'],
        ];
    }
}
