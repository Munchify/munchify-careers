@extends('layouts.public')

@section('title', 'Munchify Careers - Join the Maseno Food Revolution')

@section('content')
<!-- 1. HERO SECTION -->
<section class="bg-[#111318] text-white py-20 md:py-32 relative overflow-hidden flex items-center">
    <!-- Glowing background lights -->
    <div class="absolute w-[600px] h-[600px] bg-[#FF6B00]/15 rounded-full blur-[120px] -top-80 -right-20"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#FFD233]/10 rounded-full blur-[120px] -bottom-40 -left-40"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">
        <span class="inline-flex items-center gap-1.5 bg-[#FF6B00]/10 border border-[#FF6B00]/30 rounded-full py-1.5 px-4 text-xs font-semibold text-[#FF6B00] mb-6 animate-fade-in">
            <span class="w-1.5 h-1.5 rounded-full bg-[#FF6B00] animate-pulse"></span> We're hiring for 2026 fleet expansion!
        </span>
        
        <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold tracking-tight leading-tight max-w-4xl mx-auto mb-6 text-white">
            Join the <span class="text-[#FF6B00]">Maseno University</span> Food Delivery Revolution
        </h1>
        
        <p class="text-base sm:text-lg text-gray-400 max-w-2xl mx-auto mb-10 leading-relaxed">
            Munchify connects hungry students and staff with their favorite hot meals instantly. Build your career with Kenya's fastest-growing campus delivery network.
        </p>

        <div class="flex flex-col sm:flex-row justify-center items-center gap-4">
            <a href="#open-positions" class="btn btn-primary btn-lg w-full sm:w-auto shadow-lg hover:shadow-[#FF6B00]/25 rounded-full">
                Explore Open Roles <i class="fa-solid fa-arrow-down"></i>
            </a>
            <a href="#" @click.prevent="openTrackModal()" class="btn btn-outline btn-lg w-full sm:w-auto rounded-full">
                Track Application <i class="fa-solid fa-location-arrow"></i>
            </a>
        </div>
    </div>
</section>

