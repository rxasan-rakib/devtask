<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Client extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'name', 'phone', 'email', 'address',
        'company_name', 'system_url', 'type', 'notes',
        'is_active', 'created_by',
    ];

    public function getFormattedSystemUrlAttribute(): ?string
    {
        if (!$this->system_url) return null;
        if (!preg_match("~^(?:f|ht)tps?://~i", $this->system_url)) {
            return "https://" . $this->system_url;
        }
        return $this->system_url;
    }

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }

    public function reminders()
    {
        return $this->hasMany(Reminder::class);
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'pos'     => 'POS System',
            'website' => 'Website',
            'both'    => 'POS + Website',
            default   => 'Other',
        };
    }
}
