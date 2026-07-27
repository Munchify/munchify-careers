<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'pipeline_template_id', 'name', 'description', 'color', 'sort_order',
        'is_terminal_pass', 'is_terminal_fail', 'auto_notify_candidate',
        'notification_template_sms', 'notification_template_whatsapp',
    ];

    protected function casts(): array
    {
        return [
            'is_terminal_pass' => 'boolean',
            'is_terminal_fail' => 'boolean',
            'auto_notify_candidate' => 'boolean',
        ];
    }

    public function template()
    {
        return $this->belongsTo(PipelineTemplate::class, 'pipeline_template_id');
    }

    public function jobPipelineStages()
    {
        return $this->hasMany(JobPipelineStage::class, 'stage_id');
    }

    public function isTerminal(): bool
    {
        return $this->is_terminal_pass || $this->is_terminal_fail;
    }
}
