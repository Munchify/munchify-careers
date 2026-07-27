@extends('layouts.dashboard')

@section('title', 'Overview - Recruiter Dashboard')
@section('header_title', 'Dashboard Overview')

@section('content')
<!-- Overview Statistics Cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
    
    <!-- Active Jobs -->
    <div class="stat-card flex items-center justify-between shadow-sm animate-fade-in" style="animation-delay: 0.1s;">
        <div>
            <div class="stat-value">{{ $stats['active_jobs'] }}</div>
            <div class="stat-label">Active Job Openings</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-[#FF6B00]/10 flex items-center justify-center text-[#FF6B00] text-xl">
            <i class="fa-solid fa-briefcase"></i>
        </div>
    </div>

    <!-- Total Candidates -->
    <div class="stat-card flex items-center justify-between shadow-sm animate-fade-in" style="animation-delay: 0.2s;">
        <div>
            <div class="stat-value">{{ $stats['total_applications'] }}</div>
            <div class="stat-label">Total Applications</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-blue-50 flex items-center justify-center text-blue-600 text-xl">
            <i class="fa-solid fa-user-group"></i>
        </div>
    </div>

    <!-- Scheduled Interviews -->
    <div class="stat-card flex items-center justify-between shadow-sm animate-fade-in" style="animation-delay: 0.3s;">
        <div>
            <div class="stat-value">{{ $stats['scheduled_interviews'] }}</div>
            <div class="stat-label">Scheduled Interviews</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-purple-50 flex items-center justify-center text-purple-600 text-xl">
            <i class="fa-solid fa-calendar-check"></i>
        </div>
    </div>

    <!-- Hired candidates -->
    <div class="stat-card flex items-center justify-between shadow-sm animate-fade-in" style="animation-delay: 0.4s;">
        <div>
            <div class="stat-value">{{ $stats['hires_count'] }}</div>
            <div class="stat-label">Successful Hires</div>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl">
            <i class="fa-solid fa-trophy"></i>
        </div>
    </div>

</div>

<!-- Funnel Chart & Upcoming schedules -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    
    <!-- Funnel Conversion Horizontal Bar Chart -->
    <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-filter text-[#FF6B00]"></i> Application Recruitment Funnel
        </h3>
        <div class="h-64 relative">
            <canvas id="funnelChart"></canvas>
        </div>
    </div>

    <!-- Upcoming Interviews -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col h-80">
        <h3 class="font-extrabold text-[#111318] text-sm mb-4 flex items-center gap-2">
            <i class="fa-solid fa-clock-rotate-left text-purple-500"></i> Upcoming Interviews
        </h3>
        
        <div class="flex-grow overflow-y-auto space-y-3.5 pr-1">
            @forelse($upcomingInterviews as $interview)
                <div class="flex items-start gap-3 border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                    <div class="w-9 h-9 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-xs font-bold shrink-0">
                        <i class="fa-regular fa-clock"></i>
                    </div>
                    <div class="min-w-0 flex-grow">
                        <h4 class="font-bold text-xs text-[#111318] hover:text-[#FF6B00] transition truncate">
                            <a href="{{ route('applications.show', ['application' => $interview->application_id]) }}">{{ $interview->application->full_name }}</a>
                        </h4>
                        <p class="text-[10px] text-gray-400 font-semibold truncate">{{ $interview->application->jobListing->title }}</p>
                        <span class="text-[9px] text-purple-700 font-bold bg-purple-50 border border-purple-100 rounded-full px-2 py-0.5 mt-1 inline-block">
                            {{ $interview->scheduled_at->format('M d, g:i A') }} ({{ ucfirst($interview->type) }})
                        </span>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 text-gray-400 flex flex-col items-center justify-center gap-2">
                    <i class="fa-regular fa-calendar-times text-2xl text-gray-300"></i>
                    <p class="text-xs">No interviews scheduled soon.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- Recent applications & Job statistics -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Recent Applications Table -->
    <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-extrabold text-[#111318] text-sm flex items-center gap-2">
                <i class="fa-solid fa-history text-[#FF6B00]"></i> Recent Applications
            </h3>
            <a href="{{ route('applications.index') }}" class="text-xs text-[#FF6B00] font-bold hover:underline">View All</a>
        </div>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Candidate</th>
                        <th>Job Listing</th>
                        <th>Current Stage</th>
                        <th>Date</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($recentApps as $app)
                        <tr>
                            <td>
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center font-bold text-xs text-[#FF6B00] uppercase">
                                        {{ $app->initials }}
                                    </div>
                                    <div class="flex flex-col">
                                        <a href="{{ route('applications.show', ['application' => $app->id]) }}" class="font-bold text-xs text-gray-800 hover:text-[#FF6B00] transition">{{ $app->full_name }}</a>
                                        <span class="text-[10px] text-gray-400 font-mono">{{ $app->application_number }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="text-xs text-gray-700 font-semibold">{{ $app->jobListing->title }}</td>
                            <td>
                                <span class="badge badge-gray border border-gray-200">{{ $app->currentStage->name ?? 'N/A' }}</span>
                            </td>
                            <td class="text-xs text-gray-400 font-semibold">{{ $app->created_at->format('M d, Y') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-6 text-xs text-gray-400">No applications received yet.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Active Jobs Performance Card -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <div class="flex justify-between items-center mb-6">
            <h3 class="font-extrabold text-[#111318] text-sm flex items-center gap-2">
                <i class="fa-solid fa-list-check text-yellow-500"></i> Active Jobs
            </h3>
            <a href="{{ route('jobs.manage') }}" class="text-xs text-[#FF6B00] font-bold hover:underline">View All</a>
        </div>

        <div class="space-y-4">
            @forelse($jobs as $job)
                <div class="flex items-center justify-between border-b border-gray-100 pb-3 last:border-0 last:pb-0">
                    <div class="min-w-0">
                        <a href="{{ route('jobs.show', ['job' => $job->id]) }}" class="font-bold text-xs text-gray-850 hover:text-[#FF6B00] transition truncate block">{{ $job->title }}</a>
                        <span class="text-[9px] text-gray-400 font-semibold block mt-0.5">{{ $job->department->name }}</span>
                    </div>
                    <div class="text-right shrink-0">
                        <span class="font-black text-sm text-[#FF6B00]">{{ $job->applications_count }}</span>
                        <span class="text-[9px] text-gray-400 font-semibold block">apps</span>
                    </div>
                </div>
            @empty
                <div class="text-center py-10 text-gray-400 text-xs">No active job listings.</div>
            @endforelse
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        const ctx = document.getElementById('funnelChart').getContext('2d');
        
        // Prepare chart datasets dynamically from server array
        const rawFunnel = @json($funnelData);
        
        const labels = rawFunnel.map(item => item.stage);
        const data = rawFunnel.map(item => item.count);

        new Chart(ctx, {
            type: 'bar',
            data: {
                labels: labels,
                datasets: [{
                    label: 'Applications',
                    data: data,
                    backgroundColor: [
                        'rgba(255, 107, 0, 0.85)', // Applied
                        'rgba(245, 158, 11, 0.85)', // Screening
                        'rgba(59, 130, 246, 0.85)',  // First Interview
                        'rgba(139, 92, 246, 0.85)',  // Tech Panel
                        'rgba(236, 72, 153, 0.85)',  // Offer
                        'rgba(16, 185, 129, 0.85)',  // Hired
                        'rgba(239, 68, 68, 0.85)',   // Rejected
                    ],
                    borderRadius: 8,
                    borderWidth: 0,
                    barThickness: 24,
                }]
            },
            options: {
                indexAxis: 'y', // Horizontal bar chart
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111318',
                        titleColor: '#fff',
                        bodyColor: '#fff',
                        titleFont: { family: 'Sora', weight: 'bold', size: 11 },
                        bodyFont: { family: 'Sora', size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: {
                            color: '#9CA3AF',
                            font: { family: 'Sora', size: 10 }
                        }
                    },
                    y: {
                        grid: { display: false },
                        ticks: {
                            color: '#111318',
                            font: { family: 'Sora', weight: '600', size: 10 }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
