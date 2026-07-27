@extends('layouts.dashboard')

@section('title', 'Create Job Opening - Recruiter Dashboard')
@section('header_title', 'Create Job Opening')

@section('content')
<form action="{{ route('jobs.create') }}" method="POST" class="space-y-6" x-data="jobFormHandler()">
    @csrf

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- LEFT COLUMN: Main Job specifications -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
                <h3 class="font-extrabold text-sm text-[#111318] border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-[#FF6B00]"></i> Job Specifications
                </h3>

                <!-- Title -->
                <div>
                    <label for="title" class="form-label">Job Title <span class="text-red-500">*</span></label>
                    <input type="text" name="title" id="title" class="form-input text-xs" placeholder="e.g. Lead Delivery Rider" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Department -->
                    <div>
                        <label for="department_id" class="form-label">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="department_id" class="form-input text-xs" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}">{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pipeline Template -->
                    <div>
                        <label for="pipeline_template_id" class="form-label">Recruitment Pipeline <span class="text-red-500">*</span></label>
                        <select name="pipeline_template_id" id="pipeline_template_id" class="form-input text-xs" required>
                            <option value="">-- Select Pipeline Template --</option>
                            @foreach($templates as $tmpl)
                                <option value="{{ $tmpl->id }}">{{ $tmpl->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Job Type -->
                    <div>
                        <label for="type" class="form-label">Employment Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="form-input text-xs" required>
                            <option value="full_time">Full-time</option>
                            <option value="part_time">Part-time</option>
                            <option value="contract">Contract</option>
                            <option value="internship">Internship</option>
                        </select>
                    </div>

                    <!-- Work setting -->
                    <div>
                        <label for="location" class="form-label">Workplace Setting <span class="text-red-500">*</span></label>
                        <select name="location" id="location" class="form-input text-xs" required>
                            <option value="on_site">On-site</option>
                            <option value="remote">Remote</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                    </div>

                    <!-- Location Detail -->
                    <div>
                        <label for="location_detail" class="form-label">Location Detail</label>
                        <input type="text" name="location_detail" id="location_detail" class="form-input text-xs" placeholder="e.g. Maseno Kitchen, Main Gate">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Openings slots -->
                    <div>
                        <label for="slots" class="form-label">Slots Available <span class="text-red-500">*</span></label>
                        <input type="number" name="slots" id="slots" class="form-input text-xs" value="1" min="1" required>
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="application_deadline" class="form-label">Application Deadline</label>
                        <input type="date" name="application_deadline" id="application_deadline" class="form-input text-xs">
                    </div>
                </div>

                <!-- Primary textareas description/requirements/responsibilities -->
                <div class="space-y-4">
                    <div class="flex items-center justify-between bg-gradient-to-r from-amber-50 to-orange-50 p-3.5 rounded-xl border border-amber-200/80">
                        <div class="flex items-center gap-2.5">
                            <span class="w-8 h-8 rounded-lg bg-[#FF6B00] text-white flex items-center justify-center font-bold text-xs shadow-md shadow-[#FF6B00]/20">
                                <i class="fa-solid fa-wand-magic-sparkles"></i>
                            </span>
                            <div>
                                <h4 class="text-xs font-bold text-gray-900">AI Job Content Assistant</h4>
                                <p class="text-[10px] text-gray-500">Auto-generate professional description, requirements & responsibilities based on job title.</p>
                            </div>
                        </div>
                        <button type="button" @click="generateAiContent()" :disabled="generatingAi" class="btn bg-[#111318] hover:bg-black text-white btn-sm py-1.5 px-4 font-bold text-[11px] rounded-xl flex items-center gap-2 transition shadow-sm">
                            <i class="fa-solid fa-sparkles text-[#FFD233]" x-show="!generatingAi"></i>
                            <i class="fa-solid fa-circle-notch fa-spin text-[#FFD233]" x-show="generatingAi" style="display: none;"></i>
                            <span x-text="generatingAi ? 'Generating...' : 'Auto-Generate Specs'"></span>
                        </button>
                    </div>

                    <div>
                        <label for="description" class="form-label">Job Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                        <textarea name="description" id="description" x-model="description" rows="3" class="form-input text-xs" placeholder="Briefly outline the job description (Optional)..."></textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="requirements" class="form-label">Requirements <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea name="requirements" id="requirements" x-model="requirements" rows="3" class="form-input text-xs" placeholder="Requirements (Optional)..."></textarea>
                        </div>

                        <div>
                            <label for="responsibilities" class="form-label">Responsibilities <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea name="responsibilities" id="responsibilities" x-model="responsibilities" rows="3" class="form-input text-xs" placeholder="Responsibilities (Optional)..."></textarea>
                        </div>
                    </div>
                </div>

                <!-- File attachments requirement toggles -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-[#F7F8FA] p-4 rounded-xl border border-gray-200">
                    <!-- Requires Photo -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800">Profile Photo</span>
                            <span class="text-[10px] text-gray-500">Require candidate headshot.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_photo" value="1" class="sr-only peer" checked>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                        </label>
                    </div>

                    <!-- Requires CV -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800">Require CV / Resume</span>
                            <span class="text-[10px] text-gray-500">Require document upload.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_cv" value="1" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                        </label>
                    </div>

                    <!-- Requires Video -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800">Require Video</span>
                            <span class="text-[10px] text-gray-500">Require video intro.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_video" x-model="requiresVideo" value="1" class="sr-only peer">
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                        </label>
                    </div>
                </div>

                <!-- Video prompt description -->
                <div x-show="requiresVideo" class="animate-fade-in" style="display: none;">
                    <label for="video_prompt" class="form-label">Video Introduction Prompt <span class="text-red-500">*</span></label>
                    <textarea name="video_prompt" id="video_prompt" rows="3" class="form-input text-xs" placeholder="Instructions for the video (e.g. Introduce yourself and explain why you want to work at Munchify in 60s)..." :required="requiresVideo"></textarea>
                </div>
            </div>

            <!-- Screening Questions Builder -->
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-[#FF6B00]"></i> Screening Questions
                    </h3>
                    <div class="flex items-center gap-2">
                        <button type="button" @click="generateAiQuestions()" :disabled="generatingAiQuestions" class="btn bg-gradient-to-r from-[#FF6B00] to-[#E05D00] text-white btn-sm py-1.5 px-3.5 font-bold text-[11px] rounded-full flex items-center gap-1.5 shadow-sm hover:opacity-90 transition">
                            <i class="fa-solid fa-wand-magic-sparkles text-[#FFD233]" x-show="!generatingAiQuestions"></i>
                            <i class="fa-solid fa-circle-notch fa-spin text-white" x-show="generatingAiQuestions" style="display: none;"></i>
                            <span x-text="generatingAiQuestions ? 'Generating...' : 'AI Suggest Questions'"></span>
                        </button>
                        <button type="button" @click="addQuestion()" class="btn btn-secondary btn-sm py-1.5 px-4 font-bold text-[11px] rounded-full">
                            <i class="fa-solid fa-plus text-[9px]"></i> Add Question
                        </button>
                    </div>
                </div>

                <div class="space-y-4">
                    <template x-for="(q, index) in screeningQuestions" :key="index">
                        <div class="p-4 bg-[#F7F8FA] border border-gray-200 rounded-xl space-y-4 relative animate-fade-in">
                            <button type="button" @click="removeQuestion(index)" class="absolute top-2 right-2 text-gray-400 hover:text-red-500 transition">
                                <i class="fa-solid fa-trash text-xs"></i>
                            </button>

                            <div>
                                <label class="form-label text-[11px]">Question text</label>
                                <input type="text" :name="`screening_questions[${index}][question]`" x-model="q.question" class="form-input text-xs" placeholder="e.g. Do you have a valid driver's license?" required>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                <!-- Type select -->
                                <div>
                                    <label class="form-label text-[11px]">Answer Type</label>
                                    <select :name="`screening_questions[${index}][type]`" x-model="q.type" class="form-input text-xs" required>
                                        <option value="boolean">Yes / No (Boolean)</option>
                                        <option value="number">Number</option>
                                        <option value="text">Short Text</option>
                                    </select>
                                </div>

                                <!-- Knockout toggle -->
                                <div class="flex items-center gap-2 pt-6">
                                    <label class="flex items-center text-xs font-bold text-gray-750 cursor-pointer">
                                        <input type="checkbox" :name="`screening_questions[${index}][knockout]`" x-model="q.knockout" value="1" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 mr-2">
                                        Knockout Criteria?
                                    </label>
                                </div>

                                <!-- Expected value details based on knockout -->
                                <div x-show="q.knockout">
                                    <!-- If boolean -->
                                    <div x-show="q.type === 'boolean'">
                                        <label class="form-label text-[11px]">Expected Value</label>
                                        <select :name="`screening_questions[${index}][expected]`" class="form-input text-xs">
                                            <option value="1">Yes (True)</option>
                                            <option value="0">No (False)</option>
                                        </select>
                                    </div>
                                    <!-- If number -->
                                    <div x-show="q.type === 'number'" style="display: none;">
                                        <label class="form-label text-[11px]">Min Value Allowed</label>
                                        <input type="number" :name="`screening_questions[${index}][min]`" class="form-input text-xs" placeholder="e.g. 18">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </template>

                    <div x-show="screeningQuestions.length === 0" class="text-center py-6 text-gray-400 text-xs">
                        No screening questions added. Add one above to filter candidates during submission.
                    </div>
                </div>
            </div>

        </div>

        <!-- RIGHT COLUMN: Assignments and Hiring Manager -->
        <div class="space-y-6">
            
            <!-- Hiring Manager select card -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                    <i class="fa-solid fa-user-tie text-[#FF6B00]"></i> Job Owner / Coordinator
                </h3>
                
                <div>
                    <label for="hiring_manager_id" class="form-label">Hiring Manager <span class="text-red-500">*</span></label>
                    <select name="hiring_manager_id" id="hiring_manager_id" class="form-input text-xs" required>
                        <option value="">-- Choose Lead Manager --</option>
                        @foreach($hiringManagers as $manager)
                            <option value="{{ $manager->id }}">{{ $manager->full_name }} ({{ $manager->email }})</option>
                        @endforeach
                    </select>
                    <span class="form-help block mt-1">This user will coordinate reviews and own scheduling.</span>
                </div>
            </div>

            <!-- Team Reviewers Assignment -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                    <i class="fa-solid fa-people-group text-[#FF6B00]"></i> Assigned Team Reviewers
                </h3>
                
                <p class="text-[11px] text-gray-400 leading-normal">
                    Select team members who need access to view, score, or schedule interviews for this role listing.
                </p>

                <div class="space-y-3.5 max-h-80 overflow-y-auto pr-1">
                    @foreach($teamMembers as $member)
                        <div class="flex items-center justify-between border-b border-gray-50 pb-2.5 last:border-0 last:pb-0" x-data="{ checked: false }">
                            <div class="flex items-center min-w-0">
                                <input type="checkbox" id="member_{{ $member->id }}" @change="checked = !checked" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 mr-2 shrink-0">
                                <label for="member_{{ $member->id }}" class="flex flex-col cursor-pointer min-w-0">
                                    <span class="font-bold text-xs text-gray-800 truncate">{{ $member->full_name }}</span>
                                    <span class="text-[9px] text-gray-400 truncate capitalize">{{ $member->role_label }}</span>
                                </label>
                            </div>
                            
                            <!-- Role assignment in this job (only if checkbox is checked) -->
                            <div x-show="checked" class="animate-fade-in" style="display: none;">
                                <select :name="`assigned_team[{{ $member->id }}]`" :required="checked" class="form-input py-1 px-2 text-[10px] w-28">
                                    <option value="reviewer">Reviewer</option>
                                    <option value="interviewer">Interviewer</option>
                                    <option value="hiring_manager">Hiring Manager</option>
                                    <option value="viewer">Viewer</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Publish Actions -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                <button type="submit" class="btn btn-primary w-full py-3 shadow-lg shadow-[#FF6B00]/15">
                    Save Draft Opening
                </button>
                <a href="{{ route('jobs.manage') }}" class="btn btn-secondary w-full py-3">
                    Cancel
                </a>
            </div>

        </div>

    </div>
</form>
@endsection

@section('scripts')
<script>
    function jobFormHandler() {
        return {
            requiresVideo: false,
            description: '',
            requirements: '',
            responsibilities: '',
            generatingAi: false,
            generatingAiQuestions: false,
            addQuestion() {
                this.screeningQuestions.push({
                    question: '',
                    type: 'boolean',
                    knockout: false,
                    expected: '1',
                    min: ''
                });
            },
            removeQuestion(index) {
                this.screeningQuestions.splice(index, 1);
            },
            async generateAiQuestions() {
                const titleInput = document.getElementById('title')?.value?.trim();
                if (!titleInput) {
                    alert('Please enter a Job Title first so the AI bot can suggest relevant questions.');
                    return;
                }
                this.generatingAiQuestions = true;
                
                try {
                    const response = await fetch('/dashboard/jobs/ai-generate-questions', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title: titleInput })
                    });
                    
                    const data = await response.json();
                    if (data.success && data.questions) {
                        this.screeningQuestions = data.questions;
                    } else {
                        alert(data.message || 'Failed to suggest questions.');
                    }
                } catch (e) {
                    alert('Error connecting to AI assistant.');
                } finally {
                    this.generatingAiQuestions = false;
                }
            },
            async generateAiContent() {
                const titleInput = document.getElementById('title')?.value?.trim();
                if (!titleInput) {
                    alert('Please enter a Job Title first so the AI bot can tailor the description.');
                    return;
                }
                this.generatingAi = true;
                
                try {
                    const response = await fetch('/dashboard/jobs/ai-generate', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ title: titleInput })
                    });
                    
                    const data = await response.json();
                    if (data.success) {
                        this.description = data.description;
                        this.requirements = data.requirements;
                        this.responsibilities = data.responsibilities;
                    } else {
                        alert(data.message || 'Failed to generate AI content.');
                    }
                } catch (e) {
                    alert('Error connecting to AI assistant.');
                } finally {
                    this.generatingAi = false;
                }
            }
        }
    }
</script>
@endsection
