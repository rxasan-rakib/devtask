<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Reminder extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id', 'title', 'description', 'type',
        'client_id', 'task_id', 'remind_at',
        'notify_email', 'notify_browser',
        'is_sent', 'sent_at', 'is_recurring', 'recur_interval',
        'is_completed', 'completed_at',
        'email_sent', 'email_sent_at', 'notified_at',
    ];

    protected $casts = [
        'remind_at'      => 'datetime',
        'sent_at'        => 'datetime',
        'completed_at'   => 'datetime',
        'email_sent_at'  => 'datetime',
        'notified_at'    => 'datetime',
        'notify_email'   => 'boolean',
        'notify_browser' => 'boolean',
        'is_sent'        => 'boolean',
        'is_completed'   => 'boolean',
        'email_sent'     => 'boolean',
        'is_recurring'   => 'boolean',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function task()
    {
        return $this->belongsTo(Task::class);
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('is_completed', false)->where('is_sent', false);
    }

    public function scopeDone($query)
    {
        return $query->where(function($q) {
            $q->where('is_completed', true)->orWhere('is_sent', true);
        });
    }

    public function getIsDoneAttribute(): bool
    {
        return (bool) ($this->is_completed || $this->is_sent);
    }

    public function getIsOverdueAttribute(): bool
    {
        return !$this->is_done && $this->remind_at && $this->remind_at->isPast();
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'meeting'         => 'Meeting',
            'domain_expiry'   => 'Domain Expiry',
            'hosting_renewal' => 'Hosting Renewal',
            'task_deadline'   => 'Task Deadline',
            'client_followup' => 'Client Follow-up',
            'system_update'   => 'System Update',
            default           => 'Custom',
        };
    }

    public function getTypeIconAttribute(): string
    {
        return match($this->type) {
            'meeting'         => 'users',
            'domain_expiry'   => 'globe',
            'hosting_renewal' => 'server',
            'task_deadline'   => 'clock',
            'client_followup' => 'user',
            'system_update'   => 'refresh-cw',
            default           => 'bell',
        };
    }
}
