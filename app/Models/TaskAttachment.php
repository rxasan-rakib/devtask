<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TaskAttachment extends Model
{
    protected $fillable = [
        'task_id', 'user_id', 'file_name',
        'file_path', 'file_type', 'file_size',
    ];

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getUrlAttribute(): string
    {
        if ($this->task_id && $this->id) {
            return route('tasks.attachments.view', ['task' => $this->task_id, 'attachment' => $this->id]);
        }
        return asset('storage/' . $this->file_path);
    }

    public function getDownloadUrlAttribute(): string
    {
        if ($this->task_id && $this->id) {
            return route('tasks.attachments.download', ['task' => $this->task_id, 'attachment' => $this->id]);
        }
        return asset('storage/' . $this->file_path);
    }

    public function getDirectStorageUrlAttribute(): string
    {
        return asset('storage/' . $this->file_path);
    }

    public function getIsImageAttribute(): bool
    {
        $mime = strtolower($this->file_type ?? '');
        $ext  = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return str_starts_with($mime, 'image/') || in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg', 'bmp']);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = (int) $this->file_size;
        if ($bytes <= 0) return '0 B';

        $units = ['B', 'KB', 'MB', 'GB'];
        $i = floor(log($bytes, 1024));
        return round($bytes / pow(1024, $i), 1) . ' ' . ($units[$i] ?? 'B');
    }

    public function getIconAttribute(): string
    {
        if ($this->is_image) return 'image';
        $ext = strtolower(pathinfo($this->file_name, PATHINFO_EXTENSION));
        return match($ext) {
            'pdf'           => 'file-text',
            'doc', 'docx'   => 'file-text',
            'xls', 'xlsx'   => 'sheet',
            'zip', 'rar', '7z' => 'archive',
            default         => 'paperclip',
        };
    }
}
