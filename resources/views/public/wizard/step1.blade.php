@extends('layouts.public')

@section('title', 'Apply for ' . $job->title . ' - Step 1/5 | Munchify Careers')

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
            <span class="text-[#FF6B00] flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-[#FF6B00] text-white flex items-center justify-center text-[10px]">1</span> Personal Details</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">2</span> Experience</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">3</span> Questions</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">4</span> Uploads</span>
            <span class="text-gray-400 flex items-center gap-1.5"><span class="w-5 h-5 rounded-full bg-gray-200 text-gray-500 flex items-center justify-center text-[10px]">5</span> Review</span>
        </div>

        <!-- Body Form -->
        <div class="p-6 md:p-10">
            <form action="{{ route('apply.step.save', ['ulid' => $job->ulid, 'step' => 1]) }}" method="POST" class="space-y-6">
                @csrf
                
                <!-- Full Name -->
                <div>
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" name="full_name" id="full_name" 
                           class="form-input" 
                           placeholder="e.g. John Otieno" 
                           value="{{ old('full_name', $wizardData['step1']['full_name'] ?? '') }}" required>
                </div>

                <!-- Email Address -->
                <div>
                    <label for="email" class="form-label">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="form-input" 
                           placeholder="e.g. john.otieno@gmail.com" 
                           value="{{ old('email', $wizardData['step1']['email'] ?? '') }}" required>
                </div>

                <!-- Phone number with custom +254 prefix -->
                <div>
                    <label for="phone" class="form-label">Phone Number (WhatsApp Active)</label>
                    <div class="flex">
                        <span class="phone-prefix">+254</span>
                        <input type="tel" name="phone" id="phone" 
                               class="form-input phone-input" 
                               placeholder="e.g. 712345678" 
                               value="{{ old('phone', $wizardData['step1']['phone'] ?? '') }}" required>
                    </div>
                    <span class="form-help">Provide your active mobile number. We will send updates via WhatsApp and SMS.</span>
                </div>

                <!-- Current Residence Location -->
                <div>
                    <label for="location" class="form-label">Current Residence / Location</label>
                    <input type="text" name="location" id="location" 
                           class="form-input" 
                           placeholder="e.g. Maseno University, Millennium Hall" 
                           value="{{ old('location', $wizardData['step1']['location'] ?? '') }}" required>
                </div>

                <!-- Candidate Profile Picture / Headshot Upload -->
                <div class="p-4 bg-[#F8FAFC] border border-gray-200 rounded-2xl space-y-2">
                    <label for="profile_photo" class="form-label text-gray-800 font-bold flex items-center gap-2">
                        <i class="fa-solid fa-user-circle text-[#FF6B00]"></i> Profile Picture / Headshot
                    </label>
                    <input type="file" name="profile_photo" id="profile_photo" accept="image/*" class="form-input bg-white p-2.5 text-xs">
                    <span class="form-help block">Upload a clear photo of yourself (JPEG, PNG).</span>
                </div>

                <!-- Footer CTA actions -->
                <div class="flex justify-end pt-4 border-t border-gray-100">
                    <button type="submit" class="btn btn-primary rounded-full px-8 py-3 font-bold">
                        Continue to Step 2 <i class="fa-solid fa-angle-right"></i>
                    </button>
                </div>
            </form>
        </div>

    </div>
</section>
@endsection
