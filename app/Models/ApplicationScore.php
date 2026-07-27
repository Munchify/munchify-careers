<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationScore extends Model
{
    protected $fillable = ['application_id', 'user_id', 'stage_id', 'score', 'notes', 'recommendation'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function stage()
    {
        return $this->belongsTo(JobPipelineStage::class, 'stage_id');
    }

    public function getRecommendationLabelAttribute(): string
    {
        return match($this->recommendation) {
            'strong_yes' => 'Strong Yes',
            'yes' => 'Yes',
            'maybe' => 'Maybe',
            'no' => 'No',
            'strong_no' => 'Strong No',
            default => ucfirst($this->recommendation),
        };
    }

    public function getRecommendationColorAttribute(): string
    {
        return match($this->recommendation) {
            'strong_yes' => 'text-green-700 bg-green-100',
            'yes' => 'text-green-600 bg-green-50',
            'maybe' => 'text-yellow-700 bg-yellow-100',
            'no' => 'text-red-600 bg-red-50',
            'strong_no' => 'text-red-700 bg-red-100',
            default => 'text-gray-600 bg-gray-100',
        };
    }
}
