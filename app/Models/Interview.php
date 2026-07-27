<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Interview extends Model
{
    protected $fillable = [
        'application_id', 'stage_id', 'interviewer_id', 'scheduled_at',
        'duration_minutes', 'type', 'location_or_link', 'status',
        'feedback_submitted', 'notes',
    ];

    protected function casts(): array
    {
        return [
            'scheduled_at' => 'datetime',
            'feedback_submitted' => 'boolean',
        ];
    }

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function stage()
    {
        return $this->belongsTo(JobPipelineStage::class, 'stage_id');
    }

    public function interviewer()
    {
        return $this->belongsTo(User::class, 'interviewer_id');
    }

    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'in_person' => 'In-Person',
            'phone' => 'Phone',
            'video' => 'Video',
            default => ucfirst($this->type),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'scheduled' => 'badge-blue',
            'completed' => 'badge-green',
            'cancelled' => 'badge-gray',
            'no_show' => 'badge-red',
            default => 'badge-gray',
        };
    }

    public function scopeUpcoming($query)
    {
        return $query->where('scheduled_at', '>=', now())
                     ->where('status', 'scheduled')
                     ->orderBy('scheduled_at');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('scheduled_at', today())
                     ->where('status', 'scheduled');
    }
}
