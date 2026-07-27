<?php

namespace App\Services;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\JobPipelineStage;
use App\Models\ApplicationStageLog;
use App\Models\AuditLog;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class ApplicationService
{
    protected NotificationService $notifier;

    public function __construct(NotificationService $notifier)
    {
        $this->notifier = $notifier;
    }

    /**
     * Submit a candidate application.
     */
    public function submit(JobListing $job, array $data, $cvFile = null, $videoFile = null): Application
    {
        // Normalize phone number
        $normalizedPhone = Application::normalizePhone($data['phone']);

        // Check for duplicates
        if ($this->checkDuplicate($job->id, $normalizedPhone, $data['email'])) {
            throw ValidationException::withMessages([
                'email' => 'You have already applied for this job listing and your application is currently active.',
            ]);
        }

        return DB::transaction(function () use ($job, $data, $normalizedPhone, $cvFile, $videoFile) {
            // Generate sequential application number
            $appNumber = ApplicationNumberService::generate();

            // Set initial stage
            $firstStage = $job->pipelineStages()->orderBy('sort_order')->first();

            // Store files
            $cvPath = is_string($cvFile) ? $cvFile : ($cvFile ? $cvFile->store('cvs', 'public') : null);
            $videoPath = is_string($videoFile) ? $videoFile : ($videoFile ? $videoFile->store('videos', 'public') : null);

            // Screening Answers Processing & Knockout checks
            $screeningAnswers = $data['screening_answers'] ?? [];
            $isKnockout = false;
            $knockoutReason = null;

            if ($job->screening_questions) {
                foreach ($job->screening_questions as $qIndex => $question) {
                    $answer = $screeningAnswers[$qIndex]['answer'] ?? null;
                    
                    if (isset($question['knockout']) && $question['knockout'] == true) {
                        if (isset($question['expected'])) {
                            // Boolean knockout check
                            $expected = (bool)$question['expected'];
                            if ((bool)$answer !== $expected) {
                                $isKnockout = true;
                                $knockoutReason = "Failed knockout question: " . $question['question'];
                            }
                        } elseif (isset($question['min'])) {
                            // Number knockout check
                            $min = (int)$question['min'];
                            if ((int)$answer < $min) {
                                $isKnockout = true;
                                $knockoutReason = "Failed knockout question (below min {$min}): " . $question['question'];
                            }
                        }
                    }
                }
            }

            // Create application
            $app = Application::create([
                'job_listing_id' => $job->id,
                'application_number' => $appNumber,
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'phone' => $normalizedPhone,
                'location' => $data['location'] ?? null,
                'current_stage_id' => $firstStage ? $firstStage->id : null,
                'status' => 'active',
                'source' => $data['source'] ?? 'direct',
                'referral_name' => $data['referral_name'] ?? null,
                'cv_path' => $cvPath,
                'video_path' => $videoPath,
                'screening_answers' => $screeningAnswers,
                'is_starred' => false,
                'is_knockout' => $isKnockout,
                'cover_letter' => $data['cover_letter'] ?? null,
                'current_role' => $data['current_role'] ?? null,
                'experience_years' => $data['experience_years'] ?? null,
                'motivation' => $data['motivation'] ?? null,
                'skills' => $data['skills'] ?? null,
            ]);

            // Increment job listing applications count
            $job->increment('applications_count');

            // Log stage change
            if ($firstStage) {
                ApplicationStageLog::create([
                    'application_id' => $app->id,
                    'from_stage_id' => null,
                    'to_stage_id' => $firstStage->id,
                    'changed_by' => Auth::id() ?? 1, // Default or systemic admin
                    'note' => 'Application submitted.',
                ]);
            }

            // Create Audit Log
            AuditLog::log(
                actorId: Auth::id() ?? 1,
                action: 'application_submitted',
                entityType: Application::class,
                entityId: $app->id,
                details: ['application_number' => $appNumber]
            );

            // Handle Knockout Auto-rejection OR Send Received Notification
            if ($isKnockout) {
                $this->reject($app, "System Auto-Knockout: " . ($knockoutReason ?? 'Failed screening questions.'));
            } else {
                $this->notifier->notifyCandidate($app, 'application_received');
            }

            return $app;
        });
    }

    /**
     * Move an application to a different stage.
     */
    public function moveStage(Application $application, JobPipelineStage $newStage, ?string $note = null): void
    {
        DB::transaction(function () use ($application, $newStage, $note) {
            $fromStageId = $application->current_stage_id;

            $status = 'active';
            $hiredAt = $application->hired_at;
            $rejectedAt = $application->rejected_at;

            // Terminal status checks
            if ($newStage->is_terminal_pass) {
                $status = 'hired';
                $hiredAt = Carbon::now();
            } elseif ($newStage->is_terminal_fail) {
                $status = 'rejected';
                $rejectedAt = Carbon::now();
            }

            // Update application status
            $application->update([
                'current_stage_id' => $newStage->id,
                'status' => $status,
                'hired_at' => $hiredAt,
                'rejected_at' => $rejectedAt,
                'rejection_reason' => $newStage->is_terminal_fail ? ($note ?? $application->rejection_reason) : null,
            ]);

            // Log stage movement
            ApplicationStageLog::create([
                'application_id' => $application->id,
                'from_stage_id' => $fromStageId,
                'to_stage_id' => $newStage->id,
                'changed_by' => Auth::id() ?? 1,
                'note' => $note ?? 'Candidate moved to stage ' . $newStage->name,
            ]);

            // Create Audit Log
            AuditLog::log(
                actorId: Auth::id() ?? 1,
                action: 'application_stage_changed',
                entityType: Application::class,
                entityId: $application->id,
                details: [
                    'from_stage_id' => $fromStageId,
                    'to_stage_id' => $newStage->id,
                    'status' => $status
                ]
            );

            // Fire Auto-Notifications
            if ($newStage->auto_notify) {
                if ($status === 'hired') {
                    $this->notifier->notifyCandidate($application, 'hired');
                } elseif ($status === 'rejected') {
                    $this->notifier->notifyCandidate($application, 'rejected');
                } else {
                    $this->notifier->notifyCandidate($application, 'stage_moved');
                }
            }
        });
    }

    /**
     * Fast-track reject an application.
     */
    public function reject(Application $application, string $reason): void
    {
        $rejectStage = $application->jobListing->pipelineStages()
            ->whereHas('pipelineStage', function ($q) {
                $q->where('is_terminal_fail', true);
            })
            ->first();

        if ($rejectStage) {
            $this->moveStage($application, $rejectStage, $reason);
        } else {
            // Fallback: manually update status if no terminal fail stage is configured
            $application->update([
                'status' => 'rejected',
                'rejected_at' => Carbon::now(),
                'rejection_reason' => $reason,
            ]);

            // Fire notification
            $this->notifier->notifyCandidate($application, 'rejected');
        }
    }

    /**
     * Fast-track hire an application.
     */
    public function hire(Application $application): void
    {
        $hiredStage = $application->jobListing->pipelineStages()
            ->whereHas('pipelineStage', function ($q) {
                $q->where('is_terminal_pass', true);
            })
            ->first();

        if ($hiredStage) {
            $this->moveStage($application, $hiredStage, 'Hired through pipeline.');
        } else {
            // Fallback: manually update status
            $application->update([
                'status' => 'hired',
                'hired_at' => Carbon::now(),
            ]);

            // Fire notification
            $this->notifier->notifyCandidate($application, 'hired');
        }
    }

    /**
     * Check if a candidate has an active application for a job.
     */
    public function checkDuplicate(int $jobId, string $phone, string $email): bool
    {
        return Application::where('job_listing_id', $jobId)
            ->whereIn('status', ['active'])
            ->where(function ($query) use ($phone, $email) {
                $query->where('phone', $phone)
                    ->orWhere('email', $email);
            })
            ->exists();
    }
}
