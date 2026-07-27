@extends('layouts.public')

@section('title', 'Apply for ' . $job->title . ' - Step 4/5 | Munchify Careers')

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
            <span class="text-[#FF6B00] flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-[10px]">4</span> Uploads</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">5</span> Review</span>
        </div>

        <!-- Body Form -->
        <div class="p-6 md:p-10">
            
            <form action="{{ route('apply.step.save', ['ulid' => $job->ulid, 'step' => 4]) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                @csrf
                
                <!-- CV Upload -->
                @if($job->requires_cv)
                    <div class="space-y-2">
                        <label for="cv" class="form-label">Upload Resume / Supporting Document <span class="text-red-500">*</span></label>
                        <input type="file" name="cv" id="cv" class="form-input p-2.5 text-xs" {{ empty($wizardData['step4']['cv_path']) ? 'required' : '' }}>
                        
                        @if(!empty($wizardData['step4']['cv_path']))
                            <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 border border-emerald-100 p-2.5 rounded-xl font-semibold">
                                <i class="fa-solid fa-file-circle-check text-base"></i>
                                <span>Document uploaded: <a href="{{ Storage::url($wizardData['step4']['cv_path']) }}" target="_blank" class="underline hover:text-emerald-800 font-bold">View Document</a></span>
                            </div>
                        @endif
                        <span class="form-help block">Accepted formats: PDF, DOC, DOCX. Max file size: 5MB.</span>
                    </div>
                @endif

                <!-- Video Intro Upload -->
                @if($job->requires_video)
                    <div class="space-y-3 bg-[#F7F8FA] p-6 rounded-2xl border border-gray-200">
                        <label for="video" class="form-label text-[#111318] font-extrabold flex items-center gap-2">
                            <i class="fa-solid fa-video text-[#FF6B00]"></i> Video Introduction <span class="text-red-500">*</span>
                        </label>
                        
                        <!-- Video prompt box -->
                        <div class="p-4 bg-orange-50 border border-orange-200 text-orange-900 rounded-xl text-xs space-y-1">
                            <h4 class="font-bold flex items-center gap-1"><i class="fa-solid fa-bullhorn text-[10px]"></i> Video Prompt:</h4>
                            <p class="leading-relaxed">{{ $job->video_prompt }}</p>
                        </div>

                        <input type="file" name="video" id="video" class="form-input bg-white p-2.5 text-xs" {{ empty($wizardData['step4']['video_path']) ? 'required' : '' }}>

                        @if(!empty($wizardData['step4']['video_path']))
                            <div class="flex items-center gap-2 text-xs text-emerald-600 bg-emerald-50 border border-emerald-100 p-2.5 rounded-xl font-semibold">
                                <i class="fa-solid fa-video-slash"></i>
                                <span>Video already uploaded: <a href="{{ Storage::url($wizardData['step4']['video_path']) }}" target="_blank" class="underline hover:text-emerald-800 font-bold">Watch Video</a></span>
                            </div>
                        @endif
                        <span class="form-help block">Record using your phone/laptop. Accepted formats: MP4, MOV, WEBM. Max file size: 50MB.</span>
                    </div>
                @endif

                <!-- Footer CTA actions -->
                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 3]) }}" class="btn btn-secondary rounded-full px-6 py-3">
                        <i class="fa-solid fa-angle-left"></i> Back to Step 3
                    </a>
                    <button type="submit" class="btn btn-primary rounded-full px-8 py-3 font-bold">
                        Continue to Step 5 <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
