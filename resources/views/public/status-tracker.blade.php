@extends('layouts.public')

@section('title', 'Application Status Tracker | Munchify Careers')

@section('content')
<!-- Header Banner -->
<section class="bg-[#111318] text-white py-12 relative overflow-hidden">
    <div class="absolute w-[400px] h-[400px] bg-[#FF6B00]/10 rounded-full blur-[100px] -top-20 -right-20"></div>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
        <div>
            <div class="flex flex-wrap items-center gap-2 mb-2">
                <span class="app-number">{{ $application->application_number }}</span>
                <span class="badge {{ $application->status_badge_class }}">{{ ucfirst($application->status) }}</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-extrabold text-white">
                {{ $application->full_name }}'s Status Tracker
            </h1>
            <p class="text-xs text-gray-400 mt-1">
                Applying for: <span class="text-white font-bold">{{ $job->title }}</span> &bull; Submitted {{ $application->created_at->format('M d, Y') }}
            </p>
        </div>
        
        <a href="{{ route('careers.jobs') }}" class="btn btn-outline btn-sm rounded-full">
            <i class="fa-solid fa-angle-left"></i> Back to Jobs
        </a>
    </div>
</section>

<!-- Tracking Stepper / Activity -->
<section class="py-12 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Side: Interactive Pipeline Progress -->
        <div class="col-span-1 lg:col-span-2 space-y-6">
            
            <div class="bg-white p-6 md:p-8 rounded-2xl border border-gray-200 shadow-sm">
                <h3 class="font-extrabold text-[#111318] text-base mb-8 flex items-center gap-2">
                    <i class="fa-solid fa-road text-[#FF6B00]"></i> Recruitment Progress
                </h3>

                <!-- Pipeline Stepper Layout -->
                <div class="relative pl-8 space-y-8">
                    <!-- Vertical Line connecting steps -->
                    <div class="absolute left-[15px] top-2 bottom-2 w-0.5 bg-gray-200"></div>

                    @php
                        $currentStageOrder = $application->currentStage->sort_order ?? 1;
                        $isFail = $application->status === 'rejected';
                        $isPass = $application->status === 'hired';
                    @endphp

                    @foreach($stages as $stage)
                        @php
                            $stepOrder = $stage->sort_order;
                            $isCompleted = $stepOrder < $currentStageOrder || $isPass;
                            $isCurrent = $stepOrder === $currentStageOrder && !$isPass && !$isFail;
                            $isStepFail = $isFail && $stepOrder === $currentStageOrder && $stage->is_terminal_fail;
                            
                            // Color variables based on status
                            $dotClass = 'bg-gray-200 text-gray-500';
                            if ($isCompleted) {
                                $dotClass = 'bg-[#FF6B00] text-white';
                            } elseif ($isCurrent) {
                                $dotClass = 'bg-white border-4 border-[#FF6B00] text-[#FF6B00] shadow-[0_0_10px_rgba(255,107,0,0.3)]';
                            } elseif ($isStepFail) {
                                $dotClass = 'bg-red-500 text-white';
                            } elseif ($isPass && $stage->is_terminal_pass) {
                                $dotClass = 'bg-emerald-500 text-white';
                            }
                        @endphp

                        <div class="relative flex gap-6 items-start animate-fade-in">
                            <!-- Bullet dot -->
                            <div class="absolute -left-[33px] w-8 h-8 rounded-full flex items-center justify-center font-bold text-xs z-10 transition duration-300 {{ $dotClass }}">
                                @if($isCompleted && !$stage->is_terminal_pass)
                                    <i class="fa-solid fa-check"></i>
                                @elseif($isStepFail)
                                    <i class="fa-solid fa-xmark"></i>
                                @elseif($isPass && $stage->is_terminal_pass)
                                    <i class="fa-solid fa-trophy"></i>
                                @else
                                    {{ $stepOrder }}
                                @endif
                            </div>

                            <!-- Step label content -->
                            <div class="flex-grow">
                                <h4 class="font-bold text-sm leading-none mb-1 flex items-center gap-2 {{ $isCurrent ? 'text-[#FF6B00]' : 'text-[#111318]' }}">
                                    {{ $stage->name }}
                                    @if($isCurrent)
                                        <span class="inline-flex items-center bg-orange-50 text-[#FF6B00] text-[9px] font-bold uppercase tracking-wider py-0.5 px-2 rounded-full border border-orange-100 animate-pulse">Current Stage</span>
                                    @endif
                                    @if($isStepFail)
                                        <span class="inline-flex items-center bg-red-50 text-red-500 text-[9px] font-bold uppercase tracking-wider py-0.5 px-2 rounded-full border border-red-100">Application Closed</span>
                                    @endif
                                </h4>
                                <p class="text-xs text-gray-500 mt-1 leading-relaxed">
                                    {{ $stage->description }}
                                </p>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <!-- Custom Helper Info -->
            <div class="bg-white p-6 rounded-2xl border border-gray-200 flex gap-4 items-start">
                <div class="w-10 h-10 rounded-xl bg-orange-50 border border-orange-100 text-[#FF6B00] flex items-center justify-center shrink-0">
                    <i class="fa-solid fa-circle-question text-lg"></i>
                </div>
                <div class="space-y-1">
                    <h4 class="font-extrabold text-[#111318] text-sm">Need help or want to reschedule?</h4>
                    <p class="text-xs text-gray-500 leading-relaxed">
                        If you have scheduled an interview and need to change the details, or want to follow up, you can reply directly to the WhatsApp messages sent from our automated recruiter number, or email us at <span class="font-semibold text-gray-700">careers@munchify.co.ke</span>.
                    </p>
                </div>
            </div>

        </div>

        <!-- Right Side: Message timeline / communications logs -->
        <div class="col-span-1">
            <div class="bg-white p-6 rounded-2xl border border-gray-200 shadow-sm flex flex-col h-full max-h-[500px]">
                <h3 class="font-extrabold text-[#111318] text-sm mb-6 flex items-center gap-2">
                    <i class="fa-brands fa-whatsapp text-emerald-500 text-lg"></i> Communication History
                </h3>

                <!-- Message timeline lists -->
                <div class="flex-grow overflow-y-auto space-y-4 pr-1">
                    @forelse($application->communications as $comm)
                        @php
                            $isOutbound = $comm->direction === 'outbound';
                        @endphp
                        <div class="flex flex-col gap-1">
                            <div class="chat-bubble {{ $isOutbound ? 'chat-outbound' : 'chat-inbound' }}">
                                <p class="text-xs leading-relaxed whitespace-pre-line">{{ $comm->message }}</p>
                            </div>
                            <span class="text-[9px] text-gray-400 font-semibold mt-0.5 {{ $isOutbound ? 'text-right' : 'text-left' }}">
                                {{ $comm->sent_at ? $comm->sent_at->format('M d, g:i A') : $comm->created_at->format('M d, g:i A') }}
                            </span>
                        </div>
                    @empty
                        <div class="text-center py-12 text-gray-400 flex flex-col items-center justify-center gap-3">
                            <i class="fa-regular fa-comment-dots text-2xl text-gray-300"></i>
                            <p class="text-xs">No notifications sent yet. You will receive SMS & WhatsApp alerts as your application progresses.</p>
                        </div>
                    @endforelse
                </div>

            </div>
        </div>

    </div>
</section>
@endsection
