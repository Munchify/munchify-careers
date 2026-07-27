@extends('layouts.dashboard')

@section('title', 'Recruitment Analytics - Recruiter Dashboard')
@section('header_title', 'Recruitment Analytics')

@section('content')
<!-- Period & Job Filters -->
<div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm mb-8">
    <form action="{{ route('analytics.index') }}" method="GET" class="flex flex-col sm:flex-row gap-4 items-end sm:items-center justify-between">
        <div class="flex flex-wrap gap-4 items-center flex-grow">
            <!-- Period -->
            <div class="w-full sm:w-44">
                <label for="period" class="form-label text-[10px] text-gray-400">Date Period</label>
                <select name="period" id="period" class="form-input text-xs" onchange="this.form.submit()">
                    <option value="all" {{ $period === 'all' ? 'selected' : '' }}>All Time</option>
                    <option value="7_days" {{ $period === '7_days' ? 'selected' : '' }}>Last 7 Days</option>
                    <option value="30_days" {{ $period === '30_days' ? 'selected' : '' }}>Last 30 Days</option>
                    <option value="90_days" {{ $period === '90_days' ? 'selected' : '' }}>Last 90 Days</option>
                </select>
            </div>

            <!-- Job Opening -->
            <div class="w-full sm:w-64">
                <label for="job_id" class="form-label text-[10px] text-gray-400">Job Listing (Funnel Filter)</label>
                <select name="job_id" id="job_id" class="form-input text-xs" onchange="this.form.submit()">
                    <option value="">All Positions</option>
                    @foreach($jobs as $job)
                        <option value="{{ $job->id }}" {{ $jobId == $job->id ? 'selected' : '' }}>{{ $job->title }}</option>
                    @endforeach
                </select>
            </div>
        </div>
        
        <button type="submit" class="btn btn-secondary btn-sm py-2 px-5 font-bold rounded-xl text-xs shrink-0">
            Refresh Data
        </button>
    </form>
</div>

<!-- Core metrics summary stats cards -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6 mb-8">
    
    <!-- Active Jobs -->
    <div class="stat-card flex flex-col justify-between shadow-sm">
        <span class="stat-label">Active Roles</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="stat-value text-2xl">{{ $stats['active_jobs'] }}</span>
            <span class="w-7 h-7 rounded-lg bg-[#FF6B00]/10 flex items-center justify-center text-[#FF6B00] text-xs"><i class="fa-solid fa-briefcase"></i></span>
        </div>
    </div>

    <!-- Total Applications -->
    <div class="stat-card flex flex-col justify-between shadow-sm">
        <span class="stat-label">Total Applicants</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="stat-value text-2xl">{{ $stats['total_applications'] }}</span>
            <span class="w-7 h-7 rounded-lg bg-blue-50 flex items-center justify-center text-blue-600 text-xs"><i class="fa-solid fa-user-group"></i></span>
        </div>
    </div>

    <!-- Hires count -->
    <div class="stat-card flex flex-col justify-between shadow-sm">
        <span class="stat-label">Total Hired</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="stat-value text-2xl text-emerald-600">{{ $stats['hires_count'] }}</span>
            <span class="w-7 h-7 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-600 text-xs"><i class="fa-solid fa-trophy"></i></span>
        </div>
    </div>

    <!-- Conversion rate -->
    <div class="stat-card flex flex-col justify-between shadow-sm">
        <span class="stat-label">Hiring Velocity</span>
        <div class="flex items-baseline justify-between mt-2">
            @php
                $conversionRate = $stats['total_applications'] > 0 
                    ? round(($stats['hires_count'] / $stats['total_applications']) * 100, 1) 
                    : 0.0;
            @endphp
            <span class="stat-value text-2xl">{{ $conversionRate }}%</span>
            <span class="w-7 h-7 rounded-lg bg-orange-50 flex items-center justify-center text-[#FF6B00] text-xs"><i class="fa-solid fa-bolt"></i></span>
        </div>
    </div>

    <!-- Average Time to Hire -->
    <div class="stat-card flex flex-col justify-between shadow-sm">
        <span class="stat-label">Avg. Time-to-Hire</span>
        <div class="flex items-baseline justify-between mt-2">
            <span class="stat-value text-2xl text-purple-600">{{ $timeToHire }} <span class="text-[10px] text-gray-400 font-semibold">days</span></span>
            <span class="w-7 h-7 rounded-lg bg-purple-50 flex items-center justify-center text-purple-600 text-xs"><i class="fa-regular fa-clock"></i></span>
        </div>
    </div>

