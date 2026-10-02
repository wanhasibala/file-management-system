<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class File extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'folder_id',
        'department_id',
        'user_id',
        'title',
        'file_name',
        'file_path',
        'mime_type',
        'file_size',
        'extension',
        'description',
        'download_count',
    ];

    protected $appends = [
        'formatted_size',
        'is_previewable',
    ];

    /**
     * Folder relation.
     */
    public function folder(): BelongsTo
    {
        return $this->belongsTo(Folder::class);
    }

    /**
     * Department relation.
     */
    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Uploader relation.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Formatted file size string.
     */
    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->file_size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];

        for ($i = 0; $bytes > 1024 && $i < count($units) - 1; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2) . ' ' . $units[$i];
    }

    /**
     * Check if file is previewable in browser.
     */
    public function getIsPreviewableAttribute(): bool
    {
        $mime = strtolower((string) $this->mime_type);
        $ext = strtolower((string) $this->extension);

        if (str_starts_with($mime, 'image/')) {
            return true;
        }

        if ($mime === 'application/pdf' || $ext === 'pdf') {
            return true;
        }

        if (str_starts_with($mime, 'text/') || in_array($ext, ['txt', 'json', 'csv', 'md', 'html', 'xml', 'log'])) {
            return true;
        }

        return false;
    }
}
