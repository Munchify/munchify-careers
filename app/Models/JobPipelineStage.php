<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class JobPipelineStage extends Model
{
    protected $fillable = ['job_listing_id', 'stage_id', 'name', 'sort_order'];

    public function jobListing()
    {
        return $this->belongsTo(JobListing::class);
    }

    public function pipelineStage()
    {
        return $this->belongsTo(PipelineStage::class, 'stage_id');
    }

    public function applications()
    {
        return $this->hasMany(Application::class, 'current_stage_id');
    }

    public function scores()
    {
        return $this->hasMany(ApplicationScore::class, 'stage_id');
    }

    public function getColorAttribute(): string
    {
        return $this->pipelineStage->color ?? '#3B82F6';
    }

    public function getIsTerminalPassAttribute(): bool
    {
        return $this->pipelineStage->is_terminal_pass ?? false;
    }

    public function getIsTerminalFailAttribute(): bool
    {
        return $this->pipelineStage->is_terminal_fail ?? false;
    }

    public function getAutoNotifyAttribute(): bool
    {
        return $this->pipelineStage->auto_notify_candidate ?? true;
    }
}
