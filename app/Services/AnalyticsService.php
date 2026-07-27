<?php

namespace App\Services;

use App\Models\JobListing;
use App\Models\Application;
use App\Models\Interview;
use App\Models\ApplicationScore;
use App\Models\PipelineStage;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Get carbon start date based on period name.
     */
    protected function getStartDate(string $period): ?Carbon
    {
        return match($period) {
            '7_days' => Carbon::now()->subDays(7),
            '30_days' => Carbon::now()->subDays(30),
            '90_days' => Carbon::now()->subDays(90),
            default => null, // All-time
        };
    }

    /**
     * Overview metrics card data.
     */
    public function getOverview(string $period = 'all'): array
    {
        $startDate = $this->getStartDate($period);

        $jobsQuery = JobListing::where('status', 'published');
        $appsQuery = Application::query();
        $interviewsQuery = Interview::where('status', 'scheduled');
        $hiresQuery = Application::where('status', 'hired');

        if ($startDate) {
            $jobsQuery->where('published_at', '>=', $startDate);
            $appsQuery->where('created_at', '>=', $startDate);
            $interviewsQuery->where('scheduled_at', '>=', $startDate);
            $hiresQuery->where('hired_at', '>=', $startDate);
        }

        // Hired last period to compare (if not all-time)
        $previousHires = 0;
        if ($period !== 'all' && $startDate) {
            $prevStart = (clone $startDate)->subDays($startDate->diffInDays(Carbon::now()));
            $previousHires = Application::where('status', 'hired')
                ->where('hired_at', '>=', $prevStart)
                ->where('hired_at', '<', $startDate)
                ->count();
        }

        return [
            'active_jobs' => $jobsQuery->count(),
            'total_applications' => $appsQuery->count(),
            'scheduled_interviews' => $interviewsQuery->count(),
            'hires_count' => $hiresQuery->count(),
            'previous_hires' => $previousHires,
        ];
    }

    /**
     * Pipeline funnel conversion rates.
     */
    public function getFunnelData(string $period = 'all', ?int $jobId = null): array
    {
        $startDate = $this->getStartDate($period);

        // We will sum candidates in stages across the system, or for a specific job.
        // Group by base template stage name to present a clean horizontal bar chart.
        $query = DB::table('applications')
            ->join('job_pipeline_stages', 'applications.current_stage_id', '=', 'job_pipeline_stages.id')
            ->select('job_pipeline_stages.name as stage_name', DB::raw('count(applications.id) as count'))
            ->groupBy('job_pipeline_stages.name');

        if ($startDate) {
            $query->where('applications.created_at', '>=', $startDate);
        }

        if ($jobId) {
            $query->where('applications.job_listing_id', $jobId);
        }

        $results = $query->get()->pluck('count', 'stage_name')->toArray();

        // Get standard order of stages for default representation if needed
        $allStageNames = ['Applied', 'Screening', 'First Interview', 'Technical Panel', 'Offer Phase', 'Hired', 'Rejected'];
        
        $funnel = [];
        foreach ($allStageNames as $name) {
            $funnel[] = [
                'stage' => $name,
                'count' => $results[$name] ?? 0,
            ];
        }

        // Add any stages that were customized/not in standard names
        foreach ($results as $name => $count) {
            if (!in_array($name, $allStageNames)) {
                $funnel[] = [
                    'stage' => $name,
                    'count' => $count,
                ];
            }
        }

        return $funnel;
    }

    /**
     * Applications distribution by referral/direct source.
     */
    public function getApplicationsBySource(string $period = 'all'): array
    {
        $startDate = $this->getStartDate($period);

        $query = Application::select('source', DB::raw('count(id) as count'))
            ->groupBy('source');

        if ($startDate) {
            $query->where('created_at', '>=', $startDate);
        }

        $results = $query->get()->pluck('count', 'source')->toArray();

        return [
            'Direct' => $results['direct'] ?? 0,
            'Referral' => $results['referral'] ?? 0,
            'Social Media' => $results['social'] ?? 0,
            'Other' => $results['other'] ?? 0,
        ];
    }

    /**
     * Average time to hire in days.
     */
    public function getTimeToHire(string $period = 'all'): float
    {
        $startDate = $this->getStartDate($period);

        $query = Application::where('status', 'hired')
            ->whereNotNull('hired_at')
            ->whereNotNull('created_at');

        if ($startDate) {
            $query->where('hired_at', '>=', $startDate);
        }

        // Calculate average diff in days using SQLite-safe date calculations
        $apps = $query->get(['created_at', 'hired_at']);

        if ($apps->isEmpty()) {
            return 0.0;
        }

        $totalDays = 0;
        foreach ($apps as $app) {
            $totalDays += $app->created_at->diffInDays($app->hired_at);
        }

        return round($totalDays / $apps->count(), 1);
    }

    /**
     * Job-specific recruitment metrics.
     */
    public function getJobPerformance(string $period = 'all'): array
    {
        $startDate = $this->getStartDate($period);

        $jobs = JobListing::with(['department', 'hiringManager'])
            ->withCount(['applications' => function ($q) use ($startDate) {
                if ($startDate) $q->where('created_at', '>=', $startDate);
            }])
            ->get();

        $performance = [];

        foreach ($jobs as $job) {
            $hires = $job->applications()
                ->where('status', 'hired')
                ->when($startDate, function($q) use ($startDate) {
                    $q->where('hired_at', '>=', $startDate);
                })
                ->count();

            $rejections = $job->applications()
                ->where('status', 'rejected')
                ->when($startDate, function($q) use ($startDate) {
                    $q->where('rejected_at', '>=', $startDate);
                })
                ->count();

            $conversionRate = $job->applications_count > 0 
                ? round(($hires / $job->applications_count) * 100, 1) 
                : 0.0;

            $performance[] = [
                'id' => $job->id,
                'title' => $job->title,
                'department' => $job->department->name ?? 'N/A',
                'hiring_manager' => $job->hiringManager->full_name ?? 'N/A',
                'applications_count' => $job->applications_count,
                'hires_count' => $hires,
                'rejections_count' => $rejections,
                'conversion_rate' => $conversionRate,
                'status' => $job->status,
            ];
        }

        return $performance;
    }

    /**
     * Team member activity / evaluation metrics.
     */
    public function getTeamPerformance(string $period = 'all'): array
    {
        $startDate = $this->getStartDate($period);

        // Fetch user score counts, notes, etc.
        $query = ApplicationScore::join('users', 'application_scores.user_id', '=', 'users.id')
            ->select('users.full_name', 'users.role', DB::raw('count(application_scores.id) as scores_count'))
            ->groupBy('users.id', 'users.full_name', 'users.role');

        if ($startDate) {
            $query->where('application_scores.created_at', '>=', $startDate);
        }

        return $query->get()->toArray();
    }
}
