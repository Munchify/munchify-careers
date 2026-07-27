<?php

namespace App\Console\Commands;

use App\Models\Interview;
use App\Services\NotificationService;
use Illuminate\Console\Command;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class SendInterviewReminders extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'munchify:send-interview-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Finds interviews scheduled for tomorrow and sends SMS & WhatsApp reminders to candidates';

    protected NotificationService $notifier;

    public function __construct(NotificationService $notifier)
    {
        parent::__construct();
        $this->notifier = $notifier;
    }

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Finding interviews scheduled for tomorrow...');

        $tomorrow = Carbon::tomorrow();
        $interviews = Interview::with(['application.jobListing', 'interviewer', 'stage'])
            ->where('status', 'scheduled')
            ->whereDate('scheduled_at', $tomorrow)
            ->get();

        if ($interviews->isEmpty()) {
            $this->info('No interviews scheduled for tomorrow.');
            return;
        }

        $this->info("Found {$interviews->count()} interview(s). Sending reminders...");

        foreach ($interviews as $interview) {
            $app = $interview->application;

            $formattedTime = $interview->scheduled_at->format('M d, Y \a\t H:i');
            $typeLabel = match($interview->type) {
                'phone' => 'Phone Call',
                'video' => 'Google Meet / Video Call',
                'on_site' => 'On-site Interview',
                default => ucfirst($interview->type)
            };

            try {
                $this->notifier->notifyCandidate($app, 'interview_reminder', [
                    'scheduled_at' => $formattedTime,
                    'type' => $typeLabel,
                ]);

                $this->info("Sent reminder to {$app->full_name} for interview ID: {$interview->id}");
            } catch (\Exception $e) {
                Log::error("Failed to send scheduled interview reminder for interview ID {$interview->id}: " . $e->getMessage());
                $this->error("Failed to send reminder for interview ID: {$interview->id}");
            }
        }

        $this->info('Reminder run completed.');
    }
}