</div>

<!-- Funnel details & Sources donut charts -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-8">
    
    <!-- Funnel -->
    <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-filter text-[#FF6B00]"></i> Pipeline Funnel Distribution
        </h3>
        <div class="h-80 relative">
            <canvas id="funnelChart"></canvas>
        </div>
    </div>

    <!-- Candidate Sources -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col">
        <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-pie-chart text-yellow-500"></i> Applications by Source
        </h3>
        <div class="h-60 relative flex-grow">
            <canvas id="sourceChart"></canvas>
        </div>
    </div>

</div>

<!-- Job performance & Team activity tables -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Job Performance Table -->
    <div class="col-span-1 lg:col-span-2 bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-briefcase text-[#FF6B00]"></i> Job Performance Matrix
        </h3>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Job Title</th>
                        <th>Department</th>
                        <th>Hiring Lead</th>
                        <th>Applications</th>
                        <th>Hires</th>
                        <th>Conversion</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($jobPerformance as $perf)
                        <tr>
                            <td class="font-bold text-xs text-gray-800">{{ $perf['title'] }}</td>
                            <td class="text-xs text-gray-700 font-semibold">{{ $perf['department'] }}</td>
                            <td class="text-xs text-gray-650">{{ $perf['hiring_manager'] }}</td>
                            <td class="text-xs font-bold text-center">{{ $perf['applications_count'] }}</td>
                            <td class="text-xs font-bold text-center text-emerald-600">{{ $perf['hires_count'] }}</td>
                            <td class="text-xs font-bold text-[#FF6B00] text-center">{{ $perf['conversion_rate'] }}%</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-6 text-xs text-gray-400">No job openings recorded.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Reviewers Team activity Table -->
    <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm">
        <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
            <i class="fa-solid fa-user-check text-purple-500"></i> Reviewer Activity
        </h3>

        <div class="overflow-x-auto">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Reviewer Name</th>
                        <th>Evaluations</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($teamPerformance as $team)
                        <tr>
                            <td>
                                <div class="flex flex-col">
                                    <span class="font-bold text-xs text-gray-800">{{ $team['full_name'] }}</span>
                                    <span class="text-[9px] text-gray-400 font-semibold capitalize">{{ str_replace('_', ' ', $team['role']) }}</span>
                                </div>
                            </td>
                            <td class="text-xs font-bold text-[#FF6B00]">{{ $team['scores_count'] }} reviews</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-6 text-xs text-gray-400">No evaluations logged.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // 1. Pipeline Funnel chart
        const funnelCtx = document.getElementById('funnelChart').getContext('2d');
        const funnelData = @json($funnelData);
        
        new Chart(funnelCtx, {
            type: 'bar',
            data: {
                labels: funnelData.map(item => item.stage),
                datasets: [{
                    data: funnelData.map(item => item.count),
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
                    barThickness: 28,
                }]
            },
            options: {
                indexAxis: 'y',
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: '#111318',
                        titleFont: { family: 'Sora', weight: 'bold', size: 11 },
                        bodyFont: { family: 'Sora', size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                scales: {
                    x: {
                        grid: { display: false },
                        ticks: { color: '#9CA3AF', font: { family: 'Sora', size: 10 } }
                    },
                    y: {
                        grid: { display: false },
                        ticks: { color: '#111318', font: { family: 'Sora', weight: '600', size: 10 } }
                    }
                }
            }
        });

        // 2. Candidate Sources Donut chart
        const sourceCtx = document.getElementById('sourceChart').getContext('2d');
        const sourceRaw = @json($sourceData);

        new Chart(sourceCtx, {
            type: 'doughnut',
            data: {
                labels: Object.keys(sourceRaw),
                datasets: [{
                    data: Object.values(sourceRaw),
                    backgroundColor: [
                        '#FF6B00', // Direct
                        '#FFD233', // Referral
                        '#3B82F6', // Social Media
                        '#9CA3AF'  // Other
                    ],
                    borderWidth: 2,
                    borderColor: '#ffffff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            color: '#111318',
                            font: { family: 'Sora', weight: '600', size: 10 },
                            boxWidth: 12,
                            padding: 16
                        }
                    },
                    tooltip: {
                        backgroundColor: '#111318',
                        titleFont: { family: 'Sora', weight: 'bold', size: 11 },
                        bodyFont: { family: 'Sora', size: 11 },
                        padding: 10,
                        cornerRadius: 8,
                    }
                },
                cutout: '65%'
            }
        });
    });
</script>
@endsection
