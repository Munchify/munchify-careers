@extends('layouts.public')

@section('title', 'Apply for ' . $job->title . ' - Step 5/5 | Munchify Careers')

@section('content')
<section class="py-12 max-w-3xl mx-auto px-4 sm:px-6">
    
    <!-- Wizard Card Wrapper -->
    <div class="bg-white rounded-3xl border border-gray-200 shadow-sm overflow-hidden animate-slide-up">
        
        <!-- Header banner -->
        <div class="bg-[#111318] p-6 md:p-8 text-white relative overflow-hidden">
            <div class="absolute w-[200px] h-[200px] bg-[#FF6B00]/20 rounded-full blur-[50px] -top-20 -right-20"></div>
            <span class="text-[10px] text-[#FFD233] uppercase font-bold tracking-wider mb-1 block">Application Form</span>
            <h2 class="text-xl font-extrabold text-white leading-none">{{ $job->title }}</h2>
        </div>

        <!-- Horizontal progress steps indicator -->
        <div class="p-6 border-b border-gray-100 flex items-center justify-between text-xs font-semibold bg-[#F9FAFB]">
            <span class="text-[#FF6B00] flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-xs"></i> Personal Details</span>
            <span class="text-[#FF6B00] flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-xs"></i> Experience</span>
            <span class="text-[#FF6B00] flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-xs"></i> Questions</span>
            <span class="text-[#FF6B00] flex items-center gap-1.5"><i class="fa-solid fa-circle-check text-xs"></i> Uploads</span>
            <span class="text-[#FF6B00] flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-[10px]">5</span> Review</span>
        </div>

        <!-- Body Form -->
        <div class="p-6 md:p-10 space-y-8">
            
            <div class="p-4 bg-orange-50 border border-orange-200 text-orange-900 text-xs rounded-xl font-semibold flex items-center gap-2">
                <i class="fa-solid fa-circle-info text-base text-[#FF6B00]"></i>
                <span>Please review all details below. Clicking "Submit Application" will finalize your submission.</span>
            </div>

            <!-- Review sections -->
            <div class="space-y-6">
                
                <!-- Step 1 Summary -->
                <div class="border border-gray-200 rounded-2xl p-6 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2.5">
                        <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                            <i class="fa-solid fa-user text-[#FF6B00]"></i> Personal Details
                        </h3>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="text-[10px] text-[#FF6B00] font-bold hover:underline">Edit</a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 font-medium block">Full Name</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step1']['full_name'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Email Address</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step1']['email'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Phone Number</span>
                            <span class="font-semibold text-gray-800">+254 {{ $wizardData['step1']['phone'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Location</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step1']['location'] ?? 'N/A' }}</span>
                        </div>
                    </div>
                </div>

                <!-- Step 2 Summary -->
                <div class="border border-gray-200 rounded-2xl p-6 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2.5">
                        <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                            <i class="fa-solid fa-briefcase text-[#FF6B00]"></i> Professional Details
                        </h3>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 2]) }}" class="text-[10px] text-[#FF6B00] font-bold hover:underline">Edit</a>
                    </div>
                    
                    <div class="grid grid-cols-2 gap-4 text-xs">
                        <div>
                            <span class="text-gray-400 font-medium block">Current Role</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step2']['current_role'] ?? 'N/A' }}</span>
                        </div>
                        <div>
                            <span class="text-gray-400 font-medium block">Experience Years</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step2']['experience_years'] ?? 'N/A' }}</span>
                        </div>
                        <div class="col-span-2">
                            <span class="text-gray-400 font-medium block">Key Skills</span>
                            <span class="font-semibold text-gray-800">{{ $wizardData['step2']['skills'] ?? 'N/A' }}</span>
                        </div>
                        @if(!empty($wizardData['step2']['motivation']))
                        <div class="col-span-2">
                            <span class="text-gray-400 font-medium block">Motivation</span>
                            <p class="font-medium text-gray-700 mt-1 leading-relaxed">{{ $wizardData['step2']['motivation'] }}</p>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Step 3 Summary -->
                @if(!empty($job->screening_questions) && count($job->screening_questions) > 0)
                <div class="border border-gray-200 rounded-2xl p-6 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2.5">
                        <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                            <i class="fa-solid fa-circle-question text-[#FF6B00]"></i> Screening Answers
                        </h3>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 3]) }}" class="text-[10px] text-[#FF6B00] font-bold hover:underline">Edit</a>
                    </div>
                    
                    <div class="space-y-3 text-xs">
                        @foreach($wizardData['step3']['screening_answers'] ?? [] as $index => $ans)
                            <div>
                                <span class="text-gray-400 font-medium block leading-relaxed">{{ $ans['question'] }}</span>
                                <span class="font-bold text-gray-800">
                                    @if(is_bool($ans['answer']))
                                        {{ $ans['answer'] ? 'Yes' : 'No' }}
                                    @else
                                        {{ $ans['answer'] }}
                                    @endif
                                </span>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif

                <!-- Step 4 Summary -->
                <div class="border border-gray-200 rounded-2xl p-6 space-y-3">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-2.5">
                        <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                            <i class="fa-solid fa-file-arrow-up text-[#FF6B00]"></i> Uploaded Documents
                        </h3>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 4]) }}" class="text-[10px] text-[#FF6B00] font-bold hover:underline">Edit</a>
                    </div>
                    
                    <div class="space-y-2 text-xs font-semibold">
                        @if(!empty($wizardData['step4']['cv_path']))
                            <div class="flex items-center gap-2 text-gray-700">
                                <i class="fa-regular fa-file-pdf text-red-500 text-base"></i>
                                <span>CV Attached: <a href="{{ Storage::url($wizardData['step4']['cv_path']) }}" target="_blank" class="text-[#FF6B00] underline hover:text-[#EA5800]">View Attachment</a></span>
                            </div>
                        @else
                            @if($job->requires_cv)
                                <span class="text-red-500 block"><i class="fa-solid fa-exclamation-triangle"></i> CV document is required!</span>
                            @endif
                        @endif

                        @if(!empty($wizardData['step4']['video_path']))
                            <div class="flex items-center gap-2 text-gray-700 mt-2">
                                <i class="fa-regular fa-file-video text-[#FF6B00] text-base"></i>
                                <span>Video Intro: <a href="{{ Storage::url($wizardData['step4']['video_path']) }}" target="_blank" class="text-[#FF6B00] underline hover:text-[#EA5800]">Watch Video</a></span>
                            </div>
                        @else
                            @if($job->requires_video)
                                <span class="text-red-500 block"><i class="fa-solid fa-exclamation-triangle"></i> Video introduction is required!</span>
                            @endif
                        @endif
                    </div>
                </div>

            </div>

            <form action="{{ route('apply.submit', ['ulid' => $job->ulid]) }}" method="POST" class="pt-6 border-t border-gray-100 flex justify-between">
                @csrf
                <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 4]) }}" class="btn btn-secondary rounded-full px-6 py-3">
                    <i class="fa-solid fa-angle-left"></i> Back to Step 4
                </a>
                <button type="submit" class="btn btn-primary rounded-full px-10 py-3.5 shadow-lg shadow-[#FF6B00]/25 font-bold">
                    Submit Application <i class="fa-solid fa-paper-plane"></i>
                </button>
            </form>
        </div>

    </div>
</section>
@endsection
