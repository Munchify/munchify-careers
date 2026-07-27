@extends('layouts.dashboard')

@section('title', $job->title . ' - Pipeline Board | Munchify Careers')
@section('header_title', $job->title)

@section('content')
<!-- Job Header details / toggles -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col md:flex-row justify-between items-start md:items-center gap-6 mb-8">
    <div class="flex-grow">
        <div class="flex items-center gap-2 flex-wrap mb-2">
            <span class="badge badge-orange bg-[#FF6B00] text-white border border-[#FF6B00]">{{ $job->department->name }}</span>
            <span class="badge badge-gray">{{ $job->type_label }}</span>
            <span class="badge badge-gray">{{ $job->location_label }}</span>
            <span class="badge {{ $job->status_badge_class }}">{{ ucfirst($job->status) }}</span>
        </div>
        <p class="text-xs text-gray-500 mt-1">
            Hiring Manager: <span class="font-bold text-gray-800">{{ $job->hiringManager->full_name }}</span> &bull; 
            Slots: <span class="font-bold text-gray-800">{{ $job->slots }}</span> &bull; 
            Total Candidates: <span class="font-bold text-gray-800">{{ $job->applications_count }}</span>
        </p>
    </div>

    <!-- Actions -->
    <div class="flex items-center gap-3 shrink-0">
        <a href="{{ route('applications.index', ['job_id' => $job->id]) }}" class="btn btn-secondary btn-sm py-2 px-4 rounded-xl font-bold">
            <i class="fa-solid fa-list mr-1"></i> List View
        </a>
        
        @if(Auth::user()->canManageJobs())
            <a href="{{ route('jobs.edit', ['id' => $job->id]) }}" class="btn btn-secondary btn-sm py-2 px-4 rounded-xl">
                <i class="fa-solid fa-edit mr-1"></i> Edit Job
            </a>
        @endif
    </div>
</div>

<!-- KANBAN BOARD CONTAINER -->
<div class="overflow-x-auto pb-6 -mx-6 px-6" x-data="kanbanBoard('{{ csrf_token() }}')">
    <div class="flex gap-6 min-h-[500px] items-start">
        
        @foreach($stages as $stage)
            @php
                $stageApps = $applications[$stage->id] ?? collect();
            @endphp
            
            <!-- Kanban Stage Column -->
            <div class="kanban-column flex flex-col max-h-[80vh] shadow-sm"
                 data-stage-id="{{ $stage->id }}"
                 @dragover.prevent="onDragOver($event)"
                 @dragleave="onDragLeave($event)"
                 @drop="onDrop($event, {{ $stage->id }})">
                
                <!-- Column Header -->
                <div class="p-4 flex items-center justify-between border-b border-gray-200/50 bg-white rounded-t-xl">
                    <div class="flex items-center gap-2 min-w-0">
                        <span class="w-2.5 h-2.5 rounded-full shrink-0" style="background-color: {{ $stage->color }};"></span>
                        <h4 class="font-extrabold text-xs text-[#111318] truncate">{{ $stage->name }}</h4>
                    </div>
                    <span class="w-5 h-5 rounded-full bg-gray-200 flex items-center justify-center font-bold text-[10px] text-gray-600">
                        {{ $stageApps->count() }}
                    </span>
                </div>

                <!-- Cards area -->
                <div class="flex-grow overflow-y-auto p-2 space-y-3 min-h-[350px]">
                    @foreach($stageApps as $app)
                        <!-- Draggable Application Card -->
                        <div class="kanban-card transition-all duration-150 relative"
                             id="app_card_{{ $app->id }}"
                             data-app-id="{{ $app->id }}"
                             draggable="{{ Auth::user()->canMoveStages() ? 'true' : 'false' }}"
                             @dragstart="onDragStart($event, {{ $app->id }})"
                             @dragend="onDragEnd($event, {{ $app->id }})">
                            
                            <!-- Star & Knockout Indicator row -->
                            <div class="flex justify-between items-start mb-2">
                                <button type="button" @click.stop="toggleStar({{ $app->id }})" class="text-gray-300 hover:text-[#FFD233] transition">
                                    <i class="fa-solid fa-star text-xs" :class="starred[{{ $app->id }}] ? 'text-[#FFD233] star-filled' : 'text-gray-300 star-empty'"></i>
                                </button>
                                
                                @if($app->is_knockout)
                                    <span class="badge badge-red text-[8px] font-extrabold py-0.5 px-1.5 uppercase" title="System Knockout: Failed qualifying criteria">Knockout</span>
                                @endif
                            </div>

                            <!-- Candidate name -->
                            <h5 class="font-extrabold text-xs text-gray-800 leading-snug truncate pr-4">
                                <a href="{{ route('applications.show', ['application' => $app->id]) }}" class="hover:text-[#FF6B00] transition">
                                    {{ $app->full_name }}
                                </a>
                            </h5>
                            
                            <span class="text-[9px] text-gray-400 font-mono mt-0.5 block">{{ $app->application_number }}</span>

                            <!-- Footer info: score & date -->
                            <div class="flex items-center justify-between border-t border-gray-100 mt-3.5 pt-2 text-[9px] font-semibold text-gray-400">
                                <span class="flex items-center gap-1">
                                    <i class="fa-regular fa-calendar"></i> {{ $app->created_at->format('M d') }}
                                </span>
                                
                                @if($app->overall_score)
                                    <span class="flex items-center gap-0.5 text-[#FF6B00]">
                                        <i class="fa-solid fa-star text-[8px]"></i> {{ $app->overall_score }}
                                    </span>
                                @else
                                    <span class="text-gray-300">Unscored</span>
                                @endif
                            </div>
                        </div>
                    @endforeach

                    @if($stageApps->isEmpty())
                        <div class="py-10 text-center text-[10px] text-gray-400 italic">Drop here</div>
                    @endif
                </div>

            </div>
        @endforeach

    </div>
