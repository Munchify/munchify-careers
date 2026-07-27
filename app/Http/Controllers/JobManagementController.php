<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Models\Department;
use App\Models\PipelineTemplate;
use App\Models\User;
use App\Models\JobAssignment;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class JobManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $query = JobListing::with(['department', 'hiringManager'])->withCount('applications');

        if (!$user->canManageJobs()) {
            $assignedIds = $user->jobAssignments()->pluck('job_listing_id');
            $query->whereIn('id', $assignedIds);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('title', 'like', "%{$search}%");
        }

        $jobs = $query->latest()->paginate(10);

        return view('dashboard.jobs.index', compact('jobs'));
    }

    public function show(JobListing $job)
    {
        // Load pipeline stages and applicants
        $job->load(['pipelineStages.pipelineStage', 'department', 'hiringManager']);
        
        $stages = $job->pipelineStages;
        
        // Fetch candidates grouped by current stage
        $applications = $job->applications()
            ->with(['scores', 'notes'])
            ->orderBy('is_starred', 'desc')
            ->latest()
            ->get()
            ->groupBy('current_stage_id');

        return view('dashboard.jobs.show', compact('job', 'stages', 'applications'));
    }

    public function create()
    {
        $departments = Department::where('is_active', true)->get();
        $templates = PipelineTemplate::with('stages')->get();
        $hiringManagers = User::whereIn('role', ['admin', 'hr_manager', 'hiring_manager'])->where('is_active', true)->get();
        $teamMembers = User::where('is_active', true)->get();

        return view('dashboard.jobs.create', compact('departments', 'templates', 'hiringManagers', 'teamMembers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'location' => 'required|in:on_site,remote,hybrid',
            'location_detail' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'slots' => 'required|integer|min:1',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'pipeline_template_id' => 'required|exists:pipeline_templates,id',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requires_cv' => 'nullable|boolean',
            'requires_video' => 'nullable|boolean',
            'requires_photo' => 'nullable|boolean',
            'video_prompt' => 'required_if:requires_video,1|nullable|string',
            'hiring_manager_id' => 'required|exists:users,id',
            'screening_questions' => 'nullable|array',
            'assigned_team' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $screeningQuestions = [];
            if ($request->filled('screening_questions')) {
                foreach ($request->input('screening_questions') as $q) {
                    if (!empty($q['question'])) {
                        $screeningQuestions[] = [
                            'question' => $q['question'],
                            'type' => $q['type'],
                            'knockout' => isset($q['knockout']) && $q['knockout'] == '1',
                            'expected' => $q['expected'] ?? null,
                            'min' => $q['min'] ?? null,
                        ];
                    }
                }
            }

            $job = JobListing::create([
                'title' => $validated['title'],
                'department_id' => $validated['department_id'],
                'type' => $validated['type'],
                'location' => $validated['location'],
                'location_detail' => $validated['location_detail'] ?? null,
                'salary_range' => $validated['salary_range'] ?? null,
                'slots' => $validated['slots'],
                'application_deadline' => $validated['application_deadline'] ?? null,
                'pipeline_template_id' => $validated['pipeline_template_id'],
                'description' => $validated['description'] ?? 'Position opening at Munchify Careers.',
                'requirements' => $validated['requirements'] ?? 'No specific requirements listed.',
                'responsibilities' => $validated['responsibilities'] ?? 'As assigned by department supervisor.',
                'requires_cv' => isset($validated['requires_cv']) && $validated['requires_cv'],
                'requires_video' => isset($validated['requires_video']) && $validated['requires_video'],
                'video_prompt' => $validated['video_prompt'] ?? null,
                'hiring_manager_id' => $validated['hiring_manager_id'],
                'screening_questions' => $screeningQuestions,
                'status' => 'draft',
                'created_by' => Auth::id(),
            ]);

            // Save team assignments
            if ($request->filled('assigned_team')) {
                foreach ($request->input('assigned_team') as $userId => $role) {
                    if (!empty($role)) {
                        JobAssignment::create([
                            'job_listing_id' => $job->id,
                            'user_id' => $userId,
                            'role' => $role,
                        ]);
                    }
                }
            }

            // Always assign hiring manager as coordinator
            JobAssignment::firstOrCreate([
                'job_listing_id' => $job->id,
                'user_id' => $validated['hiring_manager_id']
            ], [
                'role' => 'hiring_manager'
            ]);

            AuditLog::log(
                actorId: Auth::id(),
                action: 'job_created',
                entityType: JobListing::class,
                entityId: $job->id,
                details: ['title' => $job->title]
            );

            return redirect()->route('jobs.manage')->with('success', 'Job listing created successfully as a draft.');
        });
    }

    public function edit($id)
    {
        $job = JobListing::with('assignments')->findOrFail($id);
        $departments = Department::where('is_active', true)->get();
        $templates = PipelineTemplate::all();
        $hiringManagers = User::whereIn('role', ['admin', 'hr_manager', 'hiring_manager'])->where('is_active', true)->get();
        $teamMembers = User::where('is_active', true)->get();

        $assignments = $job->assignments->pluck('role', 'user_id')->toArray();

        return view('dashboard.jobs.edit', compact('job', 'departments', 'templates', 'hiringManagers', 'teamMembers', 'assignments'));
    }

    public function update(Request $request, $id)
    {
        $job = JobListing::findOrFail($id);

        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'department_id' => 'required|exists:departments,id',
            'type' => 'required|in:full_time,part_time,contract,internship',
            'location' => 'required|in:on_site,remote,hybrid',
            'location_detail' => 'nullable|string|max:255',
            'salary_range' => 'nullable|string|max:100',
            'slots' => 'required|integer|min:1',
            'application_deadline' => 'nullable|date|after_or_equal:today',
            'description' => 'nullable|string',
            'requirements' => 'nullable|string',
            'responsibilities' => 'nullable|string',
            'requires_cv' => 'nullable|boolean',
            'requires_video' => 'nullable|boolean',
            'requires_photo' => 'nullable|boolean',
            'video_prompt' => 'required_if:requires_video,1|nullable|string',
            'hiring_manager_id' => 'required|exists:users,id',
            'screening_questions' => 'nullable|array',
            'assigned_team' => 'nullable|array',
        ]);

        return DB::transaction(function () use ($validated, $request, $job) {
            $screeningQuestions = [];
            if ($request->filled('screening_questions')) {
                foreach ($request->input('screening_questions') as $q) {
                    if (!empty($q['question'])) {
                        $screeningQuestions[] = [
                            'question' => $q['question'],
                            'type' => $q['type'],
                            'knockout' => isset($q['knockout']) && $q['knockout'] == '1',
                            'expected' => $q['expected'] ?? null,
                            'min' => $q['min'] ?? null,
                        ];
                    }
                }
            }

            $job->update([
                'title' => $validated['title'],
                'department_id' => $validated['department_id'],
                'type' => $validated['type'],
                'location' => $validated['location'],
                'location_detail' => $validated['location_detail'] ?? null,
                'salary_range' => $validated['salary_range'] ?? null,
                'slots' => $validated['slots'],
                'application_deadline' => $validated['application_deadline'] ?? null,
                'description' => $validated['description'] ?? 'Position opening at Munchify Careers.',
                'requirements' => $validated['requirements'] ?? 'No specific requirements listed.',
                'responsibilities' => $validated['responsibilities'] ?? 'As assigned by department supervisor.',
                'requires_cv' => isset($validated['requires_cv']) && $validated['requires_cv'],
                'requires_video' => isset($validated['requires_video']) && $validated['requires_video'],
                'video_prompt' => $validated['video_prompt'] ?? null,
                'hiring_manager_id' => $validated['hiring_manager_id'],
                'screening_questions' => $screeningQuestions,
            ]);

            // Sync assignments
            JobAssignment::where('job_listing_id', $job->id)->delete();
            if ($request->filled('assigned_team')) {
                foreach ($request->input('assigned_team') as $userId => $role) {
                    if (!empty($role)) {
                        JobAssignment::create([
                            'job_listing_id' => $job->id,
                            'user_id' => $userId,
                            'role' => $role,
                        ]);
                    }
                }
            }

            // Always assign hiring manager
            JobAssignment::firstOrCreate([
                'job_listing_id' => $job->id,
                'user_id' => $validated['hiring_manager_id']
            ], [
                'role' => 'hiring_manager'
            ]);

            AuditLog::log(
                actorId: Auth::id(),
                action: 'job_updated',
                entityType: JobListing::class,
                entityId: $job->id,
                details: ['title' => $job->title]
            );

            return redirect()->route('jobs.manage')->with('success', 'Job listing updated successfully.');
        });
    }

    public function updateStatus(Request $request, $id)
    {
        $job = JobListing::findOrFail($id);
        $status = $request->input('status');

        if (!in_array($status, ['draft', 'published', 'closed', 'archived'])) {
            return back()->withErrors(['status' => 'Invalid status option.']);
        }

        $updateData = ['status' => $status];
        if ($status === 'published') {
            $updateData['published_at'] = Carbon::now();
            $updateData['closed_at'] = null;
        } elseif ($status === 'closed') {
            $updateData['closed_at'] = Carbon::now();
        }

        $job->update($updateData);

        AuditLog::log(
            actorId: Auth::id(),
            action: 'job_status_changed',
            entityType: JobListing::class,
            entityId: $job->id,
            details: ['status' => $status]
        );

        return back()->with('success', 'Job status updated to ' . ucfirst($status) . '.');
    }

    public function duplicate($id)
    {
        $job = JobListing::findOrFail($id);

        $newJob = DB::transaction(function () use ($job) {
            $clone = $job->replicate(['applications_count', 'published_at', 'closed_at']);
            $clone->title = $job->title . ' (Copy)';
            $clone->status = 'draft';
            $clone->created_by = Auth::id();
            $clone->save(); // This triggers creation model event copying the stages!

            // Duplicate assignments
            foreach ($job->assignments as $assign) {
                JobAssignment::create([
                    'job_listing_id' => $newJob->id ?? $clone->id,
                    'user_id' => $assign->user_id,
                    'role' => $assign->role,
                ]);
            }

            return $clone;
        });

        return redirect()->route('jobs.manage')->with('success', "Job duplicated successfully as '{$newJob->title}'.");
    }
}
