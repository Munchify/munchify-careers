<?php

namespace Database\Seeders;

use App\Models\PipelineTemplate;
use App\Models\PipelineStage;
use Illuminate\Database\Seeder;

class PipelineTemplateSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Standard Pipeline (Default)
        $standard = PipelineTemplate::create([
            'name' => 'Standard Pipeline',
            'description' => 'Default 6-stage hiring process for general office, tech, and marketing roles.',
            'department_hint' => 'Technology',
            'is_default' => true,
        ]);

        $standardStages = [
            [
                'name' => 'Applied',
                'description' => 'Initial application received.',
                'color' => '#FF6B00',
                'sort_order' => 1,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Screening',
                'description' => 'Initial screening and background checks.',
                'color' => '#F59E0B',
                'sort_order' => 2,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'First Interview',
                'description' => 'Initial conversation with HR or recruiter.',
                'color' => '#3B82F6',
                'sort_order' => 3,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Technical Panel',
                'description' => 'In-depth assessment or team interview.',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Hired',
                'description' => 'Formal offer accepted and onboarded.',
                'color' => '#10B981',
                'sort_order' => 5,
                'is_terminal_pass' => true,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Rejected',
                'description' => 'Candidate did not meet requirements at this time.',
                'color' => '#EF4444',
                'sort_order' => 6,
                'is_terminal_pass' => false,
                'is_terminal_fail' => true,
                'auto_notify_candidate' => true,
            ],
        ];

        foreach ($standardStages as $stage) {
            $standard->stages()->create($stage);
        }

        // 2. Rider Pipeline
        $rider = PipelineTemplate::create([
            'name' => 'Rider Pipeline',
            'description' => 'Streamlined 5-stage recruitment process for delivery riders.',
            'department_hint' => 'Operations',
            'is_default' => false,
        ]);

        $riderStages = [
            [
                'name' => 'Applied',
                'description' => 'Rider application submitted.',
                'color' => '#FF6B00',
                'sort_order' => 1,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Practical Test',
                'description' => 'Driving/motorcycle proficiency test and document validation.',
                'color' => '#10B981',
                'sort_order' => 2,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Interview',
                'description' => 'Short face-to-face interview with Operations Supervisor.',
                'color' => '#3B82F6',
                'sort_order' => 3,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Hired',
                'description' => 'Rider successfully contracted.',
                'color' => '#10B981',
                'sort_order' => 4,
                'is_terminal_pass' => true,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Rejected',
                'description' => 'Application rejected or driver failed proficiency.',
                'color' => '#EF4444',
                'sort_order' => 5,
                'is_terminal_pass' => false,
                'is_terminal_fail' => true,
                'auto_notify_candidate' => true,
            ],
        ];

        foreach ($riderStages as $stage) {
            $rider->stages()->create($stage);
        }

        // 3. Executive Pipeline
        $exec = PipelineTemplate::create([
            'name' => 'Executive Pipeline',
            'description' => 'Comprehensive 7-stage evaluation process for executive and senior management roles.',
            'department_hint' => 'Executive',
            'is_default' => false,
        ]);

        $execStages = [
            [
                'name' => 'Applied',
                'description' => 'Executive profile submitted.',
                'color' => '#FF6B00',
                'sort_order' => 1,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'CV Screening',
                'description' => 'Initial review by executive search committee.',
                'color' => '#F59E0B',
                'sort_order' => 2,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Round 1',
                'description' => 'First interview with board representatives.',
                'color' => '#3B82F6',
                'sort_order' => 3,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Board Interview',
                'description' => 'Final presentation and panel interview with full board.',
                'color' => '#8B5CF6',
                'sort_order' => 4,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Offer Phase',
                'description' => 'Contract negotiation and package sign-off.',
                'color' => '#EC4899',
                'sort_order' => 5,
                'is_terminal_pass' => false,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Hired',
                'description' => 'Executive appointed.',
                'color' => '#10B981',
                'sort_order' => 6,
                'is_terminal_pass' => true,
                'is_terminal_fail' => false,
                'auto_notify_candidate' => true,
            ],
            [
                'name' => 'Rejected',
                'description' => 'Candidate declined or application terminated.',
                'color' => '#EF4444',
                'sort_order' => 7,
                'is_terminal_pass' => false,
                'is_terminal_fail' => true,
                'auto_notify_candidate' => true,
            ],
        ];

        foreach ($execStages as $stage) {
            $exec->stages()->create($stage);
        }
    }
}