<!-- 2. WHY MUNCHIFY GRID -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-[#111318] mb-4">Why Munchify?</h2>
            <p class="text-sm text-gray-500 max-w-xl mx-auto">We do food delivery differently. Find out why our workspace feels like family.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Card 1 -->
            <div class="card card-hover p-8 flex flex-col gap-4 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-[#FF6B00]/10 flex items-center justify-center text-[#FF6B00] text-xl font-bold">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="font-extrabold text-lg text-[#111318]">Student Friendly</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Flexible hours tailored perfectly around your classes, exams, and university schedule.</p>
            </div>

            <!-- Card 2 -->
            <div class="card card-hover p-8 flex flex-col gap-4 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-[#FFD233]/15 flex items-center justify-center text-yellow-600 text-xl font-bold">
                    <i class="fa-solid fa-coins"></i>
                </div>
                <h3 class="font-extrabold text-lg text-[#111318]">Competitive Pay</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Earn top rates plus bonuses for performance, meal accuracy, and quick dispatch times.</p>
            </div>

            <!-- Card 3 -->
            <div class="card card-hover p-8 flex flex-col gap-4 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-[#FF6B00]/10 flex items-center justify-center text-[#FF6B00] text-xl font-bold">
                    <i class="fa-solid fa-face-smile"></i>
                </div>
                <h3 class="font-extrabold text-lg text-[#111318]">Great Culture</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Collaborate with fellow Maseno University peers in a warm, welcoming, and high-energy environment.</p>
            </div>

            <!-- Card 4 -->
            <div class="card card-hover p-8 flex flex-col gap-4 border border-gray-100">
                <div class="w-12 h-12 rounded-2xl bg-emerald-50 flex items-center justify-center text-emerald-600 text-xl font-bold">
                    <i class="fa-solid fa-rocket"></i>
                </div>
                <h3 class="font-extrabold text-lg text-[#111318]">Growth Path</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Start as a rider or support agent, and advance into supervisor, logisitics operations, or tech roles.</p>
            </div>
        </div>

    </div>
</section>

<!-- 3. OPEN POSITIONS -->
<section id="open-positions" class="py-20 bg-[#F7F8FA]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
                <h2 class="text-3xl font-extrabold text-[#111318] mb-2">Featured Opportunities</h2>
                <p class="text-sm text-gray-500">Pick a role and apply online in less than 5 minutes.</p>
            </div>
            <a href="{{ route('careers.jobs') }}" class="btn btn-secondary rounded-full py-2.5 px-6 font-bold text-xs">
                View All Openings <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>

        <!-- Featured Jobs List -->
        <div class="space-y-4">
            @forelse($latestJobs as $job)
                <div class="card bg-white p-6 md:p-8 flex flex-col md:flex-row justify-between items-start md:items-center gap-6 border-l-4 border-l-[#FF6B00] shadow-sm hover:shadow-md transition duration-300">
                    <div class="flex-grow">
                        <div class="flex flex-wrap items-center gap-2 mb-3">
                            <span class="badge badge-orange">{{ $job->department->name }}</span>
                            <span class="badge badge-gray">{{ $job->type_label }}</span>
                            <span class="badge badge-gray">{{ $job->location_label }}</span>
                        </div>
                        <h3 class="font-extrabold text-xl text-[#111318] hover:text-[#FF6B00] transition mb-2">
                            <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}">{{ $job->title }}</a>
                        </h3>
                        <div class="flex items-center gap-4 text-xs text-gray-500 font-semibold">
                            <span><i class="fa-solid fa-map-marker-alt"></i> {{ $job->location_detail }}</span>
                            @if($job->salary_range)
                            <span><i class="fa-solid fa-wallet"></i> {{ $job->salary_range }}</span>
                            @endif
                        </div>
                    </div>
                    <div class="flex items-center gap-3 w-full md:w-auto">
                        <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}" class="btn btn-secondary w-full md:w-auto px-6">
                            Details
                        </a>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn btn-primary w-full md:w-auto px-6">
                            Apply <i class="fa-solid fa-angle-right"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 card bg-white">
                    <p class="text-gray-500 font-semibold">No featured roles available at this time. Please check back later!</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- 4. LIFE AT MUNCHIFY (PHOTO/TESTIMONIAL GRID) -->
<section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center mb-16">
            <h2 class="text-3xl font-extrabold text-[#111318] mb-4">Voices of Munchify</h2>
            <p class="text-sm text-gray-500 max-w-xl mx-auto">Hear directly from the students and team members who make it all happen.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Testimonial 1 -->
            <div class="bg-[#F7F8FA] p-8 rounded-2xl border border-gray-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Being a student at Maseno and working part-time as a Munchify Rider has been a game-changer. I cover my expenses and still make it to all my morning lectures on time!"
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#FF6B00] flex items-center justify-center font-bold text-white uppercase">
                        EO
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-[#111318]">Evans Onyango</h4>
                        <span class="text-[10px] text-gray-400 font-semibold">Delivery Rider & Student</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 2 -->
            <div class="bg-[#F7F8FA] p-8 rounded-2xl border border-gray-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "I joined the customer care desk and have developed massive skills in resolution management. The atmosphere here is young, helpful, and highly energetic."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-[#FFD233] flex items-center justify-center font-bold text-yellow-800 uppercase">
                        MK
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-[#111318]">Mercy Koech</h4>
                        <span class="text-[10px] text-gray-400 font-semibold">Customer Support Desk</span>
                    </div>
                </div>
            </div>

            <!-- Testimonial 3 -->
            <div class="bg-[#F7F8FA] p-8 rounded-2xl border border-gray-100 flex flex-col justify-between">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Developing the order dashboard for Munchify has allowed me to apply Laravel in real production environments, solving actual logistical bottlenecks around Maseno."
                </p>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-emerald-500 flex items-center justify-center font-bold text-white uppercase">
                        DN
                    </div>
                    <div>
                        <h4 class="font-extrabold text-sm text-[#111318]">Daniel Njuguna</h4>
                        <span class="text-[10px] text-gray-400 font-semibold">Full-Stack Dev Lead</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
