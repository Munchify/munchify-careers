<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Recruiter Dashboard') | Munchify Careers</title>
    
    <!-- Google Fonts: Sora -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="{{ asset('favicon.png') }}">
    <link rel="apple-touch-icon" href="{{ asset('m-logo.png') }}">
    
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css'])
    @yield('styles')
</head>
<body class="bg-[#F7F8FA] font-sans antialiased text-[#111318] min-h-screen flex" x-data="{ sidebarOpen: false }">

    <!-- 1. LEFT SIDEBAR -->
    <aside class="w-64 bg-[#111318] text-white flex flex-col fixed h-screen z-40 transition-all duration-300 md:translate-x-0"
           :class="sidebarOpen ? 'translate-x-0' : '-translate-x-64'">
        
        <!-- Sidebar Logo Header -->
        <div class="p-5 border-b border-white/5 flex items-center justify-between">
            <a href="{{ route('dashboard.overview') }}" class="flex items-center gap-3">
                <img src="{{ asset('m-logo.png') }}" alt="Munchify Mark" class="w-8 h-8 rounded-lg object-contain">
                <div class="flex flex-col">
                    <span class="font-extrabold tracking-tight text-sm text-white">Munchify Recruiter</span>
                    <span class="text-[9px] text-[#FFD233] uppercase tracking-wider font-semibold">Workspace</span>
                </div>
            </a>
            
            <button class="md:hidden text-gray-400 hover:text-white" @click="sidebarOpen = false">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Sidebar Navigation List -->
        <nav class="flex-grow p-4 space-y-1.5 overflow-y-auto">
            <a href="{{ route('dashboard.overview') }}" class="sidebar-link {{ Request::routeIs('dashboard.overview') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-chart-pie w-5"></i> Overview
            </a>
            
            <a href="{{ route('jobs.manage') }}" class="sidebar-link {{ Request::routeIs('jobs.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-briefcase w-5"></i> Jobs
            </a>
            
            <a href="{{ route('applications.index') }}" class="sidebar-link {{ Request::routeIs('applications.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-user-group w-5"></i> Candidates
            </a>
            
            <a href="{{ route('interviews.index') }}" class="sidebar-link {{ Request::routeIs('interviews.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-calendar-days w-5"></i> Interviews
            </a>

            @if(Auth::user()->canManageJobs())
            <a href="{{ route('pipelines.index') }}" class="sidebar-link {{ Request::routeIs('pipelines.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-arrows-split-up-and-left w-5"></i> Pipelines
            </a>
            @endif

            @if(Auth::user()->hasRole('admin', 'hr_manager', 'hiring_manager'))
            <a href="{{ route('analytics.index') }}" class="sidebar-link {{ Request::routeIs('analytics.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-chart-line w-5"></i> Analytics
            </a>
            @endif

            @if(Auth::user()->canManageJobs())
            <a href="{{ route('settings.index') }}" class="sidebar-link {{ Request::routeIs('settings.*') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-sliders w-5"></i> Settings
            </a>
            @endif

            @if(Auth::user()->isAdmin())
            <a href="{{ route('audit.index') }}" class="sidebar-link {{ Request::routeIs('audit.index') ? 'sidebar-link-active' : '' }}">
                <i class="fa-solid fa-clock-rotate-left w-5"></i> Audit Logs
            </a>
            @endif
        </nav>

        <!-- Sidebar User Profile Info -->
        <div class="p-4 border-t border-white/5 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-white/10 border border-white/10 flex items-center justify-center font-bold text-sm text-[#FFD233] uppercase">
                    {{ Auth::user()->initials }}
                </div>
                <div class="flex flex-col min-w-0">
                    <span class="font-bold text-xs truncate leading-none mb-1">{{ Auth::user()->full_name }}</span>
                    <span class="text-[10px] text-gray-500 truncate capitalize">{{ Auth::user()->role_label }}</span>
                </div>
            </div>
            
            <!-- Log Out Action Form -->
            <form action="{{ route('logout') }}" method="POST" class="inline">
                @csrf
                <button type="submit" class="text-gray-500 hover:text-[#FF6B00] p-2 rounded-lg transition" title="Log Out">
                    <i class="fa-solid fa-right-from-bracket"></i>
                </button>
            </form>
        </div>
    </aside>

    <!-- 2. MAIN LAYOUT CONTAINER -->
    <div class="flex-grow md:pl-64 flex flex-col min-h-screen">
        
        <!-- TOP NAVBAR -->
        <header class="bg-white border-b border-gray-200 h-16 sticky top-0 z-30 flex items-center px-6 justify-between">
            <div class="flex items-center gap-4">
                <button class="md:hidden text-gray-500 hover:text-gray-700" @click="sidebarOpen = true">
                    <i class="fa-solid fa-bars text-lg"></i>
                </button>
                <h2 class="font-bold text-base md:text-lg text-[#111318]">@yield('header_title', 'Recruiter Dashboard')</h2>
            </div>

            <!-- Topbar right components -->
            <div class="flex items-center gap-4">
                <a href="{{ route('careers.home') }}" target="_blank" class="text-xs text-gray-500 hover:text-[#FF6B00] font-semibold flex items-center gap-1 border border-gray-200 rounded-full py-1.5 px-3">
                    <i class="fa-solid fa-globe"></i> Careers Site <i class="fa-solid fa-arrow-up-right-from-square text-[9px]"></i>
                </a>
            </div>
        </header>

        <!-- SESSION FLASH NOTIFICATIONS -->
        @if(session('success'))
        <div class="mx-6 mt-4 p-4 bg-emerald-50 border border-emerald-200 rounded-xl text-emerald-800 text-xs font-semibold flex items-center gap-2 shadow-sm animate-fade-in">
            <i class="fa-solid fa-circle-check text-base text-emerald-500"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        @if($errors->any())
        <div class="mx-6 mt-4 p-4 bg-red-50 border border-red-200 rounded-xl text-red-800 text-xs font-semibold flex flex-col gap-1 shadow-sm animate-fade-in">
            <div class="flex items-center gap-2">
                <i class="fa-solid fa-circle-exclamation text-base text-red-500"></i>
                <span class="font-bold">Please correct the following errors:</span>
            </div>
            <ul class="list-disc pl-7 space-y-0.5 mt-1 text-[11px]">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
        @endif

        <!-- MAIN PAGE VIEW CONTENT -->
        <main class="flex-grow p-6">
            @yield('content')
        </main>
    </div>

    @yield('scripts')
</body>
</html>
