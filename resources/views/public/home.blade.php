@extends('layouts.public')

@section('title', 'Munchify Careers - Fast-Track Your Student Career at Maseno')

@section('content')
<!-- 1. HERO SECTION WITH VIBRANT DARK GRADIENTS & METRICS -->
<section class="bg-[#0D0F12] text-white py-24 md:py-36 relative overflow-hidden flex items-center">
    <!-- Glowing background Orbs -->
    <div class="absolute w-[650px] h-[650px] bg-[#FF6B00]/20 rounded-full blur-[140px] -top-80 -right-20 pointer-events-none"></div>
    <div class="absolute w-[550px] h-[550px] bg-[#FFD233]/15 rounded-full blur-[130px] -bottom-40 -left-40 pointer-events-none"></div>
    
    <!-- Subtle Grid Overlay -->
    <div class="absolute inset-0 bg-[linear-gradient(to_right,#ffffff05_1px,transparent_1px),linear-gradient(to_bottom,#ffffff05_1px,transparent_1px)] bg-[size:4rem_4rem] pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <!-- Live Status Pill Badge -->
        <div class="inline-flex items-center gap-2.5 bg-white/5 border border-white/10 backdrop-blur-md rounded-full py-2 px-5 text-xs font-bold text-[#FF6B00] mb-8 shadow-2xl animate-fade-in">
            <span class="relative flex h-2.5 w-2.5">
                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-[#FF6B00] opacity-75"></span>
                <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-[#FF6B00]"></span>
            </span>
            <span>Maseno Campus Recruitment Drive 2026</span>
            <span class="text-white/40">|</span>
            <span class="text-white font-medium">Flexible Student Shifts</span>
        </div>
        
        <!-- Headline -->
        <h1 class="text-4xl sm:text-6xl md:text-7xl font-extrabold tracking-tight leading-[1.1] max-w-5xl mx-auto mb-8 text-white">
            Build Your Future With Kenya's <span class="bg-gradient-to-r from-[#FF6B00] via-[#FFA033] to-[#FFD233] bg-clip-text text-transparent">Fastest Campus Fleet</span>
        </h1>
        
        <!-- Subtitle -->
        <p class="text-base sm:text-xl text-gray-300 max-w-3xl mx-auto mb-12 leading-relaxed font-normal">
            Join the team behind Maseno University's premier food delivery network. Enjoy flexible student hours, competitive earnings, and real career growth opportunities.
        </p>

        <!-- CTA Action Buttons -->
        <div class="flex flex-col sm:flex-row justify-center items-center gap-4 max-w-md mx-auto">
            <a href="#open-positions" class="btn bg-gradient-to-r from-[#FF6B00] to-[#E05D00] hover:from-[#E05D00] hover:to-[#C85200] text-white btn-lg w-full sm:w-auto shadow-xl shadow-[#FF6B00]/25 rounded-full font-bold px-8 py-4 text-sm transition-all transform hover:-translate-y-0.5">
                Explore Open Roles <i class="fa-solid fa-arrow-down ml-2"></i>
            </a>
            <a href="#" @click.prevent="openTrackModal()" class="btn bg-white/10 hover:bg-white/15 text-white border border-white/15 btn-lg w-full sm:w-auto rounded-full font-bold px-8 py-4 text-sm backdrop-blur-md transition">
                Track Application <i class="fa-solid fa-location-arrow ml-2 text-[#FFD233]"></i>
            </a>
        </div>

        <!-- Live Impact Metrics Stats Strip -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 max-w-4xl mx-auto mt-16 pt-12 border-t border-white/10">
            <div>
                <span class="block text-3xl md:text-4xl font-black text-white font-mono">10,000+</span>
                <span class="text-xs text-gray-400 font-medium">Campus Orders Delivered</span>
            </div>
            <div>
                <span class="block text-3xl md:text-4xl font-black text-[#FFD233] font-mono">15 Mins</span>
                <span class="text-xs text-gray-400 font-medium">Avg Delivery Speed</span>
            </div>
            <div>
                <span class="block text-3xl md:text-4xl font-black text-[#FF6B00] font-mono">100%</span>
                <span class="text-xs text-gray-400 font-medium">Student Friendly Hours</span>
            </div>
            <div>
                <span class="block text-3xl md:text-4xl font-black text-emerald-400 font-mono">Weekly</span>
                <span class="text-xs text-gray-400 font-medium">Guaranteed Payouts</span>
            </div>
        </div>
    </div>
</section>

<!-- 2. WHY JOIN MUNCHIFY (BENEFITS & PERKS) -->
<section class="py-24 bg-white relative">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold text-[#FF6B00] uppercase tracking-wider block mb-2">Why Choose Munchify</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111318] tracking-tight mb-4">Designed Around Student Success</h2>
            <p class="text-sm sm:text-base text-gray-500 leading-relaxed">We empower Maseno University students to earn independently without compromising their academic goals.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 hover:border-[#FF6B00]/40 transition duration-300 hover:shadow-xl hover:shadow-[#FF6B00]/5 flex flex-col gap-4 group">
                <div class="w-14 h-14 rounded-2xl bg-orange-100/60 text-[#FF6B00] flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-clock"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[#111318]">Flexible Shifts</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Select your working hours around your lecture timetable, exams, and personal study routines.</p>
            </div>

            <!-- Feature 2 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 hover:border-[#FF6B00]/40 transition duration-300 hover:shadow-xl hover:shadow-[#FF6B00]/5 flex flex-col gap-4 group">
                <div class="w-14 h-14 rounded-2xl bg-amber-100/60 text-amber-600 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-wallet"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[#111318]">Fast & Fair Earnings</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Enjoy reliable weekly M-Pesa payouts plus performance bonuses and meal accuracy rewards.</p>
            </div>

            <!-- Feature 3 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 hover:border-[#FF6B00]/40 transition duration-300 hover:shadow-xl hover:shadow-[#FF6B00]/5 flex flex-col gap-4 group">
                <div class="w-14 h-14 rounded-2xl bg-blue-100/60 text-blue-600 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-[#FF6B00] fa-users"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[#111318]">Vibrant Team Culture</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Work alongside fellow energetic campus peers in a supportive, friendly team environment.</p>
            </div>

            <!-- Feature 4 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 hover:border-[#FF6B00]/40 transition duration-300 hover:shadow-xl hover:shadow-[#FF6B00]/5 flex flex-col gap-4 group">
                <div class="w-14 h-14 rounded-2xl bg-emerald-100/60 text-emerald-600 flex items-center justify-center text-2xl font-bold group-hover:scale-110 transition duration-300">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="font-extrabold text-xl text-[#111318]">Real Leadership Path</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Top performers advance into fleet supervision, operations management, or software engineering roles.</p>
            </div>
        </div>

    </div>
</section>

<!-- 3. FEATURED JOB OPENINGS LIST -->
<section id="open-positions" class="py-24 bg-[#F8FAFC] border-t border-b border-gray-200/60">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-14 gap-4">
            <div>
                <span class="text-xs font-extrabold text-[#FF6B00] uppercase tracking-wider block mb-2">Careers Catalog</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111318] tracking-tight">Active Opportunities</h2>
                <p class="text-sm text-gray-500 mt-1">Select a role below to start your quick 2-minute application.</p>
            </div>
            <a href="{{ route('careers.jobs') }}" class="btn btn-secondary rounded-full py-3 px-7 font-bold text-xs hover:border-[#FF6B00] hover:text-[#FF6B00] transition">
                View All Openings <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Job Openings Grid -->
        <div class="space-y-4">
            @forelse($latestJobs as $job)
                <div class="bg-white rounded-3xl p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border border-gray-200/80 hover:border-[#FF6B00] shadow-sm hover:shadow-xl transition duration-300 group">
                    <div class="flex-grow space-y-3">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge bg-[#FF6B00]/10 text-[#FF6B00] border border-[#FF6B00]/20 font-bold px-3 py-1 text-[11px] rounded-full">{{ $job->department->name }}</span>
                            <span class="badge bg-gray-100 text-gray-700 font-semibold px-3 py-1 text-[11px] rounded-full">{{ $job->type_label }}</span>
                            <span class="badge bg-gray-100 text-gray-700 font-semibold px-3 py-1 text-[11px] rounded-full">{{ $job->location_label }}</span>
                        </div>

                        <h3 class="font-extrabold text-2xl text-[#111318] group-hover:text-[#FF6B00] transition">
                            <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}">{{ $job->title }}</a>
                        </h3>

                        <div class="flex flex-wrap items-center gap-6 text-xs text-gray-500 font-semibold pt-1">
                            <span><i class="fa-solid fa-location-dot text-[#FF6B00] mr-1.5"></i> {{ $job->location_detail ?: 'Maseno Campus' }}</span>
                            <span><i class="fa-solid fa-user-group text-[#FF6B00] mr-1.5"></i> {{ $job->slots }} {{ Str::plural('Vacancy', $job->slots) }}</span>
                            @if($job->application_deadline)
                            <span><i class="fa-solid fa-clock text-[#FF6B00] mr-1.5"></i> Apply by {{ $job->application_deadline->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto shrink-0">
                        <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}" class="btn btn-secondary w-full md:w-auto px-6 py-3 rounded-full font-bold text-xs border-gray-300">
                            Details
                        </a>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn bg-gradient-to-r from-[#FF6B00] to-[#E05D00] hover:from-[#E05D00] hover:to-[#C85200] text-white w-full md:w-auto px-7 py-3 rounded-full font-bold text-xs shadow-md shadow-[#FF6B00]/20">
                            Apply Now <i class="fa-solid fa-angle-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-3xl border border-gray-200">
                    <p class="text-gray-500 font-semibold text-sm">No featured openings available right now. Please check back soon!</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- 4. HOW IT WORKS (SIMPLE 3-STEP HIRING PROCESS) -->
<section class="py-24 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold text-[#FF6B00] uppercase tracking-wider block mb-2">Hiring Process</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-[#111318] tracking-tight mb-4">Fast-Track Application Steps</h2>
            <p class="text-sm text-gray-500">From application to your first shift in 3 easy steps.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8 relative">
            <!-- Step 1 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 relative space-y-4">
                <span class="w-12 h-12 rounded-2xl bg-[#FF6B00] text-white font-black text-xl flex items-center justify-center shadow-lg shadow-[#FF6B00]/20">1</span>
                <h3 class="font-extrabold text-xl text-[#111318]">Submit Online Application</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Fill out our quick form with your basic details, course of study, and contact number in under 2 minutes.</p>
            </div>

            <!-- Step 2 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 relative space-y-4">
                <span class="w-12 h-12 rounded-2xl bg-[#111318] text-[#FFD233] font-black text-xl flex items-center justify-center shadow-lg">2</span>
                <h3 class="font-extrabold text-xl text-[#111318]">Receive Real-time Status</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Get instant SMS and WhatsApp notifications as our recruitment team reviews your profile.</p>
            </div>

            <!-- Step 3 -->
            <div class="p-8 rounded-3xl bg-[#F8FAFC] border border-gray-200/80 relative space-y-4">
                <span class="w-12 h-12 rounded-2xl bg-[#FF6B00] text-white font-black text-xl flex items-center justify-center shadow-lg shadow-[#FF6B00]/20">3</span>
                <h3 class="font-extrabold text-xl text-[#111318]">Orientation & Onboarding</h3>
                <p class="text-xs text-gray-600 leading-relaxed">Attend a brief campus orientation, receive your gear, and start earning on your preferred schedule!</p>
            </div>
        </div>

    </div>
</section>

<!-- 5. TEAM TESTIMONIALS -->
<section class="py-24 bg-[#0D0F12] text-white relative overflow-hidden">
    <!-- Glowing background accent -->
    <div class="absolute w-[500px] h-[500px] bg-[#FF6B00]/15 rounded-full blur-[140px] top-0 right-0 pointer-events-none"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
        
        <div class="text-center max-w-3xl mx-auto mb-16">
            <span class="text-xs font-extrabold text-[#FFD233] uppercase tracking-wider block mb-2">Team Testimonials</span>
            <h2 class="text-3xl sm:text-4xl font-extrabold text-white tracking-tight mb-4">Hear From Our Campus Fleet</h2>
            <p class="text-sm text-gray-400">Discover how working with Munchify fits into campus life.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-md flex flex-col justify-between space-y-6">
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "Working as a Munchify Rider at Maseno has given me financial independence. The flexible shifts allow me to cover my expenses without missing a single lecture."
                </p>
                <div class="flex items-center gap-3.5 pt-4 border-t border-white/10">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-r from-[#FF6B00] to-[#E05D00] flex items-center justify-center font-extrabold text-white text-sm shadow-md">
                        EO
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-white">Evans Onyango</h4>
                        <span class="text-[11px] text-[#FFD233] font-semibold">Delivery Rider & Maseno Student</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-md flex flex-col justify-between space-y-6">
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "I joined the customer support team and developed vital communication skills. The team culture is incredibly supportive, energetic, and rewarding."
                </p>
                <div class="flex items-center gap-3.5 pt-4 border-t border-white/10">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-r from-amber-400 to-yellow-500 flex items-center justify-center font-extrabold text-gray-900 text-sm shadow-md">
                        MK
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-white">Mercy Koech</h4>
                        <span class="text-[11px] text-[#FFD233] font-semibold">Customer Support Lead</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-white/5 border border-white/10 p-8 rounded-3xl backdrop-blur-md flex flex-col justify-between space-y-6">
                <p class="text-xs text-gray-300 leading-relaxed italic">
                    "Munchify gave me the platform to apply software development in production environments. Building logistics tools for our campus has been an invaluable growth experience."
                </p>
                <div class="flex items-center gap-3.5 pt-4 border-t border-white/10">
                    <div class="w-11 h-11 rounded-full bg-gradient-to-r from-emerald-500 to-teal-600 flex items-center justify-center font-extrabold text-white text-sm shadow-md">
                        DN
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-white">Daniel Njuguna</h4>
                        <span class="text-[11px] text-[#FFD233] font-semibold">Full-Stack Developer</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
