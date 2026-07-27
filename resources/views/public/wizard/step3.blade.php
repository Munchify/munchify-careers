@extends('layouts.public')

@section('title', 'Apply for ' . $job->title . ' - Step 3/5 | Munchify Careers')

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
            <span class="text-[#FF6B00] flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-[10px]">3</span> Questions</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">4</span> Uploads</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">5</span> Review</span>
        </div>

        <!-- Body Form -->
        <div class="p-6 md:p-10">
            
            @if(empty($job->screening_questions) || count($job->screening_questions) === 0)
                <!-- Skip screening questions if there are none -->
                <div class="text-center py-8">
                    <p class="text-xs text-gray-500 mb-6">No screening questions are required for this role. You can proceed directly to files upload.</p>
                    <form action="{{ route('apply.step.save', ['ulid' => $job->ulid, 'step' => 3]) }}" method="POST">
                        @csrf
                        <div class="flex justify-between border-t border-gray-100 pt-6">
                            <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 2]) }}" class="btn btn-secondary rounded-full px-6 py-3">
                                <i class="fa-solid fa-angle-left"></i> Back to Step 2
                            </a>
                            <button type="submit" class="btn btn-primary rounded-full px-8 py-3 font-bold">
                                Continue to Step 4 <i class="fa-solid fa-angle-right"></i>
                            </button>
                        </div>
                    </form>
                </div>
            @else
                <form action="{{ route('apply.step.save', ['ulid' => $job->ulid, 'step' => 3]) }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @foreach($job->screening_questions as $index => $question)
                        @php
                            // Check if there is previously submitted data
                            $prevAnswer = $wizardData['step3']['screening_answers'][$index]['answer'] ?? '';
                        @endphp
                        
                        <div class="space-y-2">
                            <label class="form-label text-sm">
                                {{ $question['question'] }}
                                @if(isset($question['knockout']) && $question['knockout'] == true)
                                    <span class="text-red-500" title="This is a required qualifying question">* (Required)</span>
                                @endif
                            </label>
                            
                            @if($question['type'] === 'boolean')
                                <div class="flex gap-4">
                                    <label class="flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                                        <input type="radio" name="answers[{{ $index }}][answer]" value="1" 
                                               class="rounded border-gray-300 text-[#FF6B00] focus:ring-[#FF6B00] mr-2"
                                               {{ $prevAnswer === true || $prevAnswer === '1' || $prevAnswer === 1 ? 'checked' : '' }} required>
                                        Yes
                                    </label>
                                    <label class="flex items-center text-xs font-semibold text-gray-700 cursor-pointer">
                                        <input type="radio" name="answers[{{ $index }}][answer]" value="0" 
                                               class="rounded border-gray-300 text-[#FF6B00] focus:ring-[#FF6B00] mr-2"
                                               {{ $prevAnswer === false || $prevAnswer === '0' || $prevAnswer === 0 ? 'checked' : '' }} required>
                                        No
                                    </label>
                                </div>
                            @elseif($question['type'] === 'number')
                                <input type="number" name="answers[{{ $index }}][answer]" 
                                       class="form-input" 
                                       placeholder="e.g. 2" 
                                       value="{{ $prevAnswer }}" required>
                            @elseif($question['type'] === 'select' && isset($question['options']))
                                <select name="answers[{{ $index }}][answer]" class="form-input" required>
                                    <option value="">-- Select option --</option>
                                    @foreach($question['options'] as $option)
                                        <option value="{{ $option }}" {{ $prevAnswer === $option ? 'selected' : '' }}>{{ $option }}</option>
                                    @endforeach
                                </select>
                            @else
                                <input type="text" name="answers[{{ $index }}][answer]" 
                                       class="form-input" 
                                       placeholder="Your answer" 
                                       value="{{ $prevAnswer }}" required>
                            @endif
                        </div>
                    @endforeach

                    <!-- Footer CTA actions -->
                    <div class="flex justify-between pt-4 border-t border-gray-100">
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 2]) }}" class="btn btn-secondary rounded-full px-6 py-3">
                            <i class="fa-solid fa-angle-left"></i> Back to Step 2
                        </a>
                        <button type="submit" class="btn btn-primary rounded-full px-8 py-3 font-bold">
                            Continue to Step 4 <i class="fa-solid fa-angle-right"></i>
                        </button>
                    </div>
                </form>
            @endif
        </div>

    </div>
</section>
@endsection
