<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ApplicationStageLog extends Model
{
    protected $fillable = ['application_id', 'from_stage_id', 'to_stage_id', 'changed_by', 'note'];

    public function application()
    {
        return $this->belongsTo(Application::class);
    }

    public function fromStage()
    {
        return $this->belongsTo(JobPipelineStage::class, 'from_stage_id');
    }

    public function toStage()
    {
        return $this->belongsTo(JobPipelineStage::class, 'to_stage_id');
    }

    public function changedBy()
    {
        return $this->belongsTo(User::class, 'changed_by');
    }
}
