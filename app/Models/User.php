<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'full_name', 'email', 'password', 'role', 'department', 'is_active',
    ];

    protected $hidden = [
        'password', 'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    // Relationships
    public function createdJobs()
    {
        return $this->hasMany(JobListing::class, 'created_by');
    }

    public function managedJobs()
    {
        return $this->hasMany(JobListing::class, 'hiring_manager_id');
    }

    public function jobAssignments()
    {
        return $this->hasMany(JobAssignment::class);
    }

    public function assignedJobs()
    {
        return $this->belongsToMany(JobListing::class, 'job_assignments')->withPivot('role')->withTimestamps();
    }

    public function scores()
    {
        return $this->hasMany(ApplicationScore::class);
    }

    public function notes()
    {
        return $this->hasMany(ApplicationNote::class);
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class, 'interviewer_id');
    }

    public function auditLogs()
    {
        return $this->hasMany(AuditLog::class, 'actor_id');
    }

    // Helpers
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isHrManager(): bool
    {
        return $this->role === 'hr_manager';
    }

    public function isHiringManager(): bool
    {
        return $this->role === 'hiring_manager';
    }

    public function isInterviewer(): bool
    {
        return $this->role === 'interviewer';
    }

    public function isViewer(): bool
    {
        return $this->role === 'viewer';
    }

    public function hasRole(string ...$roles): bool
    {
        return in_array($this->role, $roles);
    }

    public function canManageJobs(): bool
    {
        return $this->hasRole('admin', 'hr_manager');
    }

    public function canMoveStages(): bool
    {
        return $this->hasRole('admin', 'hr_manager');
    }

    public function canAccessJob(JobListing $job): bool
    {
        if ($this->hasRole('admin', 'hr_manager')) {
            return true;
        }
        return $this->jobAssignments()->where('job_listing_id', $job->id)->exists();
    }

    public function getInitialsAttribute(): string
    {
        $parts = explode(' ', $this->full_name);
        $initials = '';
        foreach (array_slice($parts, 0, 2) as $part) {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        return $initials;
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'admin' => 'Admin',
            'hr_manager' => 'HR Manager',
            'hiring_manager' => 'Hiring Manager',
            'interviewer' => 'Interviewer',
            'viewer' => 'Viewer',
            default => ucfirst($this->role),
        };
    }
}
