<?php

namespace Database\Seeders;

use App\Models\Application;
use App\Models\JobListing;
use App\Models\JobPipelineStage;
use App\Models\User;
use App\Models\ApplicationScore;
use App\Models\ApplicationNote;
use App\Models\ApplicationStageLog;
use App\Models\Interview;
use App\Models\Communication;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;

class ApplicationSeeder extends Seeder
{
    public function run(): void
    {
        $jobs = JobListing::all();
        $users = User::all();

        $names = [
            'John Otieno', 'Grace Kemunto', 'David Mwangi', 'Mercy Chepngetich', 'Emmanuel Kiprop',
            'Sarah Wanjiku', 'Brian Ochieng', 'Amina Yusuf', 'Kevin Ndwiga', 'Ruth Mutua',
            'Peter Kamau', 'Faith Nekesa', 'Daniel Kipruto', 'Sharon Cherotich', 'Paul Onyango',
            'Esther Wambui', 'Michael Mwangi', 'Lucy Wairimu', 'James Omondi', 'Mary Atieno'
        ];

        $sources = ['direct', 'referral', 'social', 'other'];

        $i = 0;
        foreach ($jobs as $job) {
            // Get the job pipeline stages
            $stages = $job->pipelineStages;
            if ($stages->isEmpty()) {
                continue;
            }

            // Create 6-8 applications per job
            $appCount = rand(6, 8);
            for ($k = 0; $k < $appCount; $k++) {
                $name = $names[$i % count($names)];
                $i++;

                $phone = '+2547' . str_pad((string)rand(10000000, 99999999), 8, '0', STR_PAD_LEFT);
                $email = strtolower(str_replace(' ', '.', $name)) . '@example.com';
                
                // Select a stage
                $stageIndex = rand(0, $stages->count() - 1);
                $currentStage = $stages[$stageIndex];

                // Determine status based on stage
                $status = 'active';
                $hiredAt = null;
                $rejectedAt = null;
                if ($currentStage->is_terminal_pass) {
                    $status = 'hired';
                    $hiredAt = Carbon::now()->subDays(rand(1, 10));
                } elseif ($currentStage->is_terminal_fail) {
                    $status = 'rejected';
                    $rejectedAt = Carbon::now()->subDays(rand(1, 10));
                }

                $source = $sources[array_rand($sources)];
                $referralName = $source === 'referral' ? 'Alex Ndambuki' : null;

                // Create screening answers based on job title
                $screeningAnswers = [];
                if ($job->title === 'Delivery Rider') {
                    $screeningAnswers = [
                        ['question' => 'Do you have a valid driving/riding license?', 'answer' => true],
                        ['question' => 'Do you own a motorcycle or bicycle?', 'answer' => 'Motorcycle'],
                        ['question' => 'How many years of riding experience do you have?', 'answer' => rand(1, 5)],
                    ];
                } elseif ($job->title === 'Customer Care Representative') {
                    $screeningAnswers = [
                        ['question' => 'Are you currently a student at Maseno University?', 'answer' => true],
                        ['question' => 'Do you have previous customer service experience?', 'answer' => rand(0, 1) === 1],
                        ['question' => 'What is your availability during weekends?', 'answer' => 'Full day'],
                    ];
                } else {
                    $screeningAnswers = [
                        ['question' => 'How many years of professional PHP/Laravel experience do you have?', 'answer' => rand(2, 6)],
                        ['question' => 'Do you have experience with Flutter or mobile development?', 'answer' => rand(0, 1) === 1],
                    ];
                }

                // Sequence number
                $seqNum = str_pad((string)($i), 4, '0', STR_PAD_LEFT);
                $appNumber = "MUN-APP-{$seqNum}";

                $application = Application::create([
                    'ulid' => strtolower((string) Str::ulid()),
                    'application_number' => $appNumber,
                    'job_listing_id' => $job->id,
                    'full_name' => $name,
                    'email' => $email,
                    'phone' => $phone,
                    'location' => 'Maseno',
                    'current_stage_id' => $currentStage->id,
                    'status' => $status,
                    'source' => $source,
                    'referral_name' => $referralName,
                    'cv_path' => 'cvs/placeholder.pdf',
                    'video_path' => $job->requires_video ? 'videos/placeholder.mp4' : null,
                    'screening_answers' => $screeningAnswers,
                    'is_starred' => rand(0, 1) === 1,
                    'is_knockout' => false,
                    'cover_letter' => "Dear Hiring Team,\n\nI am writing to express my strong interest in the {$job->title} position. I believe my background aligns perfectly with your requirements...",
                    'current_role' => $job->title === 'Full-Stack Developer' ? 'Junior Dev' : 'Freelancer',
                    'experience_years' => rand(1, 5) . ' Years',
                    'motivation' => 'I love Munchify and want to help build the best food delivery app in Maseno!',
                    'skills' => $job->title === 'Full-Stack Developer' ? 'PHP, Laravel, Tailwind, Alpine, Git' : 'Riding, Navigation, Communication',
                    'hired_at' => $hiredAt,
                    'rejected_at' => $rejectedAt,
                ]);

                // Update application count
                $job->increment('applications_count');

                // Seed some notes, scores, logs, interviews for some applications
                // 1. Add some logs
                for ($j = 0; $j <= $stageIndex; $j++) {
                    ApplicationStageLog::create([
                        'application_id' => $application->id,
                        'from_stage_id' => $j === 0 ? null : $stages[$j - 1]->id,
                        'to_stage_id' => $stages[$j]->id,
                        'changed_by' => $users->random()->id,
                        'note' => "Moved to {$stages[$j]->name} automatically during seeding.",
                    ]);
                }

                // 2. Add scores for screening/interview stage
                if ($stageIndex >= 1) {
                    $scoreUsers = $users->whereIn('role', ['admin', 'hr_manager', 'hiring_manager']);
                    if ($scoreUsers->isNotEmpty()) {
                        $evaluator = $scoreUsers->random();
                        ApplicationScore::create([
                            'application_id' => $application->id,
                            'user_id' => $evaluator->id,
                            'stage_id' => $stages[1]->id,
                            'score' => rand(3, 5),
                            'notes' => 'Great communication and strong screening answers.',
                            'recommendation' => 'yes',
                        ]);
                        $application->recalculateScore();
                    }
                }

                // 3. Add notes
                if (rand(0, 1) === 1) {
                    ApplicationNote::create([
                        'application_id' => $application->id,
                        'user_id' => $users->random()->id,
                        'body' => 'Candidate seems very enthusiastic. Fits the culture of Munchify well.',
                        'is_private' => rand(0, 1) === 1,
                    ]);
                }

                // 4. Add interviews
                if ($stageIndex >= 2 && str_contains(strtolower($currentStage->name), 'interview')) {
                    $interviewer = $users->where('role', 'interviewer')->random();
                    Interview::create([
                        'application_id' => $application->id,
                        'stage_id' => $currentStage->id,
                        'interviewer_id' => $interviewer->id,
                        'scheduled_at' => Carbon::now()->addDays(rand(-2, 3)),
                        'duration_minutes' => 30,
                        'type' => 'video',
                        'location_or_link' => 'https://meet.google.com/abc-defg-hij',
                        'status' => 'scheduled',
                        'notes' => 'Please ask about their availability.',
                    ]);
                }

                // 5. Add communications
                if (rand(0, 1) === 1) {
                    Communication::create([
                        'application_id' => $application->id,
                        'channel' => 'whatsapp',
                        'direction' => 'outbound',
                        'message' => "Hi {$application->full_name}, thank you for your application. Your application number is {$application->application_number}.",
                        'sent_by' => $users->random()->id,
                        'status' => 'delivered',
                        'sent_at' => Carbon::now()->subDays(rand(1, 3)),
                    ]);
                }
            }
        }
    }
}
