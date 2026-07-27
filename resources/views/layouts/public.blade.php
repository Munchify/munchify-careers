<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Munchify Careers') | Munchify</title>
    
    <!-- Google Fonts: Sora -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css'])
    @yield('styles')
</head>
<body class="bg-[#F7F8FA] font-sans antialiased text-[#111318] min-h-screen flex flex-col">

    <!-- Header Navbar -->
    <header class="bg-[#111318] text-white py-4 sticky top-0 z-50 border-b border-white/10 shadow-lg" x-data="{ mobileMenu: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center">
            
            <!-- Logo -->
            <a href="{{ route('careers.home') }}" class="flex items-center gap-2 group">
                <div class="w-10 h-10 rounded-xl bg-[#FF6B00] flex items-center justify-center text-white font-extrabold text-xl shadow-[0_0_15px_rgba(255,107,0,0.5)] transition duration-300 group-hover:scale-105">
                    M
                </div>
                <div class="flex flex-col">
                    <span class="font-extrabold tracking-tight text-lg leading-none">Munchify</span>
                    <span class="text-[10px] text-[#FFD233] uppercase tracking-wider font-semibold">Careers Portal</span>
                </div>
            </a>

            <!-- Desktop Nav Links -->
            <nav class="hidden md:flex items-center gap-8 font-semibold text-sm">
                <a href="{{ route('careers.home') }}" class="hover:text-[#FF6B00] transition {{ Request::routeIs('careers.home') ? 'text-[#FF6B00]' : 'text-gray-300' }}">Home</a>
                <a href="{{ route('careers.jobs') }}" class="hover:text-[#FF6B00] transition {{ Request::routeIs('careers.jobs') ? 'text-[#FF6B00]' : 'text-gray-300' }}">Browse Jobs</a>
                
                <a href="#" @click.prevent="openTrackModal()" class="hover:text-[#FF6B00] text-gray-300 transition flex items-center gap-1">
                    <i class="fa-solid fa-magnifying-glass text-xs"></i> Track Application
                </a>

                <a href="{{ route('login') }}" class="btn btn-secondary btn-sm rounded-full py-2 px-5 text-xs">
                    <i class="fa-solid fa-lock text-[10px]"></i> Team Login
                </a>
            </nav>

            <!-- Mobile Hamburger -->
            <button class="md:hidden text-gray-300 hover:text-white p-2" @click="mobileMenu = !mobileMenu">
                <i class="fa-solid" :class="mobileMenu ? 'fa-xmark text-xl' : 'fa-bars text-xl'"></i>
            </button>
        </div>

        <!-- Mobile Menu -->
        <div class="md:hidden bg-[#1A1D24] border-t border-white/5 py-4 px-4 space-y-3 font-semibold text-sm" 
             x-show="mobileMenu" 
             x-transition:enter="transition ease-out duration-150"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-show.transition="mobileMenu"
             style="display: none;">
            <a href="{{ route('careers.home') }}" class="block py-2 text-gray-300 hover:text-[#FF6B00]">Home</a>
            <a href="{{ route('careers.jobs') }}" class="block py-2 text-gray-300 hover:text-[#FF6B00]">Browse Jobs</a>
            <a href="#" @click.prevent="openTrackModal(); mobileMenu = false" class="block py-2 text-gray-300 hover:text-[#FF6B00]">Track Application</a>
            <hr class="border-white/5 my-2">
            <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-full rounded-xl py-2.5">
                <i class="fa-solid fa-lock text-xs"></i> Team Login
            </a>
        </div>
    </header>

    <!-- Main Content Area -->
    <main class="flex-grow">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-[#111318] text-white py-12 mt-auto border-t border-white/5">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-4 gap-8">
            <div class="col-span-1 md:col-span-2">
                <a href="{{ route('careers.home') }}" class="flex items-center gap-2 mb-4">
                    <div class="w-8 h-8 rounded-lg bg-[#FF6B00] flex items-center justify-center text-white font-black text-lg">
                        M
                    </div>
                    <span class="font-extrabold text-lg tracking-tight">Munchify Careers</span>
                </a>
                <p class="text-xs text-gray-400 max-w-sm leading-relaxed">
                    Maseno University's premiere student food delivery service. We connect students with the best local meals, delivered fast, warm, and with a student-friendly budget. Join our team and make campus life delicious!
                </p>
            </div>
            <div>
                <h4 class="font-bold text-sm text-[#FFD233] mb-4">Quick Links</h4>
                <ul class="space-y-2 text-xs text-gray-400">
                    <li><a href="{{ route('careers.jobs') }}" class="hover:text-[#FF6B00] transition">Search Open Roles</a></li>
                    <li><a href="#" @click.prevent="openTrackModal()" class="hover:text-[#FF6B00] transition">Track Application</a></li>
                    <li><a href="{{ route('login') }}" class="hover:text-[#FF6B00] transition">Recruiter Portal</a></li>
                </ul>
            </div>
            <div>
                <h4 class="font-bold text-sm text-[#FFD233] mb-4">Contact US</h4>
                <p class="text-xs text-gray-400 leading-relaxed">
                    Maseno University, Main Campus<br>
                    Kisumu-Busia Road, Kenya<br>
                    <span class="block mt-2 font-semibold text-white">careers@munchify.co.ke</span>
                </p>
            </div>
        </div>
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 border-t border-white/5 mt-10 pt-6 text-center text-xs text-gray-500 flex flex-col md:flex-row justify-between gap-4">
            <p>&copy; {{ date('Y') }} Munchify App. All rights reserved.</p>
            <p class="flex gap-4 justify-center">
                <a href="#" class="hover:text-gray-300">Privacy Policy</a>
                <a href="#" class="hover:text-gray-300">Terms of Service</a>
            </p>
        </div>
    </footer>

    <!-- Global Track Application Modal (Alpine-driven) -->
    <div x-data="trackModalData()" 
         x-show="isOpen" 
         class="modal-overlay" 
         x-transition
         style="display: none;"
         @keydown.escape.window="close()">
        <div class="modal-content animate-scale-in max-w-md p-6" @click.outside="close()">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-bold flex items-center gap-2">
                    <i class="fa-solid fa-location-crosshairs text-[#FF6B00]"></i> Track Application
                </h3>
                <button @click="close()" class="text-gray-400 hover:text-gray-600">
                    <i class="fa-solid fa-xmark text-lg"></i>
                </button>
            </div>
            
            <p class="text-xs text-gray-500 mb-4">
                Enter the Application Tracking ID (ULID) provided in your SMS or WhatsApp confirmation message (e.g. <span class="font-mono text-gray-700">01hzqj...</span>).
            </p>

            <form @submit.prevent="track()">
                <div class="mb-4">
                    <label for="track_ulid" class="form-label">Application ULID</label>
                    <input type="text" x-model="ulid" id="track_ulid" class="form-input text-center font-mono placeholder:font-sans" placeholder="e.g. 01h9y6abc123xyz..." required>
                    <span x-show="error" class="form-error text-xs text-red-500 mt-1 block" x-text="error" style="display: none;"></span>
                </div>
                
                <button type="submit" class="btn btn-primary w-full py-3">
                    <i class="fa-solid fa-magnifying-glass"></i> Track Status
                </button>
            </form>
        </div>
    </div>

    <script>
        // Alpine data for tracking modal
        function trackModalData() {
            return {
                isOpen: false,
                ulid: '',
                error: '',
                init() {
                    window.openTrackModal = () => {
                        this.isOpen = true;
                        this.ulid = '';
                        this.error = '';
                    };
                },
                close() {
                    this.isOpen = false;
                },
                track() {
                    if (!this.ulid.trim()) {
                        this.error = 'Please enter a valid tracking ID.';
                        return;
                    }
                    // Redirect to status page
                    window.location.href = '/application/' + this.ulid.trim().toLowerCase() + '/status';
                }
            }
        }
    </script>
    @yield('scripts')
</body>
</html>
