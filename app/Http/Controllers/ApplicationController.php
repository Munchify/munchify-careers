<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\JobPipelineStage;
use App\Models\ApplicationScore;
use App\Models\ApplicationNote;
use App\Models\Communication;
use App\Models\AuditLog;
use App\Services\ApplicationService;
use App\Services\SmsService;
use App\Services\WhatsAppService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ApplicationController extends Controller
{
    protected ApplicationService $appService;

    public function __construct(ApplicationService $appService)
    {
        $this->appService = $appService;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        $jobs = JobListing::active();
        if (!$user->canManageJobs()) {
            $assignedJobIds = $user->jobAssignments()->pluck('job_listing_id');
            $jobs->whereIn('id', $assignedJobIds);
        }
        $jobs = $jobs->get();

        $query = Application::with(['jobListing', 'currentStage']);

        if (!$user->canManageJobs()) {
            $assignedJobIds = $user->jobAssignments()->pluck('job_listing_id');
            $query->whereIn('job_listing_id', $assignedJobIds);
        }

        // Filtering
        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('full_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('application_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('job_id')) {
            $query->where('job_listing_id', $request->input('job_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($request->filled('source')) {
            $query->where('source', $request->input('source'));
        }

        if ($request->boolean('starred')) {
            $query->where('is_starred', true);
        }

        $applications = $query->latest()->paginate(15);

        return view('dashboard.applications.index', compact('applications', 'jobs'));
    }

    public function show(Application $application)
    {
        $application->load([
            'jobListing.pipelineStages.pipelineStage',
            'scores.user',
            'scores.stage',
            'notes.user',
            'stageLogs.fromStage',
            'stageLogs.toStage',
            'stageLogs.changedBy',
            'interviews.interviewer',
            'interviews.stage',
            'communications.sentBy'
        ]);

        $job = $application->jobListing;
        $stages = $job->pipelineStages;
        
        // Check if user has already scored candidate in this current stage
        $existingScore = ApplicationScore::where('application_id', $application->id)
            ->where('user_id', Auth::id())
            ->where('stage_id', $application->current_stage_id)
            ->first();

        return view('dashboard.applications.show', compact('application', 'job', 'stages', 'existingScore'));
    }

    public function moveStage(Request $request, Application $application)
    {
        $validated = $request->validate([
            'stage_id' => 'required|exists:job_pipeline_stages,id',
            'note' => 'nullable|string|max:500',
        ]);

        $newStage = JobPipelineStage::findOrFail($validated['stage_id']);

        // Check if new stage belongs to this job listing
        if ($newStage->job_listing_id !== $application->job_listing_id) {
            return back()->withErrors(['stage_id' => 'Invalid stage.']);
        }

        $this->appService->moveStage($application, $newStage, $validated['note']);

        return back()->with('success', 'Candidate moved to stage: ' . $newStage->name);
    }

    public function submitScore(Request $request, Application $application)
    {
        $validated = $request->validate([
            'score' => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string|max:1000',
            'recommendation' => 'required|in:strong_yes,yes,maybe,no,strong_no',
        ]);

        ApplicationScore::updateOrCreate([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'stage_id' => $application->current_stage_id,
        ], [
            'score' => $validated['score'],
            'notes' => $validated['notes'],
            'recommendation' => $validated['recommendation'],
        ]);

        $application->recalculateScore();

        AuditLog::log(
            actorId: Auth::id(),
            action: 'candidate_scored',
            entityType: Application::class,
            entityId: $application->id,
            details: ['score' => $validated['score'], 'recommendation' => $validated['recommendation']]
        );

        return back()->with('success', 'Evaluation score submitted successfully.');
    }

    public function submitNote(Request $request, Application $application)
    {
        $validated = $request->validate([
            'body' => 'required|string|max:2000',
            'is_private' => 'required|boolean',
        ]);

        ApplicationNote::create([
            'application_id' => $application->id,
            'user_id' => Auth::id(),
            'body' => $validated['body'],
            'is_private' => $validated['is_private'],
        ]);

        AuditLog::log(
            actorId: Auth::id(),
            action: 'candidate_note_added',
            entityType: Application::class,
            entityId: $application->id,
            details: ['is_private' => $validated['is_private']]
        );

        return back()->with('success', 'Note added successfully.');
    }

    public function toggleStar(Application $application)
    {
        $application->update([
            'is_starred' => !$application->is_starred
        ]);

        return response()->json([
            'success' => true,
            'is_starred' => $application->is_starred
        ]);
    }

    public function hire(Request $request, Application $application)
    {
        $this->appService->hire($application);
        return back()->with('success', 'Candidate successfully hired!');
    }

    public function reject(Request $request, Application $application)
    {
        $validated = $request->validate([
            'rejection_reason' => 'required|string|max:1000',
        ]);

        $this->appService->reject($application, $validated['rejection_reason']);
        return back()->with('success', 'Candidate application rejected.');
    }

    public function export(Request $request)
    {
        $user = Auth::user();
        $query = Application::with(['jobListing', 'currentStage']);

        if (!$user->canManageJobs()) {
            $assignedIds = $user->jobAssignments()->pluck('job_listing_id');
            $query->whereIn('job_listing_id', $assignedIds);
        }

        if ($request->filled('job_id')) {
            $query->where('job_listing_id', $request->input('job_id'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $applications = $query->latest()->get();

        $headers = [
            'Content-type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename=applications_export_' . Carbon::now()->format('Y-m-d') . '.csv',
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        $callback = function () use ($applications) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['Application ID', 'Number', 'Job Title', 'Candidate Name', 'Email', 'Phone', 'Stage', 'Status', 'Score', 'Source', 'Applied Date']);

            foreach ($applications as $app) {
                fputcsv($file, [
                    $app->id,
                    $app->application_number,
                    $app->jobListing->title ?? 'N/A',
                    $app->full_name,
                    $app->email,
                    $app->phone,
                    $app->currentStage->name ?? 'N/A',
                    ucfirst($app->status),
                    $app->overall_score ?? 'N/A',
                    $app->source_label,
                    $app->created_at->format('Y-m-d H:i:s'),
                ]);
            }

            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    public function bulkAction(Request $request)
    {
        $validated = $request->validate([
            'ids' => 'required|array',
            'ids.*' => 'exists:applications,id',
            'bulk_action' => 'required|in:move,reject,message',
            'stage_id' => 'required_if:bulk_action,move|nullable|exists:job_pipeline_stages,id',
            'rejection_reason' => 'required_if:bulk_action,reject|nullable|string|max:1000',
            'message_channel' => 'required_if:bulk_action,message|nullable|in:sms,whatsapp',
            'message_text' => 'required_if:bulk_action,message|nullable|string|max:1000',
        ]);

        $ids = $validated['ids'];
        $action = $validated['bulk_action'];

        // Enforce job access controls on each ID
        $user = Auth::user();
        $applications = Application::whereIn('id', $ids)->get();

        foreach ($applications as $app) {
            if (!$user->canAccessJob($app->jobListing)) {
                abort(403, 'Unauthorized bulk action. Access restricted.');
            }
        }

        DB::transaction(function () use ($applications, $action, $validated) {
            if ($action === 'move') {
                $newStage = JobPipelineStage::findOrFail($validated['stage_id']);
                foreach ($applications as $app) {
                    // Make sure the stage is valid for each application's job
                    if ($newStage->job_listing_id === $app->job_listing_id) {
                        $this->appService->moveStage($app, $newStage, 'Bulk stage movement.');
                    }
                }
            } elseif ($action === 'reject') {
                foreach ($applications as $app) {
                    if ($app->status === 'active') {
                        $this->appService->reject($app, $validated['rejection_reason']);
                    }
                }
            } elseif ($action === 'message') {
                $channel = $validated['message_channel'];
                $msgText = $validated['message_text'];

                $sms = app(SmsService::class);
                $whatsapp = app(WhatsAppService::class);

                foreach ($applications as $app) {
                    if ($channel === 'sms') {
                        $sms->send($app->phone, $msgText, $app->id);
                    } else {
                        $whatsapp->send($app->phone, $msgText, $app->id);
                    }
                }
            }
        });

        return back()->with('success', 'Bulk action completed successfully.');
    }
}
