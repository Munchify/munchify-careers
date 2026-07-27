<?php

namespace App\Http\Controllers;

use App\Models\Interview;
use App\Models\Application;
use App\Models\User;
use App\Models\AuditLog;
use App\Services\NotificationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;

class InterviewController extends Controller
{
    protected NotificationService $notifier;

    public function __construct(NotificationService $notifier)
    {
        $this->notifier = $notifier;
    }

    public function index(Request $request)
    {
        $user = Auth::user();
        
        $query = Interview::with(['application.jobListing', 'interviewer', 'stage']);

        // Check scope
        if (!$user->canManageJobs()) {
            $assignedJobIds = $user->jobAssignments()->pluck('job_listing_id');
            $query->whereHas('application', function ($q) use ($assignedJobIds) {
                $q->whereIn('job_listing_id', $assignedJobIds);
            });
        }

        // Search / Filter
        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        $interviews = $query->orderBy('scheduled_at', 'desc')->paginate(15);

        // Fetch users who can interview for the scheduling dropdown
        $interviewers = User::whereIn('role', ['admin', 'hr_manager', 'hiring_manager', 'interviewer'])->where('is_active', true)->get();

        // Calendar events structure for custom monthly grid view
        $calendarInterviews = $query->clone()
            ->where('scheduled_at', '>=', Carbon::now()->startOfMonth()->subDays(7))
            ->where('scheduled_at', '<=', Carbon::now()->endOfMonth()->addDays(7))
            ->get();

        return view('dashboard.interviews.index', compact('interviews', 'interviewers', 'calendarInterviews'));
    }

    public function schedule(Request $request)
    {
        $validated = $request->validate([
            'application_id' => 'required|exists:applications,id',
            'scheduled_at' => 'required|date|after:now',
            'duration_minutes' => 'required|integer|min:15|max:180',
            'type' => 'required|in:phone,video,on_site',
            'location_or_link' => 'required|string|max:500',
            'interviewer_id' => 'required|exists:users,id',
            'notes' => 'nullable|string|max:1000',
        ]);

        $app = Application::findOrFail($validated['application_id']);
        
        // Enforce job access controls
        if (!Auth::user()->canAccessJob($app->jobListing)) {
            abort(403, 'Unauthorized scheduling request.');
        }

        return DB::transaction(function () use ($validated, $app) {
            $interview = Interview::create([
                'application_id' => $validated['application_id'],
                'stage_id' => $app->current_stage_id,
                'interviewer_id' => $validated['interviewer_id'],
                'scheduled_at' => Carbon::parse($validated['scheduled_at']),
                'duration_minutes' => $validated['duration_minutes'],
                'type' => $validated['type'],
                'location_or_link' => $validated['location_or_link'],
                'notes' => $validated['notes'],
                'status' => 'scheduled',
            ]);

            AuditLog::log(
                actorId: Auth::id(),
                action: 'interview_scheduled',
                entityType: Interview::class,
                entityId: $interview->id,
                details: ['scheduled_at' => $interview->scheduled_at->toDateTimeString()]
            );

            // Send dynamic Meta WhatsApp & SMS template notification
            $formattedTime = $interview->scheduled_at->format('M d, Y \a\t H:i');
            $typeLabel = match($interview->type) {
                'phone' => 'Phone Call',
                'video' => 'Google Meet / Video Call',
                'on_site' => 'On-site Interview (Munchify Maseno Kitchen)',
                default => ucfirst($interview->type)
            };

            $this->notifier->notifyCandidate($app, 'interview_scheduled', [
                'scheduled_at' => $formattedTime,
                'type' => $typeLabel,
                'details' => $interview->location_or_link,
            ]);

            return redirect()->back()->with('success', 'Interview scheduled and candidate notified.');
        });
    }

    public function submitFeedback(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        // Enforce access control
        if (!Auth::user()->canAccessJob($interview->application->jobListing)) {
            abort(403, 'Unauthorized feedback request.');
        }

        $validated = $request->validate([
            'notes' => 'required|string|max:2000',
            'status' => 'required|in:completed,cancelled,no_show',
        ]);

        $interview->update([
            'notes' => $validated['notes'],
            'status' => $validated['status'],
        ]);

        AuditLog::log(
            actorId: Auth::id(),
            action: 'interview_feedback_submitted',
            entityType: Interview::class,
            entityId: $interview->id,
            details: ['status' => $validated['status']]
        );

        return redirect()->back()->with('success', 'Interview feedback updated successfully.');
    }

    public function updateStatus(Request $request, $id)
    {
        $interview = Interview::findOrFail($id);

        if (!Auth::user()->canAccessJob($interview->application->jobListing)) {
            return response()->json(['error' => 'Unauthorized.'], 403);
        }

        $validated = $request->validate([
            'status' => 'required|in:scheduled,completed,cancelled,no_show',
        ]);

        $interview->update([
            'status' => $validated['status'],
        ]);

        AuditLog::log(
            actorId: Auth::id(),
            action: 'interview_status_changed',
            entityType: Interview::class,
            entityId: $interview->id,
            details: ['status' => $validated['status']]
        );

        return response()->json(['success' => true, 'status' => $interview->status]);
    }
}
