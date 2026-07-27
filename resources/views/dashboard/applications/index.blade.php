@extends('layouts.dashboard')

@section('title', 'Candidates Directory - Recruiter Dashboard')
@section('header_title', 'All Candidates')

@section('content')
<!-- Filter bar & Export actions -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6" x-data="{ advanced: false }">
    <form action="{{ route('applications.index') }}" method="GET" class="space-y-4">
        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            
            <!-- Search Keywords -->
            <div class="col-span-1 md:col-span-2 relative">
                <input type="text" name="search" value="{{ request('search') }}" class="form-input pl-8 text-xs" placeholder="Search candidates by name, email, application #...">
                <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-[10px] text-gray-400"></i>
            </div>

            <!-- Job Listing Filter -->
            <div>
                <select name="job_id" class="form-input text-xs">
                    <option value="">All Job Openings</option>
                    @foreach($jobs as $job)
                        <option value="{{ $job->id }}" {{ request('job_id') == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Filters trigger -->
            <div class="flex gap-2">
                <button type="submit" class="btn btn-primary btn-sm flex-grow rounded-xl font-bold text-xs py-2.5">
                    Filter Directory
                </button>
                <button type="button" @click="advanced = !advanced" class="btn btn-secondary btn-sm rounded-xl py-2.5 px-3">
                    <i class="fa-solid fa-sliders"></i>
                </button>
                @if(request()->anyFilled(['search', 'job_id', 'status', 'source', 'starred']))
                    <a href="{{ route('applications.index') }}" class="btn btn-secondary btn-sm rounded-xl py-2.5 px-3" title="Clear Filters">
                        <i class="fa-solid fa-rotate-left"></i>
                    </a>
                @endif
            </div>

        </div>

        <!-- Advanced Filter Options -->
        <div x-show="advanced" class="grid grid-cols-1 md:grid-cols-3 gap-4 pt-4 border-t border-gray-150 animate-fade-in" style="display: none;">
            <!-- Status Filter -->
            <div>
                <label for="status" class="form-label text-xs">Review Status</label>
                <select name="status" id="status" class="form-input text-xs">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') === 'active' ? 'selected' : '' }}>Active</option>
                    <option value="hired" {{ request('status') === 'hired' ? 'selected' : '' }}>Hired</option>
                    <option value="rejected" {{ request('status') === 'rejected' ? 'selected' : '' }}>Rejected</option>
                    <option value="withdrawn" {{ request('status') === 'withdrawn' ? 'selected' : '' }}>Withdrawn</option>
                </select>
            </div>

            <!-- Source Filter -->
            <div>
                <label for="source" class="form-label text-xs">Application Source</label>
                <select name="source" id="source" class="form-input text-xs">
                    <option value="">All Sources</option>
                    <option value="direct" {{ request('source') === 'direct' ? 'selected' : '' }}>Direct</option>
                    <option value="referral" {{ request('source') === 'referral' ? 'selected' : '' }}>Referral</option>
                    <option value="social" {{ request('source') === 'social' ? 'selected' : '' }}>Social Media</option>
                    <option value="other" {{ request('source') === 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            <!-- Starred filter toggle -->
            <div class="flex items-center pt-8">
                <label class="flex items-center text-xs font-bold text-gray-700 cursor-pointer">
                    <input type="checkbox" name="starred" value="1" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 mr-2" {{ request('starred') ? 'checked' : '' }}>
                    Show only Starred candidates?
                </label>
            </div>
        </div>
    </form>
</div>

<!-- BULK ACTIONS & EXPORTS ROW -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-4" x-data="bulkManager()">
    <!-- Bulk form -->
    <div class="flex items-center gap-2">
        <select x-model="action" class="form-input py-1.5 px-3 text-xs w-44">
            <option value="">-- Bulk Actions --</option>
            <option value="move">Move Pipeline Stage</option>
            <option value="reject">Reject Candidates</option>
            <option value="message">Send Bulk Message</option>
        </select>
        
        <button type="button" @click="triggerBulk()" class="btn btn-secondary btn-sm py-2 px-4 font-bold text-xs" :disabled="selectedCount() === 0">
            Execute <span x-show="selectedCount() > 0" class="ml-1 bg-[#FF6B00] text-white rounded-full px-1.5 py-0.5 text-[10px]" x-text="selectedCount()"></span>
        </button>
    </div>

    <!-- Export CSV -->
    <a href="{{ route('applications.export', request()->query()) }}" class="btn btn-secondary btn-sm font-bold text-xs">
        <i class="fa-solid fa-file-csv mr-1"></i> Export list as CSV
    </a>

    <!-- Bulk Action Modals -->
    <!-- 1. Bulk Stage Move Modal -->
    <div x-show="modals.move" class="modal-overlay" style="display: none;" @keydown.escape.window="modals.move = false">
        <div class="modal-content p-6 animate-scale-in max-w-sm">
            <h3 class="font-extrabold text-sm mb-4">Bulk Move Pipeline Stage</h3>
            <form :action="bulkSubmitUrl" method="POST">
                @csrf
                <input type="hidden" name="bulk_action" value="move">
                <template x-for="id in selectedIds">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="mb-4">
                    <label class="form-label text-xs">Destination Pipeline Stage</label>
                    <select name="stage_id" class="form-input text-xs" required>
                        <option value="">-- Choose Stage --</option>
                        <!-- In the general directory, we list all job pipeline stages from the jobs -->
                        @foreach($jobs as $j)
                            <optgroup label="{{ $j->title }}">
                                @foreach($j->pipelineStages as $stg)
                                    <option value="{{ $stg->id }}">{{ $stg->name }} ({{ $j->title }})</option>
                                @endforeach
                            </optgroup>
                        @endforeach
                    </select>
                    <span class="form-help block text-[10px] mt-1">Note: Candidates will only move if the chosen stage belongs to their respective Job opening.</span>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-6">
                    <button type="button" @click="modals.move = false" class="btn btn-secondary py-1.5 px-4 text-xs">Cancel</button>
                    <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs">Confirm Move</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 2. Bulk Reject Modal -->
    <div x-show="modals.reject" class="modal-overlay" style="display: none;" @keydown.escape.window="modals.reject = false">
        <div class="modal-content p-6 animate-scale-in max-w-sm">
            <h3 class="font-extrabold text-sm mb-4">Bulk Reject Candidates</h3>
            <form :action="bulkSubmitUrl" method="POST">
                @csrf
                <input type="hidden" name="bulk_action" value="reject">
                <template x-for="id in selectedIds">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="mb-4">
                    <label for="rejection_reason" class="form-label text-xs">Rejection Reason (SMS & WhatsApp notification)</label>
                    <textarea name="rejection_reason" id="rejection_reason" rows="3" class="form-input text-xs" placeholder="Describe the reason..." required></textarea>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-6">
                    <button type="button" @click="modals.reject = false" class="btn btn-secondary py-1.5 px-4 text-xs">Cancel</button>
                    <button type="submit" class="btn btn-danger py-1.5 px-5 text-xs">Confirm Reject</button>
                </div>
            </form>
        </div>
    </div>

    <!-- 3. Bulk Send Message Modal -->
    <div x-show="modals.message" class="modal-overlay" style="display: none;" @keydown.escape.window="modals.message = false">
        <div class="modal-content p-6 animate-scale-in max-w-md">
            <h3 class="font-extrabold text-sm mb-4">Send Bulk Message</h3>
            <form :action="bulkSubmitUrl" method="POST">
                @csrf
                <input type="hidden" name="bulk_action" value="message">
                <template x-for="id in selectedIds">
                    <input type="hidden" name="ids[]" :value="id">
                </template>

                <div class="mb-4">
                    <label for="message_channel" class="form-label text-xs">Delivery Channel</label>
                    <select name="message_channel" id="message_channel" class="form-input text-xs" required>
                        <option value="whatsapp">WhatsApp Business API</option>
                        <option value="sms">SMS Gateway</option>
                    </select>
                </div>

                <div class="mb-4">
                    <label for="message_text" class="form-label text-xs">Message Text</label>
                    <textarea name="message_text" id="message_text" rows="4" class="form-input text-xs" placeholder="Type message body..." required></textarea>
                    <span class="form-help block text-[10px] mt-1">Note: This will send direct message body to all selected candidate phone numbers.</span>
                </div>

                <div class="flex justify-end gap-2 border-t border-gray-100 pt-4 mt-6">
                    <button type="button" @click="modals.message = false" class="btn btn-secondary py-1.5 px-4 text-xs">Cancel</button>
                    <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs">Send Message</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Candidates list grid -->
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animate-fade-in" x-data="checkboxGrid()">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th class="w-8">
                        <input type="checkbox" @change="toggleAll($event)" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 cursor-pointer">
                    </th>
                    <th>Candidate</th>
                    <th>Job Title</th>
                    <th>Stage</th>
                    <th>Score</th>
                    <th>Source</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($applications as $app)
                    <tr>
                        <td>
                            <input type="checkbox" value="{{ $app->id }}" @change="toggleSingle($event, {{ $app->id }})" :checked="checkedIds.includes({{ $app->id }})" class="rounded border-gray-300 text-[#FF6B00] focus:ring-0 cursor-pointer">
                        </td>
                        <td>
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-orange-50 border border-orange-100 text-[#FF6B00] flex items-center justify-center font-bold text-xs uppercase shrink-0">
                                    {{ $app->initials }}
                                </div>
                                <div class="flex flex-col min-w-0">
                                    <div class="flex items-center gap-1.5">
                                        <a href="{{ route('applications.show', ['application' => $app->id]) }}" class="font-extrabold text-xs text-gray-800 hover:text-[#FF6B00] transition truncate">{{ $app->full_name }}</a>
                                        @if($app->is_starred)
                                            <i class="fa-solid fa-star text-[10px] text-[#FFD233]"></i>
                                        @endif
                                        @if($app->is_knockout)
                                            <span class="text-[8px] bg-red-100 text-red-600 font-extrabold uppercase px-1 rounded">KO</span>
                                        @endif
                                    </div>
                                    <span class="text-[9px] text-gray-400 font-mono mt-0.5">{{ $app->application_number }} &bull; {{ $app->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="text-xs text-gray-700 font-semibold">{{ $app->jobListing->title }}</td>
                        <td>
                            <span class="badge badge-gray border border-gray-200">{{ $app->currentStage->name ?? 'N/A' }}</span>
                        </td>
                        <td>
                            @if($app->overall_score)
                                <span class="flex items-center gap-0.5 text-xs font-bold text-[#FF6B00]">
                                    <i class="fa-solid fa-star text-[10px]"></i> {{ $app->overall_score }}
                                </span>
                            @else
                                <span class="text-xs text-gray-300 font-semibold">--</span>
                            @endif
                        </td>
                        <td class="text-xs text-gray-500 font-semibold">{{ $app->source_label }}</td>
                        <td>
                            <span class="badge {{ $app->status_badge_class }}">{{ ucfirst($app->status) }}</span>
                        </td>
                        <td class="text-right">
                            <a href="{{ route('applications.show', ['application' => $app->id]) }}" class="btn btn-secondary py-1 px-3.5 text-xs rounded-xl hover:border-[#FF6B00] hover:text-[#FF6B00]">
                                Profile
                            </a>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center py-12 text-gray-400 text-xs">No candidate applications listed.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($applications->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $applications->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
    // Global selected candidate IDs tracker
    window.selectedAppIds = [];

    function checkboxGrid() {
        return {
            checkedIds: [],
            toggleSingle(e, id) {
                if (e.target.checked) {
                    this.checkedIds.push(id);
                } else {
                    this.checkedIds = this.checkedIds.filter(item => item !== id);
                }
                window.selectedAppIds = this.checkedIds;
                // Dispatch event to bulk actions component
                window.dispatchEvent(new CustomEvent('selected-apps-changed', { detail: this.checkedIds }));
            },
            toggleAll(e) {
                if (e.target.checked) {
                    this.checkedIds = @json($applications->pluck('id'));
                } else {
                    this.checkedIds = [];
                }
                window.selectedAppIds = this.checkedIds;
                window.dispatchEvent(new CustomEvent('selected-apps-changed', { detail: this.checkedIds }));
            }
        }
    }

    function bulkManager() {
        return {
            action: '',
            selectedIds: [],
            bulkSubmitUrl: '{{ route('applications.bulk') }}',
            modals: {
                move: false,
                reject: false,
                message: false
            },
            init() {
                window.addEventListener('selected-apps-changed', (e) => {
                    this.selectedIds = e.detail;
                });
            },
            selectedCount() {
                return this.selectedIds.length;
            },
            triggerBulk() {
                if (!this.action) return;
                
                // Open appropriate modal based on action select value
                if (this.action === 'move') {
                    this.modals.move = true;
                } else if (this.action === 'reject') {
                    this.modals.reject = true;
                } else if (this.action === 'message') {
                    this.modals.message = true;
                }
            }
        }
    }
</script>
@endsection
