<?php

namespace App\Http\Controllers;

use App\Models\Application;
use Illuminate\Http\Request;

class StatusTrackerController extends Controller
{
    public function show($ulid)
    {
        $application = Application::with([
            'jobListing.pipelineStages.pipelineStage',
            'communications' => function ($q) {
                $q->latest();
            }
        ])->where('ulid', $ulid)->firstOrFail();

        $job = $application->jobListing;
        $stages = $job->pipelineStages;

        return view('public.status-tracker', compact('application', 'job', 'stages'));
    }
}
