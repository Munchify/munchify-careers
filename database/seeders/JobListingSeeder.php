<?php

namespace Database\Seeders;

use App\Models\JobListing;
use App\Models\Department;
use App\Models\PipelineTemplate;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;

class JobListingSeeder extends Seeder
{
    public function run(): void
    {
        $opsDept = Department::where('name', 'Operations')->first();
        $ccDept = Department::where('name', 'Customer Care')->first();
        $techDept = Department::where('name', 'Technology')->first();

        $riderTemplate = PipelineTemplate::where('name', 'Rider Pipeline')->first();
        $standardTemplate = PipelineTemplate::where('name', 'Standard Pipeline')->first();

        $opsManager = User::where('email', 'ops.hiring@munchify.com')->first();
        $techManager = User::where('email', 'tech.hiring@munchify.com')->first();
        $hrManager = User::where('email', 'hr@munchify.com')->first();

        // 1. Delivery Rider
        JobListing::create([
            'title' => 'Delivery Rider',
            'department_id' => $opsDept->id,
            'type' => 'contract',
            'location' => 'on_site',
            'location_detail' => 'Maseno University Campus & surroundings',
            'description' => 'We are looking for fast, reliable, and friendly delivery riders to join our logistics fleet at Maseno University. You will be responsible for ensuring that hot meals are delivered to students and staff on time, maintaining order accuracy, and representing the Munchify brand with a smile.',
            'requirements' => "• Valid Kenyan driving license (Class FG/motorcycle/riding qualification).\n• Proven experience riding a motorcycle or bicycle in busy environments.\n• Excellent knowledge of Maseno University campus layout and nearby residential estates.\n• Polite, customer-focused attitude and good communication skills.\n• A functional smartphone with active mobile data.",
            'responsibilities' => "• Dispatch and deliver food orders from restaurant partners to customer locations.\n• Handle food containers carefully to avoid spillages and maintain meal quality.\n• Manage cash-on-delivery and M-Pesa payments from customers.\n• Report any delivery delays or order issues to the Operations Supervisor.\n• Follow road safety protocols and maintain your vehicle/bicycle in peak operational state.",
            'salary_range' => 'KES 15,000 - 25,000 / month',
            'slots' => 10,
            'status' => 'published',
            'application_deadline' => Carbon::now()->addDays(30),
            'pipeline_template_id' => $riderTemplate->id,
            'screening_questions' => [
                [
                    'question' => 'Do you have a valid driving/riding license?',
                    'type' => 'boolean',
                    'knockout' => true,
                    'expected' => true,
                ],
                [
                    'question' => 'Do you own a motorcycle or bicycle?',
                    'type' => 'select',
                    'options' => ['Motorcycle', 'Bicycle', 'None'],
                ],
                [
                    'question' => 'How many years of riding experience do you have?',
                    'type' => 'number',
                ],
            ],
            'requires_cv' => true,
            'requires_video' => false,
            'hiring_manager_id' => $opsManager->id,
            'published_at' => Carbon::now(),
            'created_by' => $hrManager->id,
        ]);

        // 2. Customer Care Representative
        JobListing::create([
            'title' => 'Customer Care Representative',
            'department_id' => $ccDept->id,
            'type' => 'part_time',
            'location' => 'hybrid',
            'location_detail' => 'Maseno Office (2 days/week) & Remote',
            'description' => 'Munchify is seeking an empathetic, solutions-oriented Customer Care Representative to support our growing customer base at Maseno University. In this role, you will be the voice of Munchify, helping customers track their orders, resolving issues with restaurant partners, and assisting riders in finding locations.',
            'requirements' => "• Strong verbal and written communication skills in both English and Swahili.\n• Active student status at Maseno University is preferred (great for part-time schedules).\n• High level of emotional intelligence, patience, and ability to handle stressful situations.\n• Basic computer proficiency and ability to learn chat software quickly.\n• Prior customer support or call center experience is a plus.",
            'responsibilities' => "• Respond promptly to customer chats, emails, and phone calls regarding order issues.\n• Coordinate with delivery riders and partner restaurants to resolve delivery delays.\n• Document support queries and process refunds or discount coupons where appropriate.\n• Gather customer feedback and present weekly suggestions to the operations lead.",
            'salary_range' => 'KES 10,000 - 15,000 / month',
            'slots' => 3,
            'status' => 'published',
            'application_deadline' => Carbon::now()->addDays(14),
            'pipeline_template_id' => $standardTemplate->id,
            'screening_questions' => [
                [
                    'question' => 'Are you currently a student at Maseno University?',
                    'type' => 'boolean',
                ],
                [
                    'question' => 'Do you have previous customer service experience?',
                    'type' => 'boolean',
                ],
                [
                    'question' => 'What is your availability during weekends?',
                    'type' => 'select',
                    'options' => ['Full day', 'Half day', 'Not available'],
                ],
            ],
            'requires_cv' => true,
            'requires_video' => true,
            'video_prompt' => 'Record a short 60-second video introducing yourself, explaining your current studies, and sharing why you enjoy helping others.',
            'hiring_manager_id' => $opsManager->id,
            'published_at' => Carbon::now(),
            'created_by' => $hrManager->id,
        ]);

        // 3. Full-Stack Developer
        JobListing::create([
            'title' => 'Full-Stack Developer (Laravel/Vite/Tailwind)',
            'department_id' => $techDept->id,
            'type' => 'full_time',
            'location' => 'remote',
            'location_detail' => 'Remote (Kenya)',
            'description' => 'We are looking for a skilled Full-Stack Developer to maintain and upgrade the core Munchify ordering and logistics engine. You will be building new features for our student app, optimizing rider routing algorithms, and improving internal dashboards.',
            'requirements' => "• 2+ years of professional software development experience.\n• High proficiency in PHP, Laravel 10/11, and MySQL database management.\n• Strong front-end skills in modern CSS/HTML, Tailwind CSS, Alpine.js, or React/Vue.\n• Practical experience with Git version control and deploying to cloud infrastructure.\n• Familiarity with integrating SMS gateways (like Hostpinnacle) and payment gateways (like M-Pesa).",
            'responsibilities' => "• Write clean, secure, and maintainable code for Munchify web and backend platforms.\n• Build and document REST APIs for our mobile applications.\n• Optimize database queries and app cache to handle high order traffic peaks.\n• Troubleshoot production bugs and support the customer care team with technical investigations.\n• Write automated unit and integration tests.",
            'salary_range' => 'KES 80,000 - 120,000 / month',
            'slots' => 1,
            'status' => 'published',
            'application_deadline' => Carbon::now()->addDays(20),
            'pipeline_template_id' => $standardTemplate->id,
            'screening_questions' => [
                [
                    'question' => 'How many years of professional PHP/Laravel experience do you have?',
                    'type' => 'number',
                    'knockout' => true,
                    'min' => 1,
                ],
                [
                    'question' => 'Do you have experience with Flutter or mobile development?',
                    'type' => 'boolean',
                ],
            ],
            'requires_cv' => true,
            'requires_video' => true,
            'video_prompt' => 'Briefly describe a challenging backend bug or architectural issue you encountered recently, and how you went about solving it.',
            'hiring_manager_id' => $techManager->id,
            'published_at' => Carbon::now(),
            'created_by' => $hrManager->id,
        ]);
    }
}
