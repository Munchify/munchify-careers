@extends('layouts.public')

@section('title', 'Application Submitted Successfully | Munchify Careers')

@section('content')
<section class="py-16 max-w-2xl mx-auto px-4 sm:px-6">
    
    <div class="bg-white rounded-3xl border border-gray-200 shadow-xl overflow-hidden p-8 md:p-12 text-center animate-scale-in">
        
        <!-- Big Green Success Ring -->
        <div class="w-20 h-20 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center text-3xl mx-auto mb-8 border border-emerald-100 shadow-[0_0_20px_rgba(34,197,94,0.15)]">
            <i class="fa-solid fa-circle-check"></i>
        </div>

        <h1 class="text-2xl sm:text-3xl font-extrabold text-[#111318] mb-4">
            Application Submitted!
        </h1>
        
        <p class="text-xs text-gray-500 max-w-md mx-auto mb-8 leading-relaxed">
            Thank you for applying to join the Munchify team. We have received your profile details and our recruitment committee will review them shortly.
        </p>

        <!-- Stats Container -->
        <div class="bg-[#F7F8FA] border border-gray-200 rounded-2xl p-6 mb-8 text-left max-w-md mx-auto space-y-4">
            
            <div class="flex justify-between items-center border-b border-gray-200/60 pb-3">
                <span class="text-xs text-gray-400 font-medium">Applied Position</span>
                <span class="text-xs font-bold text-[#111318]">{{ $job->title }}</span>
            </div>

            <div class="flex justify-between items-center border-b border-gray-200/60 pb-3">
                <span class="text-xs text-gray-400 font-medium">Application Number</span>
                <span class="app-number font-bold">{{ session('application_number', 'MUN-APP-XXXX') }}</span>
            </div>

            <div class="flex justify-between items-center">
                <span class="text-xs text-gray-400 font-medium">Initial Status</span>
                <span class="badge badge-orange">Applied / Screening</span>
            </div>
        </div>

        <!-- Next Step Instructions -->
        <div class="text-left max-w-md mx-auto mb-10 space-y-3">
            <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5"><i class="fa-solid fa-clock-rotate-left text-[#FF6B00]"></i> What happens next?</h3>
            <ol class="list-decimal pl-5 text-xs text-gray-500 space-y-2 leading-relaxed">
                <li>We have sent a confirmation message to your phone number via <span class="font-semibold text-gray-700">WhatsApp & SMS</span>.</li>
                <li>You can track the progress of your review process in real-time by visiting the tracker link below.</li>
                <li>If you are selected for an interview, a calendar slot scheduling invitation will be sent to you.</li>
            </ol>
        </div>

        <!-- Call to action buttons -->
        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
            @if(session('status_url'))
                <a href="{{ session('status_url') }}" class="btn btn-primary w-full sm:w-auto px-8 py-3.5 shadow-lg shadow-[#FF6B00]/25 rounded-full font-bold">
                    <i class="fa-solid fa-location-crosshairs"></i> Track Application Status
                </a>
            @endif
            <a href="{{ route('careers.home') }}" class="btn btn-secondary w-full sm:w-auto px-8 py-3.5 rounded-full font-semibold">
                Back to Careers Home
            </a>
        </div>

    </div>
</section>
@endsection
