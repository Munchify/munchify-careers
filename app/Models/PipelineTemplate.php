<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PipelineTemplate extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'department_hint', 'is_default'];

    protected function casts(): array
    {
        return ['is_default' => 'boolean'];
    }

    public function stages()
    {
        return $this->hasMany(PipelineStage::class)->orderBy('sort_order');
    }

    public function jobListings()
    {
        return $this->hasMany(JobListing::class);
    }
}
