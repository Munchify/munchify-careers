@extends('layouts.public')

@section('title', 'Apply for ' . $job->title . ' - Step 2/5 | Munchify Careers')

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
            <span class="text-[#FF6B00] flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-[10px]">2</span> Experience</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">3</span> Questions</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">4</span> Uploads</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">5</span> Review</span>
        </div>

        <!-- Body Form -->
        <div class="p-6 md:p-10">
            <form action="{{ route('apply.step.save', ['ulid' => $job->ulid, 'step' => 2]) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Current Role -->
                <div>
                    <label for="current_role" class="form-label">Current Role / Status (Optional)</label>
                    <input type="text" name="current_role" id="current_role" 
                           class="form-input" 
                           placeholder="e.g. Student, Freelance Rider, Unemployed" 
                           value="{{ old('current_role', $wizardData['step2']['current_role'] ?? '') }}">
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Course of Study -->
                    <div>
                        <label for="course_of_study" class="form-label">Course of Study (Optional)</label>
                        <input type="text" name="course_of_study" id="course_of_study" 
                               class="form-input" 
                               placeholder="e.g. BSc Computer Science, Diploma in Logistics" 
                               value="{{ old('course_of_study', $wizardData['step2']['course_of_study'] ?? '') }}">
                    </div>

                    <!-- Year of Study -->
                    <div>
                        <label for="year_of_study" class="form-label">Year of Study (Optional)</label>
                        <select name="year_of_study" id="year_of_study" class="form-input">
                            <option value="">-- Select Year of Study --</option>
                            <option value="Year 1" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Year 1') ? 'selected' : '' }}>Year 1</option>
                            <option value="Year 2" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Year 2') ? 'selected' : '' }}>Year 2</option>
                            <option value="Year 3" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Year 3') ? 'selected' : '' }}>Year 3</option>
                            <option value="Year 4" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Year 4') ? 'selected' : '' }}>Year 4</option>
                            <option value="Postgraduate / Alumni" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Postgraduate / Alumni') ? 'selected' : '' }}>Postgraduate / Alumni</option>
                            <option value="Not Applicable" {{ (old('year_of_study', $wizardData['step2']['year_of_study'] ?? '') == 'Not Applicable') ? 'selected' : '' }}>Not Applicable</option>
                        </select>
                    </div>
                </div>

                <!-- Skills -->
                <div>
                    <label for="skills" class="form-label">Key Skills & Competencies (Optional)</label>
                    <input type="text" name="skills" id="skills" 
                           class="form-input" 
                           placeholder="e.g. Navigation, riding, customer service, communication" 
                           value="{{ old('skills', $wizardData['step2']['skills'] ?? '') }}">
                </div>

                <!-- Motivation -->
                <div>
                    <label for="motivation" class="form-label">Why do you want to join Munchify? (Optional)</label>
                    <textarea name="motivation" id="motivation" rows="3" 
                              class="form-input resize-none" 
                              placeholder="Briefly tell us what motivates you to work with Munchify...">{{ old('motivation', $wizardData['step2']['motivation'] ?? '') }}</textarea>
                </div>

                <!-- Cover letter -->
                <div>
                    <label for="cover_letter" class="form-label">Cover Letter / Additional Notes (Optional)</label>
                    <textarea name="cover_letter" id="cover_letter" rows="5" 
                              class="form-input" 
                              placeholder="Describe your qualifications, experience, and why you are the best fit for this role...">{{ old('cover_letter', $wizardData['step2']['cover_letter'] ?? '') }}</textarea>
                </div>

                <!-- Footer CTA actions -->
                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn btn-secondary rounded-full px-6 py-3">
                        <i class="fa-solid fa-angle-left"></i> Back to Step 1
                    </a>
                    <button type="submit" class="btn btn-primary rounded-full px-8 py-3 font-bold">
                        Continue to Step 3 <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
