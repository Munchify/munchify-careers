@extends('layouts.dashboard')

@section('title', 'Edit Pipeline Template - Recruiter Dashboard')
@section('header_title', 'Edit Pipeline: ' . $template->name)

@section('content')
<form action="{{ route('pipelines.edit', ['id' => $template->id]) }}" method="POST" class="space-y-6" x-data="pipelineFormHandler()">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT: Template details & general configs -->
        <div class="col-span-1 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6 h-fit sticky top-24">
            <h3 class="font-extrabold text-sm text-[#111318] border-b border-gray-100 pb-3 flex items-center gap-2">
                <i class="fa-solid fa-gears text-[#FF6B00]"></i> Template Properties
            </h3>

            <!-- Name -->
            <div>
                <label for="name" class="form-label">Template Name <span class="text-red-500">*</span></label>
                <input type="text" name="name" id="name" class="form-input text-xs" placeholder="e.g. Technology Pipeline" value="{{ old('name', $template->name) }}" required>
            </div>

            <!-- Description -->
            <div>
                <label for="description" class="form-label">Description</label>
                <textarea name="description" id="description" rows="3" class="form-input text-xs" placeholder="Describe this hiring workflow...">{{ old('description', $template->description) }}</textarea>
            </div>

            <!-- Hint -->
            <div>
                <label for="department_hint" class="form-label">Department Hint (Optional)</label>
                <input type="text" name="department_hint" id="department_hint" class="form-input text-xs" placeholder="e.g. Technology, Customer Care" value="{{ old('department_hint', $template->department_hint) }}">
            </div>

            <!-- Default toggle -->
            <div class="flex items-center justify-between bg-[#F7F8FA] p-3.5 rounded-xl border border-gray-250/50">
                <div class="flex flex-col">
                    <span class="text-xs font-bold text-gray-800">Set as Default?</span>
                    <span class="text-[9px] text-gray-500">New jobs will use this template by default.</span>
                </div>
                <label class="relative inline-flex items-center cursor-pointer">
                    <input type="checkbox" name="is_default" value="1" class="sr-only peer" {{ $template->is_default ? 'checked' : '' }}>
                    <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                </label>
            </div>

            <div class="pt-4 border-t border-gray-100 flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow rounded-xl py-2.5 font-bold">
                    Save Changes
                </button>
                <a href="{{ route('pipelines.index') }}" class="btn btn-secondary btn-sm py-2.5 rounded-xl">Cancel</a>
            </div>
        </div>

        <!-- RIGHT: Dynamic stages builder -->
        <div class="col-span-1 lg:col-span-2 bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
            <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                    <i class="fa-solid fa-route text-[#FF6B00]"></i> Pipeline Stages Workflow
                </h3>
                <button type="button" @click="addStage()" class="btn btn-secondary btn-sm py-1.5 px-4 font-bold text-[11px] rounded-full">
                    <i class="fa-solid fa-plus text-[9px]"></i> Add Stage
                </button>
            </div>

            <div class="space-y-4">
                <template x-for="(stage, index) in stages" :key="index">
                    <div class="p-4 bg-[#F7F8FA] border border-gray-200 rounded-xl space-y-4 relative animate-fade-in">
                        <!-- Remove button -->
                        <button type="button" @click="removeStage(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition">
                            <i class="fa-solid fa-trash text-xs"></i>
                        </button>

                        <!-- Hidden stage ID to track updateOrCreate on server -->
                        <input type="hidden" :name="`stages[${index}][id]`" x-model="stage.id">

                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                            <!-- Stage Name -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="form-label text-[11px]">Stage Name <span class="text-red-500">*</span></label>
                                <input type="text" :name="`stages[${index}][name]`" x-model="stage.name" class="form-input text-xs" placeholder="e.g. Resume Screen" required>
                            </div>

                            <!-- Color picker -->
                            <div>
                                <label class="form-label text-[11px]">Color Badge</label>
                                <div class="flex gap-2 items-center">
                                    <input type="color" :name="`stages[${index}][color]`" x-model="stage.color" class="w-8 h-8 rounded border border-gray-200 cursor-pointer p-0 bg-transparent shrink-0">
                                    <input type="text" x-model="stage.color" class="form-input text-xs font-mono py-1 px-2 uppercase" placeholder="#3B82F6">
                                </div>
                            </div>

                            <!-- Sort order (static display) -->
                            <div>
                                <label class="form-label text-[11px]">Sort Order</label>
                                <input type="text" class="form-input text-xs bg-gray-50 border-gray-150 text-gray-500 cursor-not-allowed" :value="index + 1" disabled>
                            </div>
                        </div>

                        <div>
                            <label class="form-label text-[11px]">Stage Description</label>
                            <input type="text" :name="`stages[${index}][description]`" x-model="stage.description" class="form-input text-xs" placeholder="e.g. Evaluation of CV by the recruitment lead">
                        </div>

                        <!-- Checkboxes row -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-white p-3 rounded-xl border border-gray-150/60">
                            <!-- Auto notify -->
                            <div class="flex items-center justify-between">
                                <span class="text-[10px] font-bold text-gray-750">Auto Notify Candidate?</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :name="`stages[${index}][auto_notify_candidate]`" x-model="stage.auto_notify_candidate" :checked="stage.auto_notify_candidate" value="1" class="sr-only peer">
                                    <div class="w-7 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                                </label>
                            </div>

                            <!-- Terminal Pass -->
                            <div class="flex items-center justify-between border-t md:border-t-0 md:border-l border-gray-100 pt-2 md:pt-0 md:pl-4">
                                <span class="text-[10px] font-bold text-gray-750">Terminal Pass (Hired)?</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :name="`stages[${index}][is_terminal_pass]`" x-model="stage.is_terminal_pass" :checked="stage.is_terminal_pass" @change="onTerminalPassChange(index)" value="1" class="sr-only peer">
                                    <div class="w-7 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                                </label>
                            </div>

                            <!-- Terminal Fail -->
                            <div class="flex items-center justify-between border-t md:border-t-0 md:border-l border-gray-100 pt-2 md:pt-0 md:pl-4">
                                <span class="text-[10px] font-bold text-gray-750">Terminal Fail (Reject)?</span>
                                <label class="relative inline-flex items-center cursor-pointer">
                                    <input type="checkbox" :name="`stages[${index}][is_terminal_fail]`" x-model="stage.is_terminal_fail" :checked="stage.is_terminal_fail" @change="onTerminalFailChange(index)" value="1" class="sr-only peer">
                                    <div class="w-7 h-4 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-3 after:w-3 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                                </label>
                            </div>
                        </div>

                    </div>
                </template>

                <div x-show="stages.length === 0" class="text-center py-12 text-gray-400 text-xs italic">
                    No stages added yet. Add at least 2 stages to build your hiring workflow.
                </div>
            </div>

        </div>

    </div>
</form>
@endsection

@section('scripts')
<script>
    function pipelineFormHandler() {
        return {
            stages: @json($template->stages->map(fn($stage) => [
                'id' => $stage->id,
                'name' => $stage->name,
                'description' => $stage->description,
                'color' => $stage->color,
                'auto_notify_candidate' => (bool)$stage->auto_notify_candidate,
                'is_terminal_pass' => (bool)$stage->is_terminal_pass,
                'is_terminal_fail' => (bool)$stage->is_terminal_fail,
            ])),
            
            addStage() {
                this.stages.push({
                    id: null,
                    name: '',
                    description: '',
                    color: '#3B82F6',
                    auto_notify_candidate: true,
                    is_terminal_pass: false,
                    is_terminal_fail: false
                });
            },
            
            removeStage(index) {
                this.stages.splice(index, 1);
            },

            onTerminalPassChange(index) {
                if (this.stages[index].is_terminal_pass) {
                    this.stages[index].is_terminal_fail = false;
                }
            },

            onTerminalFailChange(index) {
                if (this.stages[index].is_terminal_fail) {
                    this.stages[index].is_terminal_pass = false;
                }
            }
        }
    }
</script>
@endsection
