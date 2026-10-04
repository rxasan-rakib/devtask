<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Task extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'title', 'description', 'client_id', 'task_type_id', 'type',
        'priority', 'status', 'assigned_to', 'created_by',
        'deadline', 'completed_at', 'is_reminder_sent',
    ];

    protected $casts = [
        'deadline'          => 'datetime',
        'completed_at'      => 'datetime',
        'is_reminder_sent'  => 'boolean',
    ];

    // Relationships
    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function taskType()
    {
        return $this->belongsTo(TaskType::class, 'task_type_id');
    }

    public function assignedTo()
    {
        return $this->belongsTo(User::class, 'assigned_to');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function comments()
    {
        return $this->hasMany(TaskComment::class)->latest();
    }

    public function attachments()
    {
        return $this->hasMany(TaskAttachment::class);
    }

    public function logs()
    {
        return $this->hasMany(TaskLog::class)->latest();
    }

    // Accessors / Helpers
    public function getPriorityBadgeAttribute(): string
    {
        return match($this->priority) {
            'high'   => 'danger',
            'medium' => 'warning',
            'low'    => 'success',
            default  => 'secondary',
        };
    }

    public function getStatusBadgeAttribute(): string
    {
        return match($this->status) {
            'pending'       => 'secondary',
            'in_progress'   => 'primary',
            'on_hold'       => 'warning',
            'under_review'  => 'info',
            'completed'     => 'success',
            default         => 'secondary',
        };
    }

    public function getTypeLabelAttribute(): string
    {
        if ($this->taskType) {
            return $this->taskType->name;
        }

        return match($this->type) {
            'software_development' => 'Software / App Development',
            'website_development'  => 'Website Development',
            'bug_fix'              => 'Bug Fix / Debugging',
            'api_integration'      => 'API / Payment Integration',
            'pos_setup'            => 'POS System Setup',
            'pos_customization'    => 'POS Customization',
            'system_update'        => 'System Update',
            'cpanel_maintenance'   => 'cPanel / Server Maintenance',
            'other'                => 'Other / General',
            default                => $this->type ? ucwords(str_replace('_', ' ', $this->type)) : 'General',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return $this->taskType?->icon ?? 'tag';
    }

    public function getTypeColorAttribute(): string
    {
        return $this->taskType?->color ?? '#6366f1';
    }

    public function getIsOverdueAttribute(): bool
    {
        return $this->deadline
            && $this->deadline->isPast()
            && $this->status !== 'completed';
    }
}
