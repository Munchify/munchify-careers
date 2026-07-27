<?php

namespace App\Http\Controllers;

use App\Models\JobListing;
use App\Services\ApplicationService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class ApplicationWizardController extends Controller
{
    protected ApplicationService $appService;

    public function __construct(ApplicationService $appService)
    {
        $this->appService = $appService;
    }

    public function showStep($ulid, $step)
    {
        $job = JobListing::published()->where('ulid', $ulid)->firstOrFail();
        $step = (int)$step;

        if ($step < 1 || $step > 5) {
            return redirect()->route('apply.step', ['ulid' => $ulid, 'step' => 1]);
        }

        // Retrieve current wizard state from session
        $sessionKey = "apply_{$ulid}";
        $wizardData = Session::get($sessionKey, []);

        return view("public.wizard.step{$step}", compact('job', 'step', 'wizardData'));
    }

    public function saveStep(Request $request, $ulid, $step)
    {
        $job = JobListing::published()->where('ulid', $ulid)->firstOrFail();
        $step = (int)$step;
        $sessionKey = "apply_{$ulid}";
        $wizardData = Session::get($sessionKey, []);

        if ($step === 1) {
            $validated = $request->validate([
                'full_name' => 'required|string|max:255',
                'email' => 'required|email|max:255',
                'phone' => 'required|string|max:20',
                'location' => 'required|string|max:255',
            ]);
            $wizardData['step1'] = $validated;
        } elseif ($step === 2) {
            $validated = $request->validate([
                'current_role' => 'nullable|string|max:255',
                'experience_years' => 'nullable|string|max:50',
                'skills' => 'nullable|string|max:500',
                'cover_letter' => 'nullable|string|max:5000',
                'motivation' => 'nullable|string|max:2000',
            ]);
            $wizardData['step2'] = $validated;
        } elseif ($step === 3) {
            // Validate screening answers
            $rules = [];
            if ($job->screening_questions) {
                foreach ($job->screening_questions as $index => $q) {
                    $required = 'required';
                    if ($q['type'] === 'boolean') {
                        $rules["answers.{$index}.answer"] = 'required|boolean';
                    } elseif ($q['type'] === 'number') {
                        $rules["answers.{$index}.answer"] = 'required|integer';
                    } else {
                        $rules["answers.{$index}.answer"] = 'required|string';
                    }
                }
            }
            $validated = $request->validate($rules);
            
            // Reformat screening answers for model serialization
            $answers = [];
            if ($job->screening_questions) {
                foreach ($job->screening_questions as $index => $q) {
                    $ans = $request->input("answers.{$index}.answer");
                    // Convert boolean representation if necessary
                    if ($q['type'] === 'boolean') {
                        $ans = filter_var($ans, FILTER_VALIDATE_BOOLEAN);
                    }
                    $answers[] = [
                        'question' => $q['question'],
                        'answer' => $ans,
                    ];
                }
            }
            $wizardData['step3'] = ['screening_answers' => $answers];
        } elseif ($step === 4) {
            $rules = [];
            if ($job->requires_cv) {
                $rules['cv'] = (empty($wizardData['step4']['cv_path']) ? 'required' : 'nullable') . '|file|mimes:pdf,doc,docx|max:5120'; // 5MB limit
            }
            if ($job->requires_video) {
                $rules['video'] = (empty($wizardData['step4']['video_path']) ? 'required' : 'nullable') . '|file|mimes:mp4,mov,avi,webm|max:51200'; // 50MB limit
            }

            $request->validate($rules);

            // Upload files immediately and save the path in the session
            if ($request->hasFile('cv')) {
                $cvPath = $request->file('cv')->store('cvs', 'public');
                $wizardData['step4']['cv_path'] = $cvPath;
            }
            if ($request->hasFile('video')) {
                $videoPath = $request->file('video')->store('videos', 'public');
                $wizardData['step4']['video_path'] = $videoPath;
            }
        }

        Session::put($sessionKey, $wizardData);

        // Progress to next step
        $nextStep = $step + 1;
        if ($nextStep > 5) {
            return redirect()->route('apply.step', ['ulid' => $ulid, 'step' => 5]);
        }

        return redirect()->route('apply.step', ['ulid' => $ulid, 'step' => $nextStep]);
    }

    public function submit(Request $request, $ulid)
    {
        $job = JobListing::published()->where('ulid', $ulid)->firstOrFail();
        $sessionKey = "apply_{$ulid}";
        $wizardData = Session::get($sessionKey, []);

        if (empty($wizardData['step1'])) {
            return redirect()->route('apply.step', ['ulid' => $ulid, 'step' => 1]);
        }

        // Construct submission payload
        $payload = array_merge(
            $wizardData['step1'],
            $wizardData['step2'] ?? [],
            $wizardData['step3'] ?? []
        );

        $cvPath = $wizardData['step4']['cv_path'] ?? null;
        $videoPath = $wizardData['step4']['video_path'] ?? null;

        try {
            $app = $this->appService->submit($job, $payload, $cvPath, $videoPath);
            
            // Clear session data
            Session::forget($sessionKey);

            return redirect()->route('apply.success', ['ulid' => $ulid])
                ->with('application_number', $app->application_number)
                ->with('status_url', $app->status_url);
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'An error occurred during submission: ' . $e->getMessage()]);
        }
    }

    public function success($ulid)
    {
        $job = JobListing::published()->where('ulid', $ulid)->firstOrFail();
        return view('public.wizard.success', compact('job'));
    }
}
