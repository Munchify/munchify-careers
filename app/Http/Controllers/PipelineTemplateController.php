<?php

namespace App\Http\Controllers;

use App\Models\PipelineTemplate;
use App\Models\PipelineStage;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class PipelineTemplateController extends Controller
{
    public function index()
    {
        $templates = PipelineTemplate::withCount('stages')->get();
        return view('dashboard.pipelines.index', compact('templates'));
    }

    public function create()
    {
        return view('dashboard.pipelines.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department_hint' => 'nullable|string|max:100',
            'is_default' => 'required|boolean',
            'stages' => 'required|array|min:2',
            'stages.*.name' => 'required|string|max:100',
            'stages.*.description' => 'nullable|string|max:500',
            'stages.*.color' => 'required|string|max:7',
            'stages.*.auto_notify_candidate' => 'required|boolean',
            'stages.*.is_terminal_pass' => 'required|boolean',
            'stages.*.is_terminal_fail' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated) {
            // If default is true, clear other defaults
            if ($validated['is_default']) {
                PipelineTemplate::where('is_default', true)->update(['is_default' => false]);
            }

            $template = PipelineTemplate::create([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'department_hint' => $validated['department_hint'],
                'is_default' => $validated['is_default'],
            ]);

            foreach ($validated['stages'] as $index => $stageData) {
                PipelineStage::create([
                    'pipeline_template_id' => $template->id,
                    'name' => $stageData['name'],
                    'description' => $stageData['description'],
                    'color' => $stageData['color'],
                    'sort_order' => $index + 1,
                    'is_terminal_pass' => $stageData['is_terminal_pass'],
                    'is_terminal_fail' => $stageData['is_terminal_fail'],
                    'auto_notify_candidate' => $stageData['auto_notify_candidate'],
                ]);
            }

            AuditLog::log(
                actorId: Auth::id(),
                action: 'pipeline_template_created',
                entityType: PipelineTemplate::class,
                entityId: $template->id,
                details: ['name' => $template->name]
            );

            return redirect()->route('pipelines.index')->with('success', 'Pipeline template created successfully.');
        });
    }

    public function edit($id)
    {
        $template = PipelineTemplate::with(['stages' => function ($q) {
            $q->orderBy('sort_order');
        }])->findOrFail($id);

        return view('dashboard.pipelines.edit', compact('template'));
    }

    public function update(Request $request, $id)
    {
        $template = PipelineTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'department_hint' => 'nullable|string|max:100',
            'is_default' => 'required|boolean',
            'stages' => 'required|array|min:2',
            'stages.*.id' => 'nullable|exists:pipeline_stages,id',
            'stages.*.name' => 'required|string|max:100',
            'stages.*.description' => 'nullable|string|max:500',
            'stages.*.color' => 'required|string|max:7',
            'stages.*.auto_notify_candidate' => 'required|boolean',
            'stages.*.is_terminal_pass' => 'required|boolean',
            'stages.*.is_terminal_fail' => 'required|boolean',
        ]);

        return DB::transaction(function () use ($validated, $template) {
            if ($validated['is_default']) {
                PipelineTemplate::where('is_default', true)
                    ->where('id', '!=', $template->id)
                    ->update(['is_default' => false]);
            }

            $template->update([
                'name' => $validated['name'],
                'description' => $validated['description'],
                'department_hint' => $validated['department_hint'],
                'is_default' => $validated['is_default'],
            ]);

            // Keep track of active stage IDs to delete removed stages
            $activeIds = [];

            foreach ($validated['stages'] as $index => $stageData) {
                $stage = PipelineStage::updateOrCreate([
                    'id' => $stageData['id'] ?? null,
                    'pipeline_template_id' => $template->id,
                ], [
                    'name' => $stageData['name'],
                    'description' => $stageData['description'],
                    'color' => $stageData['color'],
                    'sort_order' => $index + 1,
                    'is_terminal_pass' => $stageData['is_terminal_pass'],
                    'is_terminal_fail' => $stageData['is_terminal_fail'],
                    'auto_notify_candidate' => $stageData['auto_notify_candidate'],
                ]);

                $activeIds[] = $stage->id;
            }

            // Delete stages that were removed
            PipelineStage::where('pipeline_template_id', $template->id)
                ->whereNotIn('id', $activeIds)
                ->delete();

            AuditLog::log(
                actorId: Auth::id(),
                action: 'pipeline_template_updated',
                entityType: PipelineTemplate::class,
                entityId: $template->id,
                details: ['name' => $template->name]
            );

            return redirect()->route('pipelines.index')->with('success', 'Pipeline template updated successfully.');
        });
    }

    public function destroy($id)
    {
        $template = PipelineTemplate::findOrFail($id);

        if ($template->is_default) {
            return back()->withErrors(['error' => 'You cannot delete the default pipeline template.']);
        }

        // Check if template is used by any job listing
        $inUse = \App\Models\JobListing::where('pipeline_template_id', $template->id)->exists();
        if ($inUse) {
            return back()->withErrors(['error' => 'This pipeline template is in use by one or more job listings and cannot be deleted.']);
        }

        DB::transaction(function () use ($template) {
            $template->delete();
            
            AuditLog::log(
                actorId: Auth::id(),
                action: 'pipeline_template_deleted',
                entityType: PipelineTemplate::class,
                entityId: $template->id,
                details: ['name' => $template->name]
            );
        });

        return redirect()->route('pipelines.index')->with('success', 'Pipeline template deleted successfully.');
    }
}
