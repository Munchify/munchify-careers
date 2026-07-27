<?php

namespace App\Http\Controllers;

use App\Services\AnalyticsService;
use App\Models\Application;
use App\Models\Interview;
use App\Models\JobListing;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    protected AnalyticsService $analytics;

    public function __construct(AnalyticsService $analytics)
    {
        $this->analytics = $analytics;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $period = $request->input('period', '30_days');

        // Overview metrics card values
        $stats = $this->analytics->getOverview($period);

        // Fetch recent applications (Hiring managers & interviewers only see assigned jobs)
        $recentAppsQuery = Application::with(['jobListing', 'currentStage'])
            ->latest();

        $upcomingInterviewsQuery = Interview::with(['application.jobListing', 'interviewer', 'stage'])
            ->where('status', 'scheduled')
            ->where('scheduled_at', '>=', Carbon::now())
            ->orderBy('scheduled_at');

        if (!$user->canManageJobs()) {
            // Apply job assignment scope
            $assignedJobIds = $user->jobAssignments()->pluck('job_listing_id');
            $recentAppsQuery->whereIn('job_listing_id', $assignedJobIds);
            $upcomingInterviewsQuery->whereHas('application', function ($q) use ($assignedJobIds) {
                $q->whereIn('job_listing_id', $assignedJobIds);
            });
        }

        $recentApps = $recentAppsQuery->take(5)->get();
        $upcomingInterviews = $upcomingInterviewsQuery->take(5)->get();

        // Funnel chart data
        $funnelData = $this->analytics->getFunnelData($period);

        // Jobs overview
        $jobsQuery = JobListing::withCount('applications')->latest();
        if (!$user->canManageJobs()) {
            $assignedJobIds = $user->jobAssignments()->pluck('job_listing_id');
            $jobsQuery->whereIn('id', $assignedJobIds);
        }
        $jobs = $jobsQuery->take(5)->get();

        return view('dashboard.overview', compact('stats', 'recentApps', 'upcomingInterviews', 'funnelData', 'jobs', 'period'));
    }
}
