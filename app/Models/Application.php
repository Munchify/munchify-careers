<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Application extends Model
{
    use HasFactory;

    protected $fillable = [
        'ulid', 'application_number', 'job_listing_id', 'full_name', 'email', 'phone',
        'location', 'current_stage_id', 'status', 'source', 'referral_name',
        'cv_path', 'video_path', 'screening_answers', 'overall_score',
        'is_starred', 'is_knockout', 'rejection_reason', 'cover_letter',
        'current_role', 'experience_years', 'motivation', 'skills',
        'hired_at', 'rejected_at', 'withdrawn_at',
    ];

    protected function casts(): array
    {
        return [
            'screening_answers' => 'array',
            'overall_score' => 'decimal:1',
            'is_starred' => 'boolean',
            'is_knockout' => 'boolean',
            'hired_at' => 'datetime',
            'rejected_at' => 'datetime',
            'withdrawn_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function ($app) {
            if (!$app->ulid) {
                $app->ulid = strtolower((string) Str::ulid());
            }
        });
    }

    // Relationships
    public function jobListing()
    {
        return $this->belongsTo(JobListing::class);
    }

    public function currentStage()
    {
        return $this->belongsTo(JobPipelineStage::class, 'current_stage_id');
    }

    public function scores()
    {
        return $this->hasMany(ApplicationScore::class);
    }

    public function notes()
    {
        return $this->hasMany(ApplicationNote::class)->latest();
    }

    public function stageLogs()
    {
        return $this->hasMany(ApplicationStageLog::class)->latest();
    }

    public function interviews()
    {
        return $this->hasMany(Interview::class)->latest('scheduled_at');
    }

    public function communications()
    {
        return $this->hasMany(Communication::class)->latest();
    }

    // Scopes
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeStarred($query)
    {
        return $query->where('is_starred', true);
    }

    public function scopeForJob($query, $jobId)
    {
        return $query->where('job_listing_id', $jobId);
    }

    // Accessors
    public function getFormattedPhoneAttribute(): string
    {
        $phone = $this->phone;
        if (str_starts_with($phone, '254') && strlen($phone) === 12) {
            return '0' . substr($phone, 3, 3) . ' ' . substr($phone, 6, 3) . ' ' . substr($phone, 9, 3);
        }
        return $phone;
    }

    public function getWhatsAppUrlAttribute(): string
    {
        return 'https://wa.me/' . $this->phone;
    }

    public function getStatusBadgeClassAttribute(): string
    {
        return match($this->status) {
            'active' => 'badge-blue',
            'hired' => 'badge-green',
            'rejected' => 'badge-red',
            'withdrawn' => 'badge-gray',
            default => 'badge-gray',
        };
    }

    public function getSourceLabelAttribute(): string
    {
        return match($this->source) {
            'direct' => 'Direct',
            'referral' => 'Referral',
            'social' => 'Social Media',
            'other' => 'Other',
            default => ucfirst($this->source),
        };
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

    public function getStatusUrlAttribute(): string
    {
        return url('/application/' . $this->ulid . '/status');
    }

    public function recalculateScore(): void
    {
        $avg = $this->scores()->avg('score');
        $this->update(['overall_score' => $avg ? round($avg, 1) : null]);
    }

    // Phone normalization
    public static function normalizePhone(string $phone): string
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);
        if (str_starts_with($phone, '+')) {
            $phone = substr($phone, 1);
        }
        if (str_starts_with($phone, '0') && strlen($phone) === 10) {
            $phone = '254' . substr($phone, 1);
        }
        if (str_starts_with($phone, '7') && strlen($phone) === 9) {
            $phone = '254' . $phone;
        }
        return $phone;
    }
}
