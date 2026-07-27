@extends('layouts.dashboard')

@section('title', 'Interviews Schedule - Recruiter Dashboard')
@section('header_title', 'Interviews Schedule')

@section('content')
<!-- Header Filters & Schedule Trigger -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6" x-data="{ scheduleModal: false }">
    
    <!-- Filter status -->
    <form action="{{ route('interviews.index') }}" method="GET" class="flex gap-2">
        <select name="status" class="form-input py-1.5 px-3 text-xs w-44">
            <option value="">All Statuses</option>
            <option value="scheduled" {{ request('status') === 'scheduled' ? 'selected' : '' }}>Scheduled</option>
            <option value="completed" {{ request('status') === 'completed' ? 'selected' : '' }}>Completed</option>
            <option value="cancelled" {{ request('status') === 'cancelled' ? 'selected' : '' }}>Cancelled</option>
            <option value="no_show" {{ request('status') === 'no_show' ? 'selected' : '' }}>No Show</option>
        </select>
        <button type="submit" class="btn btn-secondary btn-sm py-2 px-4 font-bold text-xs rounded-xl">Filter</button>
    </form>

    <!-- Trigger Modal -->
    <button @click="scheduleModal = true" class="btn btn-primary btn-sm py-2.5 px-5 font-bold rounded-full text-xs shadow-md shadow-[#FF6B00]/15">
        <i class="fa-solid fa-plus text-[10px]"></i> Schedule Interview
    </button>

    <!-- Schedule Interview Modal -->
    <div x-show="scheduleModal" class="modal-overlay" style="display: none;" @keydown.escape.window="scheduleModal = false">
        <div class="modal-content animate-scale-in max-w-md p-6" @click.outside="scheduleModal = false">
            <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                    <i class="fa-solid fa-calendar-plus text-[#FF6B00]"></i> Schedule Interview
                </h3>
                <button @click="scheduleModal = false" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>

            <form action="{{ route('interviews.schedule') }}" method="POST" class="space-y-4">
                @csrf
                
                <!-- Candidate Application select -->
                <div>
                    <label for="application_id" class="form-label text-xs">Select Candidate</label>
                    <select name="application_id" id="application_id" class="form-input text-xs" required>
                        <option value="">-- Choose Candidate --</option>
                        @php
                            // Fetch active candidate list
                            $activeApps = \App\Models\Application::with('jobListing')->where('status', 'active')->get();
                        @endphp
                        @foreach($activeApps as $app)
                            <option value="{{ $app->id }}">{{ $app->full_name }} ({{ $app->jobListing->title }})</option>
                        @endforeach
                    </select>
                </div>

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
                        <label for="type" class="form-label text-xs">Meeting Type</label>
                        <select name="type" id="type" class="form-input text-xs" required>
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
                            @foreach($interviewers as $user)
                                <option value="{{ $user->id }}">{{ $user->full_name }} ({{ $user->role_label }})</option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Link/address -->
                <div>
                    <label for="location_or_link" class="form-label text-xs">Google Meet Link / Meeting Address</label>
                    <input type="text" name="location_or_link" id="location_or_link" class="form-input text-xs" placeholder="e.g. https://meet.google.com/abc-defg-hij" required>
                </div>

                <!-- Notes -->
                <div>
                    <label for="notes" class="form-label text-xs">Preparatory Instructions Notes (Optional)</label>
                    <textarea name="notes" id="notes" rows="2" class="form-input text-xs" placeholder="Instructions..."></textarea>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-150 pt-4 mt-6">
                    <button type="button" @click="scheduleModal = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                    <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs font-bold rounded-xl">Schedule & Notify</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Layout: Tabs (List view vs Kanban Board vs Calendar Grid view) -->
