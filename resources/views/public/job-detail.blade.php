@extends('layouts.public')

@section('title', $job->title . ' - Munchify Careers')

@section('content')
<!-- Header Banner -->
<section class="bg-[#111318] text-white py-16 relative overflow-hidden">
    <div class="absolute w-[400px] h-[400px] bg-[#FF6B00]/15 rounded-full blur-[100px] -top-20 -right-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="flex flex-wrap items-center gap-2 mb-4">
            <span class="badge badge-orange bg-[#FF6B00] text-white border border-[#FF6B00]">{{ $job->department->name }}</span>
            <span class="badge badge-outline border-white/20 text-white/90 bg-white/5">{{ $job->type_label }}</span>
            <span class="badge badge-outline border-white/20 text-white/90 bg-white/5">{{ $job->location_label }}</span>
        </div>
        
        <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-4 text-white">
            {{ $job->title }}
        </h1>

        <div class="flex flex-wrap items-center gap-6 text-xs text-gray-400 font-semibold">
            <span><i class="fa-solid fa-map-marker-alt text-[#FFD233] mr-1"></i> {{ $job->location_detail }}</span>
            @if($job->salary_range)
            <span><i class="fa-solid fa-wallet text-[#FFD233] mr-1"></i> {{ $job->salary_range }}</span>
            @endif
            @if($job->application_deadline)
            <span><i class="fa-solid fa-calendar-alt text-[#FFD233] mr-1"></i> Apply before: {{ $job->application_deadline->format('M d, Y') }}</span>
            @endif
        </div>
    </div>
</section>

<!-- Two Column Details -->
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Rich Job Details -->
        <div class="col-span-1 lg:col-span-2 space-y-8 bg-white p-6 md:p-10 rounded-2xl border border-gray-200 shadow-sm">
            
            <!-- Description -->
            <div>
                <h2 class="text-lg font-bold text-[#111318] border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-file-invoice text-[#FF6B00]"></i> Role Overview
                </h2>
                <div class="prose-content text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $job->description }}
                </div>
            </div>

            <!-- Responsibilities -->
            <div>
                <h2 class="text-lg font-bold text-[#111318] border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-clipboard-list text-[#FF6B00]"></i> Key Responsibilities
                </h2>
                <div class="prose-content text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $job->responsibilities }}
                </div>
            </div>

            <!-- Requirements -->
            <div>
                <h2 class="text-lg font-bold text-[#111318] border-b border-gray-100 pb-3 mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-circle-check text-[#FF6B00]"></i> Job Requirements
                </h2>
                <div class="prose-content text-xs text-gray-600 leading-relaxed whitespace-pre-line">
                    {{ $job->requirements }}
                </div>
            </div>

        </div>

        <!-- Right Side: Sidebar card -->
        <div class="col-span-1 space-y-6">
            
            <!-- Quick Facts Card -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm space-y-6">
                <h3 class="font-extrabold text-[#111318] text-sm flex items-center gap-2">
                    <i class="fa-solid fa-info-circle text-[#FF6B00]"></i> Job Summary
                </h3>

                <div class="space-y-4">
                    <div class="flex justify-between items-center text-xs py-2 border-b border-gray-100">
                        <span class="text-gray-400 font-medium">Department</span>
                        <span class="font-bold text-[#111318]">{{ $job->department->name }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs py-2 border-b border-gray-100">
                        <span class="text-gray-400 font-medium">Employment Type</span>
                        <span class="font-bold text-[#111318]">{{ $job->type_label }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs py-2 border-b border-gray-100">
                        <span class="text-gray-400 font-medium">Workplace Setting</span>
                        <span class="font-bold text-[#111318]">{{ $job->location_label }}</span>
                    </div>

                    <div class="flex justify-between items-center text-xs py-2 border-b border-gray-100">
                        <span class="text-gray-400 font-medium">Available Openings</span>
                        <span class="font-bold text-[#111318]">{{ $job->slots }} {{ Str::plural('slot', $job->slots) }}</span>
                    </div>

                    @if($job->salary_range)
                    <div class="flex justify-between items-center text-xs py-2 border-b border-gray-100">
                        <span class="text-gray-400 font-medium">Offered Compensation</span>
                        <span class="font-bold text-[#FF6B00]">{{ $job->salary_range }}</span>
                    </div>
                    @endif

                    @if($job->application_deadline)
                    <div class="flex justify-between items-center text-xs py-2">
                        <span class="text-gray-400 font-medium">Application Deadline</span>
                        <span class="font-bold text-[#111318]">{{ $job->application_deadline->format('Y-m-d') }}</span>
                    </div>
                    @endif
                </div>

                @if($job->isOpen())
                    <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn btn-primary w-full py-3.5 shadow-lg shadow-[#FF6B00]/25 rounded-full font-bold text-center">
                        Apply for this position <i class="fa-solid fa-arrow-right"></i>
                    </a>
                @else
                    <button class="btn btn-secondary w-full py-3.5 rounded-full cursor-not-allowed font-bold" disabled>
                        Applications Closed
                    </button>
                @endif
            </div>

            <!-- Similar Positions Card -->
            <div class="bg-[#111318] text-white p-6 rounded-2xl shadow-sm">
                <h3 class="font-extrabold text-sm text-[#FFD233] mb-4 flex items-center gap-2">
                    <i class="fa-solid fa-briefcase"></i> Other Openings
                </h3>
                
                <div class="space-y-4">
                    @forelse($similarJobs as $simJob)
                        <div class="border-b border-white/5 pb-4 last:border-b-0 last:pb-0">
                            <h4 class="font-bold text-xs hover:text-[#FF6B00] transition mb-1">
                                <a href="{{ route('careers.jobs.show', ['ulid' => $simJob->ulid]) }}">{{ $simJob->title }}</a>
                            </h4>
                            <span class="text-[10px] text-gray-500 font-semibold">{{ $simJob->department->name }} &bull; {{ $simJob->location_label }}</span>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">No other roles listed at this time.</p>
                    @endforelse
                </div>
            </div>

        </div>

    </div>
</section>
@endsection
