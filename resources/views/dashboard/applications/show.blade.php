@extends('layouts.dashboard')

@section('title', $application->full_name . ' - Profile | Munchify Careers')
@section('header_title', 'Candidate Profile')

@section('content')
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8" x-data="candidateDetail('{{ csrf_token() }}')">
    
    <!-- LEFT PANEL: Candidate Details -->
    <div class="col-span-1 space-y-6">
        
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col items-center text-center relative">
            <!-- Star toggle -->
            <button type="button" @click="toggleStar()" class="absolute top-4 right-4 text-gray-300 hover:text-[#FFD233] transition">
                <i class="fa-solid fa-star text-lg" :class="isStarred ? 'text-[#FFD233] star-filled' : 'text-gray-300 star-empty'"></i>
            </button>

            <!-- Initials Avatar -->
            <div class="w-20 h-20 rounded-full bg-orange-50 border-2 border-orange-100 text-[#FF6B00] flex items-center justify-center font-extrabold text-2xl uppercase mb-4 shadow-sm">
                {{ $application->initials }}
            </div>

            <!-- Name & App Number -->
            <h3 class="font-extrabold text-lg text-[#111318] mb-0.5 leading-snug">{{ $application->full_name }}</h3>
            <span class="app-number text-[10px] mb-4">{{ $application->application_number }}</span>
            
            <div class="flex flex-wrap gap-1.5 justify-center mb-6">
                <span class="badge {{ $application->status_badge_class }}">{{ ucfirst($application->status) }}</span>
                <span class="badge badge-gray border border-gray-200">{{ $application->currentStage->name ?? 'N/A' }}</span>
                @if($application->is_knockout)
                    <span class="badge badge-red uppercase py-0.5 text-[8px] font-extrabold tracking-wider">Knockout</span>
                @endif
            </div>

            <!-- Basic stats (Overall score) -->
            <div class="w-full grid grid-cols-2 gap-4 border-t border-b border-gray-150/60 py-4 mb-6 text-left text-xs">
                <div>
                    <span class="text-gray-400 font-semibold block mb-0.5">Overall Evaluation</span>
                    @if($application->overall_score)
                        <span class="font-black text-sm text-[#FF6B00] flex items-center gap-0.5">
                            <i class="fa-solid fa-star text-xs"></i> {{ $application->overall_score }} <span class="text-[10px] text-gray-400 font-semibold">/ 5</span>
                        </span>
                    @else
                        <span class="font-bold text-gray-400">Unevaluated</span>
                    @endif
                </div>
                <div>
                    <span class="text-gray-400 font-semibold block mb-0.5">Source / Channel</span>
                    <span class="font-bold text-[#111318]">{{ $application->source_label }}</span>
                </div>
            </div>

            <!-- Contact info -->
            <div class="w-full text-left space-y-3.5 text-xs font-semibold">
                <div class="flex items-center gap-3">
                    <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                        <i class="fa-regular fa-envelope"></i>
                    </div>
                    <div class="min-w-0">
                        <span class="text-[10px] text-gray-400 block font-medium">Email Address</span>
                        <a href="mailto:{{ $application->email }}" class="text-gray-700 truncate block hover:text-[#FF6B00]">{{ $application->email }}</a>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="w-8 h-8 rounded-xl bg-gray-50 flex items-center justify-center text-gray-400 shrink-0">
                            <i class="fa-solid fa-phone"></i>
                        </div>
                        <div class="min-w-0">
                            <span class="text-[10px] text-gray-400 block font-medium">Mobile Number</span>
                            <span class="text-gray-700 block">+254 {{ $application->phone }}</span>
                        </div>
                    </div>
                    
                    <a href="{{ $application->whatsapp_url }}" target="_blank" class="btn btn-secondary btn-icon py-1.5 px-2 bg-emerald-50 hover:bg-emerald-100 hover:border-emerald-200 text-emerald-600 border border-emerald-100 rounded-lg flex items-center gap-1.5 text-[10px] font-bold">
                        <i class="fa-brands fa-whatsapp text-xs"></i> Chat
                    </a>
                </div>
            </div>
        </div>

        <!-- STAGE TRANSITION CONTROL CARD -->
        @if($application->status === 'active')
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
                <h4 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                    <i class="fa-solid fa-arrows-split-up-and-left text-[#FF6B00]"></i> Update Pipeline Stage
                </h4>

                <form action="{{ route('applications.move-stage', ['application' => $application->id]) }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label for="stage_id" class="form-label text-xs">Pipeline Stage</label>
                        <select name="stage_id" id="stage_id" class="form-input text-xs" required>
                            @foreach($stages as $stage)
                                <option value="{{ $stage->id }}" {{ $application->current_stage_id == $stage->id ? 'selected' : '' }}>
                                    {{ $stage->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label for="stage_note" class="form-label text-xs">Movement Notes (Optional)</label>
                        <textarea name="note" id="stage_note" rows="2" class="form-input text-xs" placeholder="Add internal details regarding movement..."></textarea>
                    </div>

                    <div class="flex gap-2">
                        <button type="submit" class="btn btn-primary btn-sm flex-grow rounded-xl font-bold py-2.5">
                            Update Stage
                        </button>
                    </div>
                </form>

                <div class="flex gap-2 border-t border-gray-100 pt-4">
                    <!-- Reject action -->
                    <button type="button" @click="rejectModal = true" class="btn btn-danger btn-sm flex-grow rounded-xl py-2.5 font-bold">
                        <i class="fa-solid fa-ban text-[10px]"></i> Reject Candidate
                    </button>
                    <!-- Hire action -->
                    <button type="button" @click="hireModal = true" class="btn btn-success btn-sm flex-grow rounded-xl py-2.5 font-bold">
                        <i class="fa-solid fa-trophy text-[10px]"></i> Hire Candidate
                    </button>
                </div>
            </div>
        @endif

        <!-- CV & VIDEO ATTACHMENTS CARD -->
        <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-4">
            <h4 class="font-extrabold text-sm text-[#111318] flex items-center gap-2">
                <i class="fa-solid fa-file-arrow-up text-[#FF6B00]"></i> Submitted Materials
            </h4>

            <div class="space-y-3">
                @if($application->cv_path)
                    <a href="{{ Storage::url($application->cv_path) }}" target="_blank" class="flex items-center justify-between p-3.5 bg-red-50/20 border border-red-200/50 text-red-800 hover:bg-red-50/40 rounded-xl transition duration-150">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fa-regular fa-file-pdf text-red-500 text-xl"></i>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-xs text-[#111318] truncate">Curriculum Vitae</span>
                                <span class="text-[9px] text-gray-400">PDF / Document</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-download text-xs"></i>
                    </a>
                @else
                    <div class="p-3 text-center bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-400 font-semibold">
                        No CV attached
                    </div>
                @endif

                @if($application->video_path)
                    <a href="{{ Storage::url($application->video_path) }}" target="_blank" class="flex items-center justify-between p-3.5 bg-orange-50/20 border border-orange-200/50 text-[#FF6B00] hover:bg-orange-50/40 rounded-xl transition duration-150">
                        <div class="flex items-center gap-2.5 min-w-0">
                            <i class="fa-regular fa-file-video text-[#FF6B00] text-xl"></i>
                            <div class="flex flex-col min-w-0">
                                <span class="font-bold text-xs text-[#111318] truncate">Introduction Video</span>
                                <span class="text-[9px] text-gray-400">MP4 / Video</span>
                            </div>
                        </div>
                        <i class="fa-solid fa-play text-xs"></i>
                    </a>
                @else
                    <div class="p-3 text-center bg-gray-50 border border-gray-150 rounded-xl text-xs text-gray-400 font-semibold">
                        No Video intro submitted
                    </div>
                @endif
            </div>
        </div>

    </div>

    <!-- RIGHT PANEL: Tabs Container -->
    <div class="col-span-1 lg:col-span-2 space-y-6" x-data="{ activeTab: 'details' }">
        
        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden flex flex-col min-h-[500px]">
            <!-- Tab buttons navigation -->
            <div class="tab-nav bg-[#F9FAFB] px-4">
                <button @click="activeTab = 'details'" class="tab-button" :class="activeTab === 'details' ? 'tab-button-active' : ''">Candidate Info</button>
                <button @click="activeTab = 'eval'" class="tab-button" :class="activeTab === 'eval' ? 'tab-button-active' : ''">Evaluation</button>
                <button @click="activeTab = 'notes'" class="tab-button" :class="activeTab === 'notes' ? 'tab-button-active' : ''">Notes</button>
                <button @click="activeTab = 'interviews'" class="tab-button" :class="activeTab === 'interviews' ? 'tab-button-active' : ''">Interviews</button>
                <button @click="activeTab = 'comms'" class="tab-button" :class="activeTab === 'comms' ? 'tab-button-active' : ''">Communications</button>
            </div>

            <!-- Tab content cards -->
            <div class="p-6 md:p-8 flex-grow">
                
                <!-- 1. TABS: Candidate Info details -->
                <div x-show="activeTab === 'details'" class="space-y-6 animate-fade-in">
                    <!-- Professional Profile -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-6 border-b border-gray-100">
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Current Role / Status</span>
                            <span class="font-extrabold text-xs text-gray-800 block mt-1">{{ $application->current_role ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-xs text-gray-400 font-medium block">Experience Years</span>
                            <span class="font-extrabold text-xs text-gray-800 block mt-1">{{ $application->experience_years ?? 'N/A' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-xs text-gray-400 font-medium block">Key Skills</span>
                            <span class="font-extrabold text-xs text-gray-850 block mt-1">{{ $application->skills ?? 'N/A' }}</span>
                        </div>
                        @if($application->motivation)
                        <div class="col-span-2">
                            <span class="text-xs text-gray-400 font-medium block">Motivation</span>
                            <p class="text-xs text-gray-600 leading-relaxed mt-1 whitespace-pre-line">{{ $application->motivation }}</p>
                        </div>
                        @endif
                        @if($application->cover_letter)
                        <div class="col-span-2">
                            <span class="text-xs text-gray-400 font-medium block">Cover Letter</span>
                            <p class="text-xs text-gray-600 leading-relaxed mt-1 whitespace-pre-line bg-gray-50 border border-gray-100 p-4 rounded-xl max-h-60 overflow-y-auto">{{ $application->cover_letter }}</p>
                        </div>
                        @endif
                    </div>

                    <!-- Screening Questions and Answers -->
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318] mb-4 flex items-center gap-1.5"><i class="fa-solid fa-circle-question text-[#FF6B00]"></i> Screening Answers</h4>
                        
                        <div class="space-y-4">
                            @forelse($application->screening_answers ?? [] as $ans)
                                <div class="bg-[#F7F8FA] border border-gray-150 p-4 rounded-xl text-xs space-y-1">
                                    <span class="text-gray-400 font-medium leading-normal block">{{ $ans['question'] }}</span>
                                    <span class="font-extrabold text-[#111318] block">
                                        @if(is_bool($ans['answer']))
                                            {{ $ans['answer'] ? 'Yes' : 'No' }}
                                        @else
                                            {{ $ans['answer'] }}
                                        @endif
                                    </span>
                                </div>
                            @empty
                                <div class="text-center py-6 text-gray-400 text-xs italic">No screening questions were required.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 2. TABS: Evaluation Form and scores list -->
                <div x-show="activeTab === 'eval'" class="space-y-8 animate-fade-in" style="display: none;">
                    
                    <!-- Evaluation form (only if active) -->
                    @if($application->status === 'active')
                        <div class="bg-gray-50 border border-gray-250/50 p-6 rounded-2xl space-y-4">
                            <h4 class="font-extrabold text-xs text-[#111318] flex items-center gap-1.5"><i class="fa-solid fa-star-half-stroke text-[#FF6B00]"></i> Evaluate Candidate for: {{ $application->currentStage->name }}</h4>
                            
                            <form action="{{ route('applications.score', ['application' => $application->id]) }}" method="POST" class="space-y-4">
                                @csrf
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <!-- Score (1-5) -->
                                    <div>
                                        <label for="score" class="form-label text-xs">Score rating</label>
                                        <select name="score" id="score" class="form-input text-xs" required>
                                            <option value="">-- Rate Score --</option>
                                            <option value="5" {{ $existingScore && $existingScore->score === 5 ? 'selected' : '' }}>5 - Excellent</option>
                                            <option value="4" {{ $existingScore && $existingScore->score === 4 ? 'selected' : '' }}>4 - Good</option>
                                            <option value="3" {{ $existingScore && $existingScore->score === 3 ? 'selected' : '' }}>3 - Average</option>
                                            <option value="2" {{ $existingScore && $existingScore->score === 2 ? 'selected' : '' }}>2 - Poor</option>
                                            <option value="1" {{ $existingScore && $existingScore->score === 1 ? 'selected' : '' }}>1 - Failed</option>
                                        </select>
                                    </div>

                                    <!-- Recommendation -->
                                    <div>
                                        <label for="recommendation" class="form-label text-xs">Recommendation</label>
                                        <select name="recommendation" id="recommendation" class="form-input text-xs" required>
                                            <option value="">-- Choose Recommendation --</option>
                                            <option value="strong_yes" {{ $existingScore && $existingScore->recommendation === 'strong_yes' ? 'selected' : '' }}>Strong Yes</option>
                                            <option value="yes" {{ $existingScore && $existingScore->recommendation === 'yes' ? 'selected' : '' }}>Yes</option>
                                            <option value="maybe" {{ $existingScore && $existingScore->recommendation === 'maybe' ? 'selected' : '' }}>Maybe</option>
                                            <option value="no" {{ $existingScore && $existingScore->recommendation === 'no' ? 'selected' : '' }}>No</option>
                                            <option value="strong_no" {{ $existingScore && $existingScore->recommendation === 'strong_no' ? 'selected' : '' }}>Strong No</option>
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label for="notes" class="form-label text-xs">Evaluation Notes</label>
                                    <textarea name="notes" id="notes" rows="3" class="form-input text-xs placeholder:text-gray-400" placeholder="State observations and reasoning..." required>{{ $existingScore->notes ?? '' }}</textarea>
                                </div>

                                <button type="submit" class="btn btn-primary btn-sm px-6 py-2.5 rounded-xl font-bold">
                                    Submit Evaluation
                                </button>
                            </form>
                        </div>
                    @endif

                    <!-- List of past scores -->
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318] mb-4 flex items-center gap-1.5"><i class="fa-solid fa-list-check text-[#FF6B00]"></i> Evaluation History</h4>
                        <div class="space-y-4">
                            @forelse($application->scores as $score)
                                <div class="border border-gray-150 rounded-xl p-4 space-y-2.5 bg-white text-xs">
                                    <div class="flex justify-between items-center">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-gray-850">{{ $score->user->full_name }}</span>
                                            <span class="text-[10px] text-gray-400 font-semibold bg-gray-50 border border-gray-100 rounded-full px-2 py-0.5">{{ $score->stage->name }}</span>
                                        </div>
                                        <span class="font-bold flex items-center gap-0.5 text-[#FF6B00]">
                                            <i class="fa-solid fa-star text-[10px]"></i> {{ $score->score }} / 5
                                        </span>
                                    </div>
                                    <p class="text-gray-650 leading-relaxed">{{ $score->notes }}</p>
                                    <div class="flex justify-between items-center text-[9px] text-gray-400 font-semibold pt-1">
                                        <span>Recommendation: <strong class="text-[#FF6B00] uppercase">{{ str_replace('_', ' ', $score->recommendation) }}</strong></span>
                                        <span>{{ $score->created_at->format('M d, Y H:i') }}</span>
                                    </div>
                                </div>
                            @empty
                                <div class="text-center py-8 text-gray-400 text-xs italic">No evaluations submitted yet.</div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- 3. TABS: Notes comments list -->
                <div x-show="activeTab === 'notes'" class="space-y-6 animate-fade-in" style="display: none;">
                    
                    <!-- New Note Form -->
                    <form action="{{ route('applications.note', ['application' => $application->id]) }}" method="POST" class="space-y-4 bg-gray-50 border border-gray-150 p-4 rounded-xl">
                        @csrf
                        <div>
                            <label for="body" class="form-label text-xs">Internal Comment / Note</label>
                            <textarea name="body" id="body" rows="2" class="form-input text-xs" placeholder="Add observations..." required></textarea>
                        </div>
                        
                        <div class="flex justify-between items-center">
                            <div>
                                <label class="flex items-center text-xs font-semibold text-gray-650 cursor-pointer">
                                    <input type="checkbox" name="is_private" value="1" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 mr-2">
                                    Private to me?
                                </label>
                            </div>
                            <button type="submit" class="btn btn-secondary btn-sm py-2 px-5 font-bold rounded-xl text-xs">
                                Add Note
                            </button>
                        </div>
                    </form>

                    <!-- Comments list -->
                    <div class="space-y-4">
                        @forelse($application->notes as $note)
                            <!-- Skip if private and not owned by current user -->
                            @if(!$note->is_private || $note->user_id === Auth::id())
                                <div class="border border-gray-150 bg-white rounded-xl p-4 text-xs">
                                    <div class="flex justify-between items-center mb-1.5">
                                        <div class="flex items-center gap-2">
                                            <span class="font-extrabold text-gray-800">{{ $note->user->full_name }}</span>
                                            @if($note->is_private)
                                                <span class="text-[9px] bg-red-50 text-red-500 font-bold border border-red-100 rounded-full px-1.5 py-0.5">Private</span>
                                            @endif
                                        </div>
                                        <span class="text-[9px] text-gray-400 font-semibold">{{ $note->created_at->diffForHumans() }}</span>
                                    </div>
                                    <p class="text-gray-650 leading-relaxed">{{ $note->body }}</p>
                                </div>
                            @endif
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs italic">No internal comments added.</div>
                        @endforelse
                    </div>

                </div>

                <!-- 4. TABS: Interviews & Schedule links -->
                <div x-show="activeTab === 'interviews'" class="space-y-6 animate-fade-in" style="display: none;">
                    
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-2">
                        <h4 class="font-extrabold text-xs text-[#111318] flex items-center gap-1.5"><i class="fa-solid fa-calendar-check text-[#FF6B00]"></i> Interviews Schedule</h4>
                        @if($application->status === 'active')
                            <!-- Trigger schedule modal -->
                            <button type="button" @click="scheduleModal = true" class="btn btn-secondary btn-sm py-1.5 px-4 font-bold text-[11px] rounded-full">
                                <i class="fa-solid fa-plus text-[9px]"></i> Schedule Interview
                            </button>
                        @endif
                    </div>

                    <!-- Interviews list -->
                    <div class="space-y-4">
                        @forelse($application->interviews as $interview)
                            <div class="border border-gray-150 rounded-xl p-4 bg-white text-xs space-y-3" x-data="{ feedbackOpen: false }">
                                <div class="flex justify-between items-center">
                                    <div class="flex flex-col">
                                        <span class="font-extrabold text-gray-850">Interviewer: {{ $interview->interviewer->full_name }}</span>
                                        <span class="text-[9px] text-[#FF6B00] font-bold mt-0.5">Stage: {{ $interview->stage->name }}</span>
                                    </div>
                                    <span class="badge {{ $interview->status === 'completed' ? 'badge-green' : 'badge-orange' }}">
                                        {{ ucfirst($interview->status) }}
                                    </span>
                                </div>

                                <div class="grid grid-cols-2 gap-4 text-[11px] font-semibold bg-gray-50 p-3 rounded-lg text-gray-650">
                                    <div>
                                        <span class="text-gray-400 font-medium block">Date & Time</span>
                                        <span>{{ $interview->scheduled_at->format('M d, Y @ h:i A') }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-400 font-medium block">Meeting Channel / Location</span>
                                        @if(str_starts_with($interview->location_or_link, 'http'))
                                            <a href="{{ $interview->location_or_link }}" target="_blank" class="text-[#FF6B00] underline truncate block">{{ $interview->location_or_link }}</a>
                                        @else
                                            <span class="truncate block">{{ $interview->location_or_link }}</span>
                                        @endif
                                    </div>
                                </div>

                                @if($interview->notes)
                                    <div class="text-[11px] bg-[#F7F8FA] p-3 rounded-lg border border-gray-100">
                                        <span class="text-gray-400 font-bold block mb-1">Notes & Feedback:</span>
                                        <p class="leading-relaxed whitespace-pre-line">{{ $interview->notes }}</p>
                                    </div>
                                @endif

                                <!-- Submit feedback inline button -->
                                @if($interview->status === 'scheduled' && (Auth::id() === $interview->interviewer_id || Auth::user()->canManageJobs()))
                                    <div class="border-t border-gray-100 pt-3">
                                        <button type="button" @click="feedbackOpen = !feedbackOpen" class="btn btn-secondary btn-sm py-1.5 px-4 text-[10px] font-bold rounded-xl">
                                            <i class="fa-regular fa-message mr-1"></i> Submit Interview Feedback
                                        </button>
                                        
                                        <!-- Feedback form inline -->
                                        <div x-show="feedbackOpen" class="mt-4 p-4 border border-gray-200 bg-gray-50 rounded-xl space-y-4 animate-fade-in" style="display: none;">
                                            <form action="{{ route('interviews.feedback', ['interview' => $interview->id]) }}" method="POST" class="space-y-4">
                                                @csrf
                                                <div>
                                                    <label for="feedback_status" class="form-label text-[11px]">Outcome Status</label>
                                                    <select name="status" id="feedback_status" class="form-input text-xs" required>
                                                        <option value="completed">Completed / Finished</option>
                                                        <option value="cancelled">Cancelled</option>
                                                        <option value="no_show">No Show / Candidate Absconded</option>
                                                    </select>
                                                </div>

                                                <div>
                                                    <label for="feedback_notes" class="form-label text-[11px]">Detailed Feedback Notes</label>
                                                    <textarea name="notes" id="feedback_notes" rows="3" class="form-input text-xs" placeholder="Summarize candidate answers, tech skills, soft skills, and recommendation..." required></textarea>
                                                </div>

                                                <div class="flex justify-end gap-2">
                                                    <button type="button" @click="feedbackOpen = false" class="btn btn-secondary py-1 px-3.5 text-[10px]">Cancel</button>
                                                    <button type="submit" class="btn btn-primary py-1 px-4 text-[10px] font-bold">Save Feedback</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs italic">No interviews scheduled yet.</div>
                        @endforelse
                    </div>

                </div>

                <!-- 5. TABS: Communications WhatsApp outbound chat view -->
                <div x-show="activeTab === 'comms'" class="space-y-6 animate-fade-in" style="display: none;">
                    
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3 mb-2">
                        <h4 class="font-extrabold text-xs text-[#111318] flex items-center gap-1.5">
                            <i class="fa-brands fa-whatsapp text-emerald-500 text-base"></i> Automated Notifications Logs
                        </h4>
                    </div>

                    <!-- Notification logs -->
                    <div class="space-y-4 max-h-96 overflow-y-auto pr-1">
                        @forelse($application->communications as $comm)
                            @php
                                $isOutbound = $comm->direction === 'outbound';
                            @endphp
                            <div class="flex flex-col gap-1">
                                <div class="chat-bubble {{ $isOutbound ? 'chat-outbound' : 'chat-inbound' }}">
                                    <div class="flex justify-between items-center text-[8px] font-bold text-gray-400/90 mb-1 border-b border-gray-200/20 pb-0.5">
                                        <span class="uppercase">{{ $comm->channel }} &bull; {{ $comm->direction }}</span>
                                        @if($isOutbound && $comm->sentBy)
                                            <span>BY: {{ $comm->sentBy->full_name }}</span>
                                        @endif
                                    </div>
                                    <p class="text-[11px] leading-relaxed whitespace-pre-line">{{ $comm->message }}</p>
                                </div>
                                <span class="text-[9px] text-gray-400 font-semibold mt-0.5 {{ $isOutbound ? 'text-right' : 'text-left' }}">
                                    {{ $comm->sent_at ? $comm->sent_at->format('M d, Y H:i') : $comm->created_at->format('M d, Y H:i') }}
                                </span>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400 text-xs italic">No communications logged.</div>
                        @endforelse
                    </div>

                </div>

            </div>
        </div>

    </div>

    <!-- HIRE MODAL (Alpine-driven) -->
    <div x-show="hireModal" class="modal-overlay" style="display: none;" @keydown.escape.window="hireModal = false">
        <div class="modal-content animate-scale-in max-w-sm p-6" @click.outside="hireModal = false">
            <h3 class="font-extrabold text-[#111318] text-sm mb-3">Hire Candidate?</h3>
            <p class="text-xs text-gray-500 mb-6 leading-relaxed">
                This will move the candidate to the terminal <strong>Hired</strong> stage. They will receive automated congratulatory notifications via SMS & WhatsApp.
            </p>
            <form action="{{ route('applications.hire', ['application' => $application->id]) }}" method="POST">
                @csrf
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" @click="hireModal = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="btn btn-success py-1.5 px-5 text-xs font-bold rounded-xl">Confirm Hire</button>
                </div>
            </form>
        </div>
    </div>

    <!-- REJECT MODAL (Alpine-driven) -->
    <div x-show="rejectModal" class="modal-overlay" style="display: none;" @keydown.escape.window="rejectModal = false">
        <div class="modal-content animate-scale-in max-w-sm p-6" @click.outside="rejectModal = false">
            <h3 class="font-extrabold text-[#111318] text-sm mb-3">Reject Candidate?</h3>
            <p class="text-xs text-gray-500 mb-4 leading-relaxed">
                This will move the candidate to the terminal <strong>Rejected</strong> stage. They will receive automated notifications explaining the reject.
            </p>
            
            <form action="{{ route('applications.reject', ['application' => $application->id]) }}" method="POST" class="space-y-4">
                @csrf
                <div>
                    <label for="rejection_reason" class="form-label text-xs">Rejection Feedback Reason</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-input text-xs" placeholder="Provide feedback notes..." required></textarea>
                </div>
                
                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4">
                    <button type="button" @click="rejectModal = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="btn btn-danger py-1.5 px-5 text-xs font-bold rounded-xl">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- INTERVIEW SCHEDULER MODAL (Alpine-driven) -->
    <div x-show="scheduleModal" class="modal-overlay" style="display: none;" @keydown.escape.window="scheduleModal = false">
        <div class="modal-content animate-scale-in max-w-md p-6" @click.outside="scheduleModal = false">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-plus text-[#FF6B00]"></i> Schedule Candidate Interview
                </h3>
                <button @click="scheduleModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('interviews.schedule') }}" method="POST" class="space-y-4">
                @csrf
                <input type="hidden" name="application_id" value="{{ $application->id }}">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Scheduled Date & Time -->
                    <div>
                        <label for="scheduled_at" class="form-label text-xs">Date & Time</label>
                        <input type="datetime-local" name="scheduled_at" id="scheduled_at" class="form-input text-xs" required>
                    </div>

                    <!-- Duration -->
                    <div>
                        <label for="duration_minutes" class="form-label text-xs">Duration (Minutes)</label>
                        <select name="duration_minutes" id="duration_minutes" class="form-input text-xs" required>
                            <option value="30">30 Minutes</option>
                            <option value="45">45 Minutes</option>
                            <option value="60">60 Minutes</option>
                            <option value="90">90 Minutes</option>
                        </select>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Type -->
                    <div>
                        <label for="interview_type" class="form-label text-xs">Meeting Type</label>
                        <select name="type" id="interview_type" class="form-input text-xs" required>
                            <option value="video">Google Meet / Video Call</option>
                            <option value="phone">Phone Call</option>
                            <option value="on_site">On-site (Munchify Kitchen)</option>
                        </select>
                    </div>

                    <!-- Interviewer -->
                    <div>
                        <label for="interviewer_id" class="form-label text-xs">Assigned Interviewer</label>
                        <select name="interviewer_id" id="interviewer_id" class="form-input text-xs" required>
                            <option value="">-- Choose Reviewer --</option>
                            @php
                                // Fetch users for interviewer select
                                $interviewersList = \App\Models\User::where('is_active', true)->get();
                            @endphp
                            @foreach($interviewersList as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->role_label }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Meeting link or location details -->
                <div>
                    <label for="location_or_link" class="form-label text-xs">Google Meet Link / Meeting Address</label>
                    <input type="text" name="location_or_link" id="location_or_link" class="form-input text-xs" placeholder="e.g. https://meet.google.com/abc-defg-hij" required>
                </div>

                <!-- Notes -->
                <div>
                    <label for="interview_notes" class="form-label text-xs">Preparatory Instructions Notes (Optional)</label>
                    <textarea name="notes" id="interview_notes" rows="2" class="form-input text-xs" placeholder="Additional prep instructions..."></textarea>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-150 pt-4 mt-6">
                    <button type="button" @click="scheduleModal = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs font-bold rounded-xl">Schedule & Notify</button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script>
    function candidateDetail(token) {
        return {
            csrfToken: token,
            isStarred: {{ $application->is_starred ? 'true' : 'false' }},
            hireModal: false,
            rejectModal: false,
            scheduleModal: false,

            toggleStar() {
                fetch(`/dashboard/applications/{{ $application->id }}/star`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.isStarred = data.is_starred;
                    }
                });
            }
        }
    }
</script>
@endsection
