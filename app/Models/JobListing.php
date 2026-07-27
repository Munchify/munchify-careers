<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class JobListing extends Model
{
    use HasFactory;

    protected $fillable = [
        'ulid', 'title', 'department_id', 'type', 'location', 'location_detail',
        'description', 'requirements', 'responsibilities', 'salary_range', 'slots',
        'status', 'application_deadline', 'pipeline_template_id', 'screening_questions',
        'requires_cv', 'requires_video', 'video_prompt', 'hiring_manager_id',
        'applications_count', 'published_at', 'closed_at', 'created_by',
    ];

    protected function casts(): array
    {
        return [
            'screening_questions' => 'array',
            'requires_cv' => 'boolean',
            'requires_video' => 'boolean',
            'application_deadline' => 'date',
            'published_at' => 'datetime',
            'closed_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($job) {
            if (!$job->ulid) {
                $job->ulid = strtolower((string) Str::ulid());
            }
        });

        static::created(function ($job) {
            if ($job->pipelineTemplate) {
                foreach ($job->pipelineTemplate->stages as $stage) {
                    $job->pipelineStages()->create([
                        'stage_id' => $stage->id,
                        'name' => $stage->name,
                        'sort_order' => $stage->sort_order,
                    ]);
                }
            }
        });
    }

    // Relationships
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    public function pipelineTemplate()
    {
        return $this->belongsTo(PipelineTemplate::class);
    }

    public function hiringManager()
    {
        return $this->belongsTo(User::class, 'hiring_manager_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function pipelineStages()
    {
        return $this->hasMany(JobPipelineStage::class)->orderBy('sort_order');
    }

    public function applications()
    {
        return $this->hasMany(Application::class);
    }

    public function assignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function assignedUsers()
    {
        return $this->belongsToMany(User::class, 'job_assignments')->withPivot('role')->withTimestamps();
    }

    // Scopes
    public function scopePublished($query)
    {
        return $query->where('status', 'published');
    }

    public function scopeActive($query)
    {
        return $query->whereIn('status', ['draft', 'published']);
    }

    // Accessors
    public function getTypeLabelAttribute(): string
    {
        return match($this->type) {
            'full_time' => 'Full-time',
            'part_time' => 'Part-time',
            'contract' => 'Contract',
            'internship' => 'Internship',
            default => ucfirst($this->type),
        };
    }

    public function getLocationLabelAttribute(): string
    {
        return match($this->location) {
            'on_site' => 'On-site',
            'remote' => 'Remote',
            'hybrid' => 'Hybrid',
            default => ucfirst($this->location),
        };
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'draft' => 'badge-gray',
            'published' => 'badge-green',
            'closed' => 'badge-orange',
            'archived' => 'badge-gray',
            default => 'badge-gray',
        };
    }

    public function isPublished(): bool
    {
        return $this->status === 'published';
    }

    public function isOpen(): bool
    {
        if ($this->status !== 'published') return false;
        if ($this->application_deadline && $this->application_deadline->isPast()) return false;
        return true;
    }

    public function getFirstStage()
    {
        return $this->pipelineStages()->orderBy('sort_order')->first();
    }
}
