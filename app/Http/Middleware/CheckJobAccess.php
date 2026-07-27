<?php

namespace App\Http\Middleware;

use App\Models\JobListing;
use App\Models\Application;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckJobAccess
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if (!$user) {
            return redirect()->route('login');
        }

        $job = null;

        // Try to find the job listing from route parameters
        if ($request->route('job')) {
            $jobParam = $request->route('job');
            if ($jobParam instanceof JobListing) {
                $job = $jobParam;
            } else {
                $job = JobListing::where('id', $jobParam)
                    ->orWhere('ulid', $jobParam)
                    ->first();
            }
        } elseif ($request->route('application')) {
            $appParam = $request->route('application');
            if ($appParam instanceof Application) {
                $job = $appParam->jobListing;
            } else {
                $app = Application::where('id', $appParam)
                    ->orWhere('ulid', $appParam)
                    ->first();
                if ($app) {
                    $job = $app->jobListing;
                }
            }
        }

        // If a job was found, check access. If not found, let it fall through (e.g. 404 handled by Laravel)
        if ($job && !$user->canAccessJob($job)) {
            abort(403, 'Unauthorized access - you are not assigned to this job listing.');
        }

        return $next($request);
    }
}
