@extends('layouts.dashboard')

@section('title', 'Jobs Management - Recruiter Dashboard')
@section('header_title', 'Job Openings')

@section('content')
<!-- Header Filter/Create Bar -->
<div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-6">
    <!-- Search form -->
    <form action="{{ route('jobs.manage') }}" method="GET" class="relative w-full sm:max-w-xs">
        <input type="text" name="search" value="{{ request('search') }}" class="form-input pl-8 text-xs" placeholder="Search jobs by title...">
        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3.5 text-[10px] text-gray-400"></i>
    </form>

    <!-- Create CTA (Admin/HR only) -->
    @if(Auth::user()->canManageJobs())
        <a href="{{ route('jobs.create') }}" class="btn btn-primary btn-sm px-5 py-2.5 rounded-full font-bold shadow-md shadow-[#FF6B00]/15">
            <i class="fa-solid fa-plus text-[10px]"></i> Create Job Opening
        </a>
    @endif
</div>

<!-- Jobs List Card -->
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animate-fade-in">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Job Title</th>
                    <th>Department</th>
                    <th>Setting / Type</th>
                    <th>slots</th>
                    <th>Candidates</th>
                    <th>Status</th>
                    <th class="text-right">Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($jobs as $job)
                    <tr>
                        <td>
                            <div class="flex flex-col">
                                <a href="{{ route('jobs.show', ['job' => $job->id]) }}" class="font-extrabold text-xs text-[#111318] hover:text-[#FF6B00] transition">{{ $job->title }}</a>
                                <span class="text-[9px] text-gray-400 mt-0.5">Created {{ $job->created_at->format('M d, Y') }}</span>
                            </div>
                        </td>
                        <td class="text-xs text-gray-700 font-semibold">{{ $job->department->name }}</td>
                        <td>
                            <div class="flex flex-col gap-0.5">
                                <span class="text-xs text-gray-700 font-semibold">{{ $job->location_label }}</span>
                                <span class="text-[10px] text-gray-400 font-semibold">{{ $job->type_label }}</span>
                            </div>
                        </td>
                        <td class="text-xs text-gray-700 font-bold">{{ $job->slots }}</td>
                        <td>
                            <a href="{{ route('applications.index', ['job_id' => $job->id]) }}" class="inline-flex items-center gap-1.5 hover:underline text-[#FF6B00] font-bold text-xs">
                                <i class="fa-solid fa-user-group text-[10px]"></i> {{ $job->applications_count }}
                            </a>
                        </td>
                        <td>
                            <span class="badge {{ $job->status_badge_class }}">{{ ucfirst($job->status) }}</span>
                        </td>
                        <td class="text-right">
                            <div class="inline-flex items-center gap-1">
                                <!-- Kanban board view -->
                                <a href="{{ route('jobs.show', ['job' => $job->id]) }}" class="btn btn-secondary btn-icon py-1.5 px-2.5 text-xs hover:border-[#FF6B00] hover:text-[#FF6B00]" title="Kanban Pipeline Board">
                                    <i class="fa-solid fa-arrows-split-up-and-left text-[11px]"></i> Board
                                </a>

                                @if(Auth::user()->canManageJobs())
                                    <!-- Edit -->
                                    <a href="{{ route('jobs.edit', ['id' => $job->id]) }}" class="btn btn-secondary btn-icon py-1.5 px-2.5 text-xs hover:border-[#FF6B00] hover:text-[#FF6B00]" title="Edit Job details">
                                        <i class="fa-regular fa-edit text-[11px]"></i>
                                    </a>

                                    <!-- Status Toggle Dropdown/Dropdown Actions -->
                                    <div x-data="{ open: false }" class="relative inline-block text-left">
                                        <button @click="open = !open" class="btn btn-secondary btn-icon py-1.5 px-2 text-xs" title="More Actions">
                                            <i class="fa-solid fa-ellipsis-vertical text-[11px]"></i>
                                        </button>
                                        <div x-show="open" @click.away="open = false" class="dropdown-menu animate-scale-in max-w-xs" style="display: none;">
                                            
                                            <!-- Publish/Close Status Options -->
                                            @if($job->status !== 'published')
                                                <form action="{{ route('jobs.status', ['id' => $job->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="published">
                                                    <button type="submit" class="dropdown-item text-left w-full text-xs font-semibold text-emerald-600">
                                                        <i class="fa-solid fa-circle-check text-[10px] mr-1"></i> Publish Opening
                                                    </button>
                                                </form>
                                            @endif

                                            @if($job->status === 'published')
                                                <form action="{{ route('jobs.status', ['id' => $job->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="closed">
                                                    <button type="submit" class="dropdown-item text-left w-full text-xs font-semibold text-orange-600">
                                                        <i class="fa-solid fa-ban text-[10px] mr-1"></i> Close Opening
                                                    </button>
                                                </form>
                                            @endif

                                            @if($job->status !== 'draft')
                                                <form action="{{ route('jobs.status', ['id' => $job->id]) }}" method="POST">
                                                    @csrf
                                                    <input type="hidden" name="status" value="draft">
                                                    <button type="submit" class="dropdown-item text-left w-full text-xs font-semibold text-gray-650">
                                                        <i class="fa-regular fa-file-lines text-[10px] mr-1"></i> Revert to Draft
                                                    </button>
                                                </form>
                                            @endif

                                            <!-- Duplicate -->
                                            <form action="{{ route('jobs.duplicate', ['id' => $job->id]) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-left w-full text-xs font-semibold border-t border-gray-100 mt-1">
                                                    <i class="fa-regular fa-copy text-[10px] mr-1"></i> Duplicate Listing
                                                </button>
                                            </form>

                                            <!-- Delete -->
                                            <form action="{{ route('jobs.delete', ['id' => $job->id]) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete {{ addslashes($job->title) }}? This action cannot be undone.');">
                                                @csrf
                                                <button type="submit" class="dropdown-item text-left w-full text-xs font-semibold text-red-600 hover:bg-red-50">
                                                    <i class="fa-solid fa-trash-can text-[10px] mr-1"></i> Delete Opening
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="7" class="text-center py-12 text-gray-400 text-xs">No job listings matching your query.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($jobs->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $jobs->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
