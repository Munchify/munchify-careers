@extends('layouts.dashboard')

@section('title', 'Audit Trail - Recruiter Dashboard')
@section('header_title', 'System Audit Logs')

@section('content')
<!-- Filter bar -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-6">
    <form action="{{ route('audit.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4 items-end">
        <!-- Search Keywords -->
        <div>
            <label for="search" class="form-label text-xs">Search Actor</label>
            <div class="relative">
                <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-input pl-8 text-xs" placeholder="e.g. Admin name, email...">
                <i class="fa-solid fa-user absolute left-3 top-3.5 text-[10px] text-gray-400"></i>
            </div>
        </div>

        <!-- Filter action -->
        <div>
            <label for="action" class="form-label text-xs">Filter Action</label>
            <select name="action" id="action" class="form-input text-xs">
                <option value="">All Actions</option>
                @foreach($actions as $act)
                    <option value="{{ $act }}" {{ request('action') === $act ? 'selected' : '' }}>{{ str_replace('_', ' ', $act) }}</option>
                @endforeach
            </select>
        </div>

        <div class="flex gap-2">
            <button type="submit" class="btn btn-primary btn-sm flex-grow rounded-xl py-2.5 font-bold text-xs">
                Filter Logs
            </button>
            @if(request()->anyFilled(['search', 'action']))
                <a href="{{ route('audit.index') }}" class="btn btn-secondary btn-sm rounded-xl py-2.5 px-3" title="Clear Filters">
                    <i class="fa-solid fa-rotate-left"></i>
                </a>
            @endif
        </div>
    </form>
</div>

<!-- Logs List Card -->
<div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden animate-fade-in">
    <div class="overflow-x-auto">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Timestamp</th>
                    <th>Actor / Recruiter</th>
                    <th>Action</th>
                    <th>Target Entity</th>
                    <th>JSON Details</th>
                </tr>
            </thead>
            <tbody>
                @forelse($logs as $log)
                    <tr>
                        <td class="text-xs text-gray-400 font-bold whitespace-nowrap">
                            {{ $log->created_at->format('Y-m-d H:i:s') }}
                        </td>
                        <td>
                            @if($log->actor)
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-orange-50 border border-orange-100 text-[#FF6B00] flex items-center justify-center font-bold text-[10px] uppercase">
                                        {{ $log->actor->initials }}
                                    </div>
                                    <div class="flex flex-col min-w-0">
                                        <span class="font-bold text-xs text-gray-800 truncate">{{ $log->actor->full_name }}</span>
                                        <span class="text-[9px] text-gray-400 font-semibold truncate">{{ $log->actor->email }}</span>
                                    </div>
                                </div>
                            @else
                                <span class="text-xs text-gray-400 font-bold italic">System Engine</span>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-orange font-bold text-[9px] uppercase tracking-wide">
                                {{ str_replace('_', ' ', $log->action) }}
                            </span>
                        </td>
                        <td class="text-xs text-gray-500 font-semibold truncate max-w-xs">
                            @if($log->entity_type)
                                {{ class_basename($log->entity_type) }} (ID: {{ $log->entity_id }})
                            @else
                                N/A
                            @endif
                        </td>
                        <td>
                            <div class="max-w-xs overflow-x-auto">
                                <code class="text-[10px] text-purple-700 bg-purple-50/50 p-1.5 rounded font-mono leading-normal select-all whitespace-nowrap block">
                                    {{ json_encode($log->details) }}
                                </code>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-12 text-gray-400 text-xs">No audit logs logged in database.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    @if($logs->hasPages())
        <div class="p-4 border-t border-gray-200">
            {{ $logs->appends(request()->query())->links() }}
        </div>
    @endif
</div>
@endsection