</div>
@endsection

@section('scripts')
<script>
    function kanbanBoard(token) {
        return {
            csrfToken: token,
            starred: {
                @foreach($applications->flatten() as $app)
                    {{ $app->id }}: {{ $app->is_starred ? 'true' : 'false' }},
                @endforeach
            },
            
            onDragStart(e, appId) {
                e.dataTransfer.setData('text/plain', appId);
                const element = document.getElementById('app_card_' + appId);
                if (element) {
                    // Slight delay to allow standard drag image snapshot
                    setTimeout(() => {
                        element.classList.add('dragging');
                    }, 0);
                }
            },
            
            onDragEnd(e, appId) {
                const element = document.getElementById('app_card_' + appId);
                if (element) {
                    element.classList.remove('dragging');
                }
            },

            onDragOver(e) {
                // Highlight drop column
                const column = e.target.closest('.kanban-column');
                if (column) {
                    column.classList.add('bg-gray-300/60');
                }
            },

            onDragLeave(e) {
                const column = e.target.closest('.kanban-column');
                if (column) {
                    column.classList.remove('bg-gray-300/60');
                }
            },

            onDrop(e, stageId) {
                const column = e.target.closest('.kanban-column');
                if (column) {
                    column.classList.remove('bg-gray-300/60');
                }

                const appId = e.dataTransfer.getData('text/plain');
                if (!appId) return;

                // POST stage update to server
                fetch(`/dashboard/applications/${appId}/move-stage`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        stage_id: stageId,
                        note: 'Moved stage via Kanban drag-and-drop.'
                    })
                })
                .then(response => {
                    if (response.ok) {
                        window.location.reload();
                    } else {
                        alert('Could not move candidate: permission denied or invalid stage.');
                    }
                })
                .catch(error => {
                    console.error('Kanban drag error:', error);
                });
            },

            toggleStar(appId) {
                fetch(`/dashboard/applications/${appId}/star`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': this.csrfToken,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        this.starred[appId] = data.is_starred;
                    }
                });
            }
        }
    }
</script>
@endsection
