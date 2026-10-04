<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AppNotification extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'app_notifications';

    protected $fillable = [
        'user_id',
        'type',
        'title',
        'message',
        'action_url',
        'icon',
        'color',
        'data',
        'read_at',
    ];

    protected $casts = [
        'data'    => 'array',
        'read_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeUnread($query)
    {
        return $query->whereNull('read_at');
    }

    public function scopeRead($query)
    {
        return $query->whereNotNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function markAsRead(): void
    {
        if (is_null($this->read_at)) {
            $this->update(['read_at' => now()]);
        }
    }

    public function markAsUnread(): void
    {
        if (!is_null($this->read_at)) {
            $this->update(['read_at' => null]);
        }
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'reminder'      => 'Reminder Alert',
            'task_deadline' => 'Task Overdue',
            'task_assigned' => 'Task Assigned',
            'task_status'   => 'Task Status',
            'client'        => 'Client Activity',
            'system'        => 'System Alert',
            default         => ucwords(str_replace('_', ' ', $this->type ?? 'Notification')),
        };
    }

    public function getTypeBadgeColorAttribute(): string
    {
        return match($this->type) {
            'reminder'      => '#ff7828',
            'task_deadline' => '#ef4444',
            'task_assigned' => '#6366f1',
            'task_status'   => '#06b6d4',
            'client'        => '#10b981',
            'system'        => '#8b5cf6',
            default         => '#64748b',
        };
    }

    public function getDefaultIconAttribute(): string
    {
        if ($this->icon) {
            return $this->icon;
        }

        return match($this->type) {
            'reminder'      => 'bell-ring',
            'task_deadline' => 'alert-triangle',
            'task_assigned' => 'user-check',
            'task_status'   => 'activity',
            'client'        => 'building-2',
            'system'        => 'info',
            default         => 'bell',
        };
    }

    /**
     * Convenient static dispatcher for creating notifications.
     */
    public static function send(
        int $userId,
        string $type,
        string $title,
        ?string $message = null,
        ?string $actionUrl = null,
        ?string $icon = null,
        ?string $color = null,
        ?array $data = []
    ): self {
        return self::create([
            'user_id'    => $userId,
            'type'       => $type,
            'title'      => $title,
            'message'    => $message,
            'action_url' => $actionUrl,
            'icon'       => $icon ?? 'bell',
            'color'      => $color ?? '#6366f1',
            'data'       => $data,
            'read_at'    => null,
        ]);
    }
}
