<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobAssignment extends Model
{
    protected $fillable = ['job_listing_id', 'user_id', 'role'];

    public function jobListing()
    {
        return $this->belongsTo(JobListing::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getRoleLabelAttribute(): string
    {
        return match($this->role) {
            'hiring_manager' => 'Hiring Manager',
            'interviewer' => 'Interviewer',
            'reviewer' => 'Reviewer',
            default => ucfirst($this->role),
        };
    }
}
