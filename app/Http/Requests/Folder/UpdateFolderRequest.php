<?php

namespace App\Http\Requests\Folder;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFolderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() && $this->user()->isAdmin();
    }

    public function rules(): array
    {
        $folder = $this->route('folder');
        $folderId = is_object($folder) ? $folder->id : $folder;

        return [
            'name' => ['required', 'string', 'max:255'],
            'parent_id' => [
                'nullable',
                'exists:folders,id',
                function ($attribute, $value, $fail) use ($folder, $folderId) {
                    if ($value && (int) $value === (int) $folderId) {
                        $fail('A folder cannot be its own parent.');
                    }
                    if ($value && is_object($folder)) {
                        $descendantIds = $folder->getAllDescendantIds();
                        if (in_array((int) $value, $descendantIds, true)) {
                            $fail('A folder cannot be moved inside its own descendant subfolder.');
                        }
                    }
                },
            ],
            'description' => ['nullable', 'string', 'max:1000'],
            'color' => ['nullable', 'string', 'max:30'],
        ];
    }
}
