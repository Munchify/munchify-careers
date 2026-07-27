@extends('layouts.public')

@section('title', 'Munchify Careers - Join Maseno Campus Food Operations')

@section('content')
<!-- HERO SECTION -->
<section class="bg-white py-16 md:py-24 border-b border-gray-100">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="space-y-6 max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 bg-[#FFF7ED] border border-[#FFEDD5] rounded-full py-1.5 px-4 text-xs font-bold text-[#FF6B00]">
                <span class="w-2 h-2 rounded-full bg-[#FF6B00] animate-pulse"></span>
                <span>Maseno University Hiring Drive 2026</span>
            </div>

            <h1 class="text-4xl sm:text-5xl lg:text-6xl font-black text-[#111318] tracking-tight leading-[1.15]">
                Earn, Learn & Grow with <span class="text-[#FF6B00]">Munchify Campus Fleet</span>
            </h1>

            <p class="text-base sm:text-lg text-gray-600 leading-relaxed font-normal">
                Join Kenya’s premier university food delivery platform. Flexible shifts designed around your lecture timetable, weekly M-Pesa payouts, and real leadership growth.
            </p>

            <!-- Actions -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-2">
                <a href="#open-positions" class="btn btn-primary btn-lg w-full sm:w-auto font-bold px-8 shadow-lg shadow-[#FF6B00]/20">
                    View Open Roles <i class="fa-solid fa-arrow-down ml-1"></i>
                </a>
                <a href="#" @click.prevent="openTrackModal()" class="btn btn-secondary btn-lg w-full sm:w-auto font-bold px-8">
                    Track My Application <i class="fa-solid fa-location-crosshairs ml-1 text-[#FF6B00]"></i>
                </a>
            </div>

            <!-- Trust signals strip -->
            <div class="pt-10 mt-10 border-t border-gray-100 grid grid-cols-3 gap-6 max-w-2xl mx-auto">
                <div>
                    <span class="block text-2xl font-extrabold text-[#111318]">10,000+</span>
                    <span class="text-xs text-gray-500 font-medium">Orders Delivered</span>
                </div>
                <div>
                    <span class="block text-2xl font-extrabold text-[#FF6B00]">Flexible</span>
                    <span class="text-xs text-gray-500 font-medium">Student Shifts</span>
                </div>
                <div>
                    <span class="block text-2xl font-extrabold text-[#111318]">Weekly</span>
                    <span class="text-xs text-gray-500 font-medium">M-Pesa Payouts</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- WHY MUNCHIFY SECTION -->
