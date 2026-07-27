<?php

namespace App\Services;

use App\Models\Application;
use App\Models\NotificationTemplate;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Carbon;
use App\Mail\CandidateWorkflowMail;
use App\Models\Communication;

class NotificationService
{
    protected SmsService $sms;

    public function __construct(SmsService $sms)
    {
        $this->sms = $sms;
    }

    /**
     * Notify candidate of an event using Email & SMS template rendering.
     */
    public function notifyCandidate(Application $application, string $eventKey, array $extras = []): void
    {
        $template = NotificationTemplate::where('event_key', $eventKey)->first();

        if (!$template) {
            Log::warning("Notification template not found for event: {$eventKey}");
            return;
        }

        // Prepare placeholder replacements
        $replacements = [
            '{name}' => $application->full_name,
            '{job_title}' => $application->jobListing->title,
            '{app_number}' => $application->application_number,
            '{status_url}' => $application->status_url,
            '{stage_name}' => $extras['stage_name'] ?? ($application->currentStage->name ?? ''),
            '{scheduled_at}' => $extras['scheduled_at'] ?? '',
            '{type}' => $extras['type'] ?? '',
            '{details}' => $extras['details'] ?? '',
        ];

        // Replace placeholders in bodies
        $smsBody = str_replace(array_keys($replacements), array_values($replacements), $template->sms_body);
        $emailSubject = str_replace(array_keys($replacements), array_values($replacements), $template->email_subject ?? '');
        $emailBody = str_replace(array_keys($replacements), array_values($replacements), $template->email_body ?? '');

        // Send Email
        if (!empty($application->email) && !empty($emailSubject) && !empty($emailBody)) {
            try {
                Mail::to($application->email)
                    ->send(new CandidateWorkflowMail($emailSubject, $emailBody, $application->full_name));

                // Log email communication
                Communication::create([
                    'application_id' => $application->id,
                    'channel' => 'email',
                    'direction' => 'outbound',
                    'subject' => $emailSubject,
                    'message' => $emailBody,
                    'sent_by' => Auth::id(),
                    'status' => 'sent',
                    'sent_at' => Carbon::now(),
                ]);
            } catch (\Exception $e) {
                Log::error("Failed to send candidate email: " . $e->getMessage());
            }
        }

        // Send SMS
        try {
            $this->sms->send($application->phone, $smsBody, $application->id);
        } catch (\Exception $e) {
            Log::error("Failed to send candidate SMS: " . $e->getMessage());
        }
    }

    /**
     * Notify the recruitment/hiring team of an event.
     */
    public function notifyTeam(string $event, Application $application): void
    {
        Log::info("Team notification event [{$event}] fired for Application: {$application->application_number}");
    }
}
