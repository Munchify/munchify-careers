@extends('layouts.public')

@section('title', 'Browse Job Openings - Munchify Careers')

@section('content')
<!-- Search / Header banner -->
<section class="bg-[#111318] text-white py-12 relative overflow-hidden">
    <div class="absolute w-[300px] h-[300px] bg-[#FF6B00]/10 rounded-full blur-[80px] -top-20 -right-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        <h1 class="text-3xl font-extrabold mb-2">Explore Open Positions</h1>
        <p class="text-xs text-gray-400">Discover your next opportunity at Maseno University's premiere delivery network.</p>
    </div>
</section>

<!-- Filterable Listings Grid -->
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
        
        <!-- SIDEBAR FILTERS (Desktop) -->
        <div class="col-span-1 bg-white p-6 rounded-2xl border border-gray-200 h-fit sticky top-24">
            <h3 class="font-extrabold text-[#111318] text-sm mb-4 flex items-center gap-2">
                <i class="fa-solid fa-filter text-[#FF6B00]"></i> Filter Jobs
            </h3>

            <form action="{{ route('careers.jobs') }}" method="GET" class="space-y-6">
                <!-- Text Search -->
                <div>
                    <label for="search" class="form-label text-xs">Search Keywords</label>
                    <div class="relative">
                        <input type="text" name="search" id="search" value="{{ request('search') }}" class="form-input text-xs pl-8" placeholder="e.g. Developer, Rider">
                        <i class="fa-solid fa-magnifying-glass absolute left-3 top-3 text-[10px] text-gray-400"></i>
                    </div>
                </div>

                <!-- Department Filter -->
                <div>
                    <label for="department" class="form-label text-xs">Department</label>
                    <select name="department" id="department" class="form-input text-xs">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                            <option value="{{ $dept->id }}" {{ request('department') == $dept->id ? 'selected' : '' }}>{{ $dept->name }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Job Type Filter -->
                <div>
                    <label for="type" class="form-label text-xs">Job Type</label>
                    <select name="type" id="type" class="form-input text-xs">
                        <option value="">All Types</option>
                        <option value="full_time" {{ request('type') === 'full_time' ? 'selected' : '' }}>Full-time</option>
                        <option value="part_time" {{ request('type') === 'part_time' ? 'selected' : '' }}>Part-time</option>
                        <option value="contract" {{ request('type') === 'contract' ? 'selected' : '' }}>Contract</option>
                        <option value="internship" {{ request('type') === 'internship' ? 'selected' : '' }}>Internship</option>
                    </select>
                </div>

                <!-- Location Filter -->
                <div>
                    <label for="location" class="form-label text-xs">Workplace Location</label>
                    <select name="location" id="location" class="form-input text-xs">
                        <option value="">All Locations</option>
                        <option value="on_site" {{ request('location') === 'on_site' ? 'selected' : '' }}>On-site</option>
                        <option value="remote" {{ request('location') === 'remote' ? 'selected' : '' }}>Remote</option>
                        <option value="hybrid" {{ request('location') === 'hybrid' ? 'selected' : '' }}>Hybrid</option>
                    </select>
                </div>

                <!-- Actions -->
                <div class="flex gap-2">
                    <button type="submit" class="btn btn-primary btn-sm flex-grow py-2.5 font-bold text-xs rounded-xl">
                        Apply Filters
                    </button>
                    @if(request()->anyFilled(['search', 'department', 'type', 'location']))
                        <a href="{{ route('careers.jobs') }}" class="btn btn-secondary btn-sm py-2.5 px-3 rounded-xl" title="Clear Filters">
                            <i class="fa-solid fa-rotate-left"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>

        <!-- JOB LISTINGS COLUMN -->
        <div class="col-span-1 lg:col-span-3 space-y-4">
            
            <div class="flex justify-between items-center mb-2">
                <span class="text-xs text-gray-500 font-semibold">
                    Showing {{ $jobs->firstItem() ?? 0 }}-{{ $jobs->lastItem() ?? 0 }} of {{ $jobs->total() }} open roles
                </span>
            </div>

            @forelse($jobs as $job)
                <div class="card bg-white p-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-l-4 border-l-[#FF6B00] shadow-sm hover:shadow-md transition">
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-2 mb-2">
                            <span class="badge badge-orange">{{ $job->department->name }}</span>
                            <span class="badge badge-gray">{{ $job->type_label }}</span>
                            <span class="badge badge-gray">{{ $job->location_label }}</span>
                        </div>
                        <h3 class="font-extrabold text-lg text-[#111318] hover:text-[#FF6B00] transition mb-1">
                            <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}">{{ $job->title }}</a>
                        </h3>
                        <p class="text-xs text-gray-500 line-clamp-2 mb-3 leading-relaxed">
                            {{ Str::limit($job->description, 160) }}
                        </p>
                        <div class="flex items-center gap-4 text-[11px] text-gray-400 font-semibold">
                            <span><i class="fa-solid fa-map-marker-alt"></i> {{ $job->location_detail }}</span>
                            @if($job->salary_range)
                            <span><i class="fa-solid fa-wallet"></i> {{ $job->salary_range }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-2 w-full md:w-auto shrink-0">
                        <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}" class="btn btn-secondary w-full md:w-auto py-2 px-5 text-xs">
                            View details
                        </a>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn btn-primary w-full md:w-auto py-2 px-5 text-xs">
                            Apply
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 card bg-white flex flex-col items-center justify-center">
                    <div class="w-16 h-16 rounded-full bg-gray-50 border border-gray-100 flex items-center justify-center text-gray-400 text-2xl mb-4">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </div>
                    <h3 class="font-bold text-gray-800 mb-1">No Jobs Found</h3>
                    <p class="text-xs text-gray-500 max-w-sm">We couldn't find any openings matching your query. Try clearing filters or searching other keywords.</p>
                </div>
            @endforelse

            <!-- Pagination links -->
            <div class="mt-8">
                {{ $jobs->appends(request()->query())->links() }}
            </div>

        </div>

    </div>
</section>
@endsection
