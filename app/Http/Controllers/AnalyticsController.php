<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Models\JobListing;
use Illuminate\Http\Request;

class AnalyticsController extends Controller
{
    protected AnalyticsService $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function index(Request $request)
    {
        $period = $request->input('period', '30_days');
        $jobId = $request->filled('job_id') ? (int)$request->input('job_id') : null;

        $stats = $this->analytics->getOverview($period);
        $funnelData = $this->analytics->getFunnelData($period, $jobId);
        $sourceData = $this->analytics->getApplicationsBySource($period);
        $timeToHire = $this->analytics->getTimeToHire($period);
        $jobPerformance = $this->analytics->getJobPerformance($period);
        $teamPerformance = $this->analytics->getTeamPerformance($period);

        // Fetch jobs for filter dropdown
        $jobs = JobListing::published()->get();

        return view('dashboard.analytics.index', compact(
            'stats',
            'funnelData',
            'sourceData',
            'timeToHire',
            'jobPerformance',
            'teamPerformance',
            'jobs',
            'period',
            'jobId'
        ));
    }
}