<div class="space-y-6" x-data="interviewTabHandler('{{ csrf_token() }}')">
    <!-- Tab buttons -->
    <div class="flex border-b border-gray-200">
        <button @click="tab = 'kanban'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition duration-150" :class="tab === 'kanban' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-square-poll-vertical mr-1"></i> Kanban Board
        </button>
        <button @click="tab = 'list'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition duration-150" :class="tab === 'list' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-list mr-1"></i> List Schedules
        </button>
        <button @click="tab = 'calendar'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition duration-150" :class="tab === 'calendar' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-calendar mr-1"></i> Calendar Grid
        </button>
    </div>

    <!-- 0. Kanban Board View -->
    <div x-show="tab === 'kanban'" class="overflow-x-auto pb-6 animate-fade-in">
        <div class="flex gap-6 min-h-[450px] items-start">
            @php
                $kanbanColumns = [
                    'scheduled' => ['title' => 'Scheduled', 'color' => '#3B82F6', 'badge' => 'badge-blue'],
                    'completed' => ['title' => 'Completed', 'color' => '#10B981', 'badge' => 'badge-green'],
                    'cancelled' => ['title' => 'Cancelled', 'color' => '#6B7280', 'badge' => 'badge-gray'],
                    'no_show'   => ['title' => 'No Show', 'color' => '#EF4444', 'badge' => 'badge-red'],
                ];
                $allInterviews = $interviews->items();
            @endphp

            @foreach($kanbanColumns as $statusCode => $col)
                @php
                    $colInterviews = collect($allInterviews)->filter(fn($i) => $i->status === $statusCode);
                @endphp
                <div class="kanban-column flex flex-col flex-1 min-w-[260px] max-h-[80vh] shadow-sm rounded-xl border border-gray-200 bg-gray-50/50"
                     data-status="{{ $statusCode }}"
                     @dragover.prevent="onDragOver($event)"
                     @dragleave="onDragLeave($event)"
                     @drop="onDrop($event, '{{ $statusCode }}')">
                    
                    <div class="p-4 flex items-center justify-between border-b border-gray-200 bg-white rounded-t-xl">
                        <div class="flex items-center gap-2">
                            <span class="w-2.5 h-2.5 rounded-full" style="background-color: {{ $col['color'] }};"></span>
                            <h4 class="font-extrabold text-xs text-[#111318]">{{ $col['title'] }}</h4>
                        </div>
                        <span class="w-5 h-5 rounded-full bg-gray-100 flex items-center justify-center font-bold text-[10px] text-gray-600">
                            {{ $colInterviews->count() }}
                        </span>
                    </div>

                    <div class="flex-grow overflow-y-auto p-3 space-y-3 min-h-[350px]">
                        @foreach($colInterviews as $interview)
                            <div class="kanban-card transition-all duration-150 relative bg-white p-4 rounded-xl border border-gray-200 shadow-sm cursor-grab active:cursor-grabbing hover:border-[#FF6B00]/40"
                                 id="interview_card_{{ $interview->id }}"
                                 data-interview-id="{{ $interview->id }}"
                                 draggable="true"
                                 @dragstart="onDragStart($event, {{ $interview->id }})"
                                 @dragend="onDragEnd($event, {{ $interview->id }})">
                                
                                <div class="flex justify-between items-start mb-2">
                                    <span class="text-[9px] font-mono text-gray-400">#INT-{{ $interview->id }}</span>
                                    <span class="badge {{ $col['badge'] }} text-[9px] font-bold">{{ ucfirst($interview->status) }}</span>
                                </div>

                                <h5 class="font-extrabold text-xs text-gray-800 leading-snug truncate">
                                    <a href="{{ route('applications.show', ['application' => $interview->application_id]) }}" class="hover:text-[#FF6B00]">
                                        {{ $interview->application->full_name }}
                                    </a>
                                </h5>
                                <p class="text-[10px] text-gray-500 font-semibold mt-0.5">{{ $interview->application->jobListing->title }}</p>

                                <div class="mt-3 pt-2.5 border-t border-gray-100 flex flex-col gap-1.5 text-[10px] text-gray-600">
                                    <div class="flex items-center gap-1.5 font-bold text-gray-700">
                                        <i class="fa-regular fa-clock text-[#FF6B00]"></i> {{ $interview->scheduled_at->format('M d @ H:i') }} ({{ $interview->duration_minutes }}m)
                                    </div>
                                    <div class="flex items-center gap-1.5 truncate text-gray-500">
                                        <i class="fa-solid fa-user-tie"></i> {{ $interview->interviewer->full_name }}
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @if($colInterviews->isEmpty())
                            <div class="py-12 text-center text-[10px] text-gray-400 italic">Drag interviews here</div>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>
    </div>

    <!-- 1. List Schedules -->
    <div x-show="tab === 'list'" class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animate-fade-in">
        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Job opening</th>
                        <th>Interviewer</th>
                        <th>Scheduled Date / Time</th>
                        <th>Setting / Type</th>
                        <th>Status</th>
                        <th class="text-right">Action</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($interviews as $interview)
                        <tr>
                            <td>
                                <a href="{{ route('applications.show', ['application' => $interview->application_id]) }}" class="font-bold text-xs text-gray-800 hover:text-[#FF6B00] transition">
                                    {{ $interview->application->full_name }}
                                </a>
                            </td>
                            <td class="text-xs text-gray-750 font-semibold">{{ $interview->application->jobListing->title }}</td>
                            <td class="text-xs text-gray-700 font-semibold">{{ $interview->interviewer->full_name }}</td>
                            <td class="text-xs text-gray-800 font-bold">{{ $interview->scheduled_at->format('M d, Y @ h:i A') }}</td>
                            <td>
                                <div class="flex flex-col gap-0.5">
                                    <span class="text-xs text-gray-700 font-semibold">
                                        {{ $interview->type === 'video' ? 'Google Meet' : ($interview->type === 'phone' ? 'Phone' : 'On-site') }}
                                    </span>
                                    <span class="text-[9px] text-gray-400 font-mono truncate max-w-xs">{{ $interview->location_or_link }}</span>
                                </div>
                            </td>
                            <td>
                                <span class="badge {{ $interview->status === 'completed' ? 'badge-green' : 'badge-orange' }}">
                                    {{ ucfirst($interview->status) }}
                                </span>
                            </td>
                            <td class="text-right">
                                <a href="{{ route('applications.show', ['application' => $interview->application_id]) }}" class="btn btn-secondary py-1 px-3 text-xs rounded-xl">
                                    Profile
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-gray-400 text-xs">No scheduled interviews.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($interviews->hasPages())
            <div class="p-4 border-t border-gray-200">
                {{ $interviews->appends(request()->query())->links() }}
            </div>
        @endif
    </div>

    <!-- 2. Calendar Grid View -->
    <div x-show="tab === 'calendar'" class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 animate-fade-in" style="display: none;">
        
        <div class="flex justify-between items-center mb-6">
            <h4 class="font-extrabold text-sm text-[#111318]">{{ Carbon\Carbon::now()->format('F Y') }}</h4>
        </div>

        <!-- Days of week headers -->
        <div class="grid grid-cols-7 gap-2 text-center text-xs font-bold text-gray-400 border-b border-gray-100 pb-2">
            <span>Sun</span>
            <span>Mon</span>
            <span>Tue</span>
            <span>Wed</span>
            <span>Thu</span>
            <span>Fri</span>
            <span>Sat</span>
        </div>

        <!-- CSS Grid Calendar dates -->
        <div class="grid grid-cols-7 gap-2 mt-2">
            @php
                $startOfMonth = Carbon\Carbon::now()->startOfMonth();
                $endOfMonth = Carbon\Carbon::now()->endOfMonth();
                $startDayOfWeek = $startOfMonth->dayOfWeek; // 0 (Sun) to 6 (Sat)
                
                // Add empty columns for blank days before start of month
                $days = [];
                for ($d = 0; $d < $startDayOfWeek; $d++) {
                    $days[] = null;
                }
                
                // Add actual days of the month
                for ($d = 1; $d <= $endOfMonth->day; $d++) {
                    $days[] = $startOfMonth->copy()->day($d);
                }
            @endphp

            @foreach($days as $day)
                @if($day === null)
                    <div class="bg-gray-50/50 rounded-xl min-h-[80px] border border-gray-100/50"></div>
                @else
                    @php
                        // Filter interviews on this day
                        $dayInterviews = $calendarInterviews->filter(function($i) use ($day) {
                            return $i->scheduled_at->isSameDay($day);
                        });
                        $isToday = $day->isToday();
                    @endphp
                    
                    <div class="bg-white rounded-xl min-h-[80px] p-2 border relative flex flex-col gap-1 {{ $isToday ? 'border-[#FF6B00] bg-orange-50/5' : 'border-gray-200' }}">
                        <span class="text-[10px] font-extrabold {{ $isToday ? 'text-[#FF6B00]' : 'text-[#111318]' }}">{{ $day->day }}</span>
                        
                        <div class="space-y-1 overflow-y-auto flex-grow max-h-16">
                            @foreach($dayInterviews as $di)
                                <a href="{{ route('applications.show', ['application' => $di->application_id]) }}" class="block p-1 bg-purple-50 text-purple-700 hover:bg-purple-100 rounded text-[8px] font-bold truncate leading-tight border border-purple-100" title="{{ $di->application->full_name }} - {{ $di->application->jobListing->title }}">
                                    {{ $di->scheduled_at->format('H:i') }} &bull; {{ $di->application->full_name }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    function interviewTabHandler(token) {
        return {
            tab: 'kanban',
            csrfToken: token,

            onDragStart(e, interviewId) {
                e.dataTransfer.setData('text/plain', interviewId);
                const el = document.getElementById('interview_card_' + interviewId);
                if (el) {
                    setTimeout(() => el.classList.add('opacity-50'), 0);
                }
            },

            onDragEnd(e, interviewId) {
                const el = document.getElementById('interview_card_' + interviewId);
                if (el) {
                    el.classList.remove('opacity-50');
                }
            },

            onDragOver(e) {
                const col = e.target.closest('.kanban-column');
                if (col) col.classList.add('bg-orange-50/30');
            },

            onDragLeave(e) {
                const col = e.target.closest('.kanban-column');
                if (col) col.classList.remove('bg-orange-50/30');
            },

            onDrop(e, status) {
                const col = e.target.closest('.kanban-column');
                if (col) col.classList.remove('bg-orange-50/30');

                const interviewId = e.dataTransfer.getData('text/plain');
                if (!interviewId) return;

                fetch(`/dashboard/interviews/${interviewId}/status`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({ status: status })
                })
                .then(res => res.json())
                .then(data => {
                    if (data.success) {
                        window.location.reload();
                    } else {
                        alert('Unable to update interview status.');
                    }
                })
                .catch(err => console.error('Kanban drop error:', err));
            }
        }
    }
</script>
@endsection
