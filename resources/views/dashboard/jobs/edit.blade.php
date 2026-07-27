@extends('layouts.dashboard')

@section('title', 'Edit Job Opening - Recruiter Dashboard')
@section('header_title', 'Edit Job: ' . $job->title)

@section('content')
<form action="{{ route('jobs.edit', ['id' => $job->id]) }}" method="POST" class="space-y-6" x-data="jobFormHandler()">
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
                    <input type="text" name="title" id="title" class="form-input text-xs" placeholder="e.g. Lead Delivery Rider" value="{{ old('title', $job->title) }}" required>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Department -->
                    <div>
                        <label for="department_id" class="form-label">Department <span class="text-red-500">*</span></label>
                        <select name="department_id" id="department_id" class="form-input text-xs" required>
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                                <option value="{{ $dept->id }}" {{ $job->department_id == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Pipeline hint (Disabled on edit to prevent data mismatch, but shows static label) -->
                    <div>
                        <label class="form-label">Recruitment Pipeline</label>
                        <input type="text" class="form-input text-xs bg-gray-50 border-gray-200 text-gray-500 cursor-not-allowed" value="{{ $job->pipelineTemplate->name ?? 'N/A' }}" disabled>
                        <span class="form-help text-[10px]">Pipeline cannot be changed after creation.</span>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Job Type -->
                    <div>
                        <label for="type" class="form-label">Employment Type <span class="text-red-500">*</span></label>
                        <select name="type" id="type" class="form-input text-xs" required>
                            <option value="full_time" {{ $job->type === 'full_time' ? 'selected' : '' }}>Full-time</option>
                            <option value="part_time" {{ $job->type === 'part_time' ? 'selected' : '' }}>Part-time</option>
                            <option value="contract" {{ $job->type === 'contract' ? 'selected' : '' }}>Contract</option>
                            <option value="internship" {{ $job->type === 'internship' ? 'selected' : '' }}>Internship</option>
                        </select>
                    </div>

                    <!-- Work setting -->
                    <div>
                        <label for="location" class="form-label">Workplace Setting <span class="text-red-500">*</span></label>
                        <select name="location" id="location" class="form-input text-xs" required>
                            <option value="on_site" {{ $job->location === 'on_site' ? 'selected' : '' }}>On-site</option>
                            <option value="remote" {{ $job->location === 'remote' ? 'selected' : '' }}>Remote</option>
                            <option value="hybrid" {{ $job->location === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                        </select>
                    </div>

                    <!-- Location Detail -->
                    <div>
                        <label for="location_detail" class="form-label">Location Detail</label>
                        <input type="text" name="location_detail" id="location_detail" class="form-input text-xs" placeholder="e.g. Maseno Kitchen, Main Gate" value="{{ old('location_detail', $job->location_detail) }}">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <!-- Salary Range -->
                    <div>
                        <label for="salary_range" class="form-label">Salary Range / Compensation</label>
                        <input type="text" name="salary_range" id="salary_range" class="form-input text-xs" placeholder="e.g. KES 20,000 - 30,000" value="{{ old('salary_range', $job->salary_range) }}">
                    </div>

                    <!-- Openings slots -->
                    <div>
                        <label for="slots" class="form-label">Slots Available <span class="text-red-500">*</span></label>
                        <input type="number" name="slots" id="slots" class="form-input text-xs" value="{{ old('slots', $job->slots) }}" min="1" required>
                    </div>

                    <!-- Deadline -->
                    <div>
                        <label for="application_deadline" class="form-label">Application Deadline</label>
                        <input type="date" name="application_deadline" id="application_deadline" class="form-input text-xs" value="{{ $job->application_deadline ? $job->application_deadline->format('Y-m-d') : '' }}">
                    </div>
                </div>

                <!-- Primary textareas description/requirements/responsibilities -->
                <div class="space-y-4">
                    <div>
                        <label for="description" class="form-label">Job Description <span class="text-red-500">*</span></label>
                        <textarea name="description" id="description" rows="5" class="form-input text-xs" placeholder="Outline the job description (Accepts HTML)..." required>{{ old('description', $job->description) }}</textarea>
                    </div>

                    <div>
                        <label for="requirements" class="form-label">Requirements <span class="text-red-500">*</span></label>
                        <textarea name="requirements" id="requirements" rows="4" class="form-input text-xs" placeholder="List the requirements (one per line, accepts HTML)..." required>{{ old('requirements', $job->requirements) }}</textarea>
                    </div>

                    <div>
                        <label for="responsibilities" class="form-label">Responsibilities <span class="text-red-500">*</span></label>
                        <textarea name="responsibilities" id="responsibilities" rows="4" class="form-input text-xs" placeholder="List the responsibilities (one per line, accepts HTML)..." required>{{ old('responsibilities', $job->responsibilities) }}</textarea>
                    </div>
                </div>

                <!-- File attachments requirement toggles -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 bg-[#F7F8FA] p-4 rounded-xl border border-gray-200">
                    <!-- Requires CV -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800">Require CV upload</span>
                            <span class="text-[10px] text-gray-500">Candidates must upload a PDF/Word resume.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_cv" value="1" class="sr-only peer" {{ $job->requires_cv ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                        </label>
                    </div>

                    <!-- Requires Video -->
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs font-bold text-gray-800">Require Video Intro</span>
                            <span class="text-[10px] text-gray-500">Candidates must record a short video intro.</span>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_video" x-model="requiresVideo" value="1" class="sr-only peer" {{ $job->requires_video ? 'checked' : '' }}>
                            <div class="w-9 h-5 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-[#FF6B00]"></div>
                        </label>
                    </div>
                </div>

                <!-- Video prompt description -->
                <div x-show="requiresVideo" class="animate-fade-in" style="display: none;">
                    <label for="video_prompt" class="form-label">Video Introduction Prompt <span class="text-red-500">*</span></label>
                    <textarea name="video_prompt" id="video_prompt" rows="3" class="form-input text-xs" placeholder="Instructions for the video (e.g. Introduce yourself and explain why you want to work at Munchify in 60s)..." :required="requiresVideo">{{ old('video_prompt', $job->video_prompt) }}</textarea>
                </div>
            </div>

            <!-- Screening Questions Builder -->
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm space-y-6">
                <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-4">
                    <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                        <i class="fa-solid fa-circle-question text-[#FF6B00]"></i> Screening Questions
                    </h3>
                    <button type="button" @click="addQuestion()" class="btn btn-secondary btn-sm py-1.5 px-4 font-bold text-[11px] rounded-full">
                        <i class="fa-solid fa-plus text-[9px]"></i> Add Question
                    </button>
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
                                        <select :name="`screening_questions[${index}][expected]`" x-model="q.expected" class="form-input text-xs">
                                            <option value="1">Yes (True)</option>
                                            <option value="0">No (False)</option>
                                        </select>
                                    </div>
                                    <!-- If number -->
                                    <div x-show="q.type === 'number'" style="display: none;">
                                        <label class="form-label text-[11px]">Min Value Allowed</label>
                                        <input type="number" :name="`screening_questions[${index}][min]`" x-model="q.min" class="form-input text-xs" placeholder="e.g. 18">
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
                            <option value="{{ $manager->id }}" {{ $job->hiring_manager_id == $manager->id ? 'selected' : '' }}>{{ $manager->full_name }} ({{ $manager->email }})</option>
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
                        @php
                            $isAssigned = array_key_exists($member->id, $assignments);
                            $assignedRole = $assignments[$member->id] ?? 'reviewer';
                        @endphp
                        <div class="flex items-center justify-between border-b border-gray-50 pb-2.5 last:border-0 last:pb-0" x-data="{ checked: {{ $isAssigned ? 'true' : 'false' }} }">
                            <div class="flex items-center min-w-0">
                                <input type="checkbox" id="member_{{ $member->id }}" @change="checked = !checked" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 mr-2 shrink-0" {{ $isAssigned ? 'checked' : '' }}>
                                <label for="member_{{ $member->id }}" class="flex flex-col cursor-pointer min-w-0">
                                    <span class="font-bold text-xs text-gray-800 truncate">{{ $member->full_name }}</span>
                                    <span class="text-[9px] text-gray-400 truncate capitalize">{{ $member->role_label }}</span>
                                </label>
                            </div>
                            
                            <!-- Role assignment in this job (only if checkbox is checked) -->
                            <div x-show="checked" class="animate-fade-in" style="display: none;">
                                <select :name="`assigned_team[{{ $member->id }}]`" :required="checked" class="form-input py-1 px-2 text-[10px] w-28">
                                    <option value="reviewer" {{ $assignedRole === 'reviewer' ? 'selected' : '' }}>Reviewer</option>
                                    <option value="interviewer" {{ $assignedRole === 'interviewer' ? 'selected' : '' }}>Interviewer</option>
                                    <option value="hiring_manager" {{ $assignedRole === 'hiring_manager' ? 'selected' : '' }}>Hiring Manager</option>
                                    <option value="viewer" {{ $assignedRole === 'viewer' ? 'selected' : '' }}>Viewer</option>
                                </select>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Publish Actions -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-3">
                <button type="submit" class="btn btn-primary w-full py-3 shadow-lg shadow-[#FF6B00]/15">
                    Save Changes
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
            requiresVideo: {{ $job->requires_video ? 'true' : 'false' }},
            screeningQuestions: @json($job->screening_questions ?? []),
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
            }
        }
    }
</script>
@endsection