<section class="py-20 bg-[#F9FAFB]">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-bold text-[#FF6B00] uppercase tracking-wider block mb-2">Campus Benefits</span>
            <h2 class="text-3xl font-extrabold text-[#111318] tracking-tight">Why Join Munchify?</h2>
            <p class="text-sm text-gray-500 mt-2">Built specifically to empower university students with real work experience and reliable income.</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
            <!-- Card 1 -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:border-[#FF6B00] transition">
                <div class="w-12 h-12 rounded-xl bg-[#FFF7ED] text-[#FF6B00] flex items-center justify-center text-xl font-bold mb-5">
                    <i class="fa-solid fa-graduation-cap"></i>
                </div>
                <h3 class="font-extrabold text-base text-[#111318] mb-2">Student Timetables</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Choose shifts that easily fit around your lectures, practicals, and exam dates.</p>
            </div>

            <!-- Card 2 -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:border-[#FF6B00] transition">
                <div class="w-12 h-12 rounded-xl bg-[#FEF3C7] text-amber-600 flex items-center justify-center text-xl font-bold mb-5">
                    <i class="fa-solid fa-[#FF6B00] fa-money-bill-wave"></i>
                </div>
                <h3 class="font-extrabold text-base text-[#111318] mb-2">Competitive Pay</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Earn per delivery plus dispatch bonuses and weekly M-Pesa payouts.</p>
            </div>

            <!-- Card 3 -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:border-[#FF6B00] transition">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-xl font-bold mb-5">
                    <i class="fa-solid fa-[#FF6B00] fa-users"></i>
                </div>
                <h3 class="font-extrabold text-base text-[#111318] mb-2">Campus Community</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Work with friendly Maseno peers in a supportive, high-energy environment.</p>
            </div>

            <!-- Card 4 -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm hover:border-[#FF6B00] transition">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-xl font-bold mb-5">
                    <i class="fa-solid fa-chart-line"></i>
                </div>
                <h3 class="font-extrabold text-base text-[#111318] mb-2">Career Promotion</h3>
                <p class="text-xs text-gray-500 leading-relaxed">Advance from rider or agent into supervisor, logistics, or developer roles.</p>
            </div>
        </div>

    </div>
</section>

<!-- OPEN POSITIONS SECTION -->
<section id="open-positions" class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="flex flex-col md:flex-row justify-between items-start md:items-end mb-12 gap-4">
            <div>
                <span class="text-xs font-bold text-[#FF6B00] uppercase tracking-wider block mb-2">Current Hiring</span>
                <h2 class="text-3xl font-extrabold text-[#111318] tracking-tight">Open Job Vacancies</h2>
                <p class="text-sm text-gray-500 mt-1">Select a role below to start your online application.</p>
            </div>
            <a href="{{ route('careers.jobs') }}" class="btn btn-secondary rounded-full py-2.5 px-6 font-bold text-xs">
                View All Openings <i class="fa-solid fa-arrow-right ml-1"></i>
            </a>
        </div>

        <!-- Job Cards List -->
        <div class="space-y-4">
            @forelse($latestJobs as $job)
                <div class="bg-white rounded-2xl p-6 border border-gray-200 shadow-sm hover:border-[#FF6B00] transition flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
                    <div class="space-y-2">
                        <div class="flex flex-wrap items-center gap-2">
                            <span class="badge bg-[#FFF7ED] text-[#FF6B00] border border-[#FFEDD5] text-[11px] font-bold">{{ $job->department->name }}</span>
                            <span class="badge badge-gray text-[11px] font-semibold">{{ $job->type_label }}</span>
                            <span class="badge badge-gray text-[11px] font-semibold">{{ $job->location_label }}</span>
                        </div>

                        <h3 class="font-extrabold text-xl text-[#111318] hover:text-[#FF6B00] transition">
                            <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}">{{ $job->title }}</a>
                        </h3>

                        <div class="flex flex-wrap items-center gap-6 text-xs text-gray-500 font-semibold pt-1">
                            <span><i class="fa-solid fa-location-dot text-[#FF6B00] mr-1.5"></i> {{ $job->location_detail ?: 'Maseno Main Campus' }}</span>
                            <span><i class="fa-solid fa-user-group text-[#FF6B00] mr-1.5"></i> {{ $job->slots }} {{ Str::plural('Vacancy', $job->slots) }}</span>
                            @if($job->application_deadline)
                            <span><i class="fa-solid fa-clock text-[#FF6B00] mr-1.5"></i> Deadline: {{ $job->application_deadline->format('M d, Y') }}</span>
                            @endif
                        </div>
                    </div>

                    <div class="flex items-center gap-3 w-full md:w-auto shrink-0">
                        <a href="{{ route('careers.jobs.show', ['ulid' => $job->ulid]) }}" class="btn btn-secondary w-full md:w-auto px-6 py-2.5 rounded-full text-xs font-bold">
                            Details
                        </a>
                        <a href="{{ route('apply.step', ['ulid' => $job->ulid, 'step' => 1]) }}" class="btn btn-primary w-full md:w-auto px-6 py-2.5 rounded-full text-xs font-bold">
                            Apply <i class="fa-solid fa-angle-right ml-1"></i>
                        </a>
                    </div>
                </div>
            @empty
                <div class="text-center py-12 bg-[#F9FAFB] rounded-2xl border border-gray-200">
                    <p class="text-gray-500 text-sm font-semibold">No open vacancies at this moment. Check back soon!</p>
                </div>
            @endforelse
        </div>

    </div>
</section>

<!-- TESTIMONIALS SECTION -->
<section class="py-20 bg-[#F9FAFB] border-t border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        
        <div class="text-center max-w-2xl mx-auto mb-14">
            <span class="text-xs font-bold text-[#FF6B00] uppercase tracking-wider block mb-2">Team Leadership & Voices</span>
            <h2 class="text-3xl font-extrabold text-[#111318] tracking-tight">Meet the Team Behind Munchify</h2>
            <p class="text-sm text-gray-500 mt-2">Hear directly from the leadership and fleet members driving operations at Maseno Campus.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <!-- Review 1: Dan -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:border-[#FF6B00] transition">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Our dispatch strategy centers around speed, order safety, and absolute customer delight across every corner of Maseno Campus."
                </p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-[#FF6B00] text-white flex items-center justify-center font-extrabold text-xs">
                        D
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318]">Dan</h4>
                        <span class="text-[10px] text-gray-500 font-semibold block">Head of Delivery</span>
                    </div>
                </div>
            </div>

            <!-- Review 2: Cliff -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:border-[#FF6B00] transition">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "We build seamless operational workflows linking kitchen partners directly with our fleet to guarantee instant dispatch times."
                </p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-[#111318] text-[#FFD233] flex items-center justify-center font-extrabold text-xs">
                        C
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318]">Cliff</h4>
                        <span class="text-[10px] text-gray-500 font-semibold block">Head of Operations</span>
                    </div>
                </div>
            </div>

            <!-- Review 3: Potmo -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:border-[#FF6B00] transition">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Financial transparency and guaranteed weekly payouts empower our team members to focus on growth and excellent service."
                </p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-emerald-600 text-white flex items-center justify-center font-extrabold text-xs">
                        P
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318]">Potmo</h4>
                        <span class="text-[10px] text-gray-500 font-semibold block">Head of Finance</span>
                    </div>
                </div>
            </div>

            <!-- Review 4: Jesee -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:border-[#FF6B00] transition">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Engineering intelligent recruitment algorithms and real-time tracking dashboards ensures seamless experience for candidates and recruiters."
                </p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-blue-600 text-white flex items-center justify-center font-extrabold text-xs">
                        J
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318]">Jesee</h4>
                        <span class="text-[10px] text-gray-500 font-semibold block">Head of IT</span>
                    </div>
                </div>
            </div>

            <!-- Review 5: Derick -->
            <div class="bg-white p-7 rounded-2xl border border-gray-200 shadow-sm flex flex-col justify-between hover:border-[#FF6B00] transition">
                <p class="text-xs text-gray-600 leading-relaxed italic mb-6">
                    "Riding with Munchify gives me total flexibility around my schedule, dependable income, and a team that always has my back."
                </p>
                <div class="flex items-center gap-3 pt-4 border-t border-gray-100">
                    <div class="w-10 h-10 rounded-full bg-[#FEF3C7] text-amber-800 flex items-center justify-center font-extrabold text-xs">
                        D
                    </div>
                    <div>
                        <h4 class="font-extrabold text-xs text-[#111318]">Derick</h4>
                        <span class="text-[10px] text-gray-500 font-semibold block">Delivery Driver</span>
                    </div>
                </div>
            </div>
        </div>

    </div>
</section>
@endsection
