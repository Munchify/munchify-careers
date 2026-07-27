<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\Department;
use Illuminate\Http\Request;

class PublicJobController extends Controller
{
    public function index()
    {
        $latestJobs = JobListing::published()
            ->latest('published_at')
            ->take(3)
            ->get();

        $departments = Department::where('is_active', true)
            ->withCount(['jobListings' => function ($q) {
                $q->published();
            }])
            ->get();

        return view('public.home', compact('latestJobs', 'departments'));
    }

    public function jobs(Request $request)
    {
        $departments = Department::where('is_active', true)->get();
        
        $query = JobListing::published()->with('department');

        // Text Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        // Department filter
        if ($request->filled('department')) {
            $query->where('department_id', $request->input('department'));
        }

        // Type filter
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        // Location filter
        if ($request->filled('location')) {
            $query->where('location', $request->input('location'));
        }

        $jobs = $query->latest('published_at')->paginate(10);

        return view('public.jobs', compact('jobs', 'departments'));
    }

    public function show($ulid)
    {
        $job = JobListing::published()
            ->where('ulid', $ulid)
            ->firstOrFail();

        // Retrieve similar jobs (same department or latest published)
        $similarJobs = JobListing::published()
            ->where('id', '!=', $job->id)
            ->where('department_id', $job->department_id)
            ->take(3)
            ->get();

        if ($similarJobs->isEmpty()) {
            $similarJobs = JobListing::published()
                ->where('id', '!=', $job->id)
                ->latest('published_at')
                ->take(3)
                ->get();
        }

        return view('public.job-detail', compact('job', 'similarJobs'));
    }
}
