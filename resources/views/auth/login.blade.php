<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Team Login | Munchify Careers</title>
    
    <!-- Google Fonts: Sora -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Sora:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    @vite(['resources/css/app.css'])
</head>
<body class="bg-[#111318] font-sans antialiased text-white min-h-screen flex items-center justify-center relative overflow-hidden">

    <!-- Brand glowing gradients in background -->
    <div class="absolute w-[500px] h-[500px] bg-[#FF6B00]/10 rounded-full blur-[100px] -top-40 -left-40"></div>
    <div class="absolute w-[500px] h-[500px] bg-[#FFD233]/5 rounded-full blur-[100px] -bottom-40 -right-40"></div>

    <div class="w-full max-w-md p-6 relative z-10 animate-scale-in">
        
        <!-- Logo -->
        <div class="flex flex-col items-center mb-8">
            <a href="{{ route('careers.home') }}" class="flex items-center gap-2 group mb-2">
                <div class="w-12 h-12 rounded-xl bg-[#FF6B00] flex items-center justify-center text-white font-black text-2xl shadow-[0_0_20px_rgba(255,107,0,0.4)] transition duration-300 group-hover:scale-105">
                    M
                </div>
                <div class="flex flex-col text-left">
                    <span class="font-extrabold tracking-tight text-xl leading-none">Munchify</span>
                    <span class="text-[10px] text-[#FFD233] uppercase tracking-wider font-semibold">Careers Portal</span>
                </div>
            </a>
            <p class="text-xs text-gray-400">Internal recruitment dashboard</p>
        </div>

        <!-- Card Container -->
        <div class="bg-[#1A1D24] border border-white/5 p-8 rounded-2xl shadow-[0_20px_50px_rgba(0,0,0,0.3)]">
            
            <h2 class="text-lg font-bold mb-6 text-center text-white flex items-center justify-center gap-2">
                <i class="fa-solid fa-lock text-[#FFD233]"></i> Recruiter Login
            </h2>

            <!-- Errors alert box -->
            @if ($errors->any())
            <div class="p-3.5 bg-red-950/40 border border-red-500/20 rounded-xl text-red-400 text-xs font-semibold mb-6 flex gap-2">
                <i class="fa-solid fa-circle-exclamation text-base text-red-500 mt-0.5"></i>
                <div class="flex flex-col">
                    <span class="font-bold">Login failed:</span>
                    <ul class="list-disc pl-4 mt-1 space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
            @endif

            <form action="{{ route('login') }}" method="POST" class="space-y-5">
                @csrf
                
                <div>
                    <label for="email" class="form-label text-gray-300">Email Address</label>
                    <input type="email" name="email" id="email" 
                           class="form-input bg-[#111318] border-white/10 text-white focus:border-[#FF6B00] focus:ring-[#FF6B00]/10 placeholder:text-gray-600" 
                           placeholder="e.g. hr@munchify.com" value="{{ old('email') }}" required autocomplete="email" autofocus>
                </div>

                <div>
                    <div class="flex justify-between items-center mb-1">
                        <label for="password" class="form-label text-gray-300 mb-0">Password</label>
                    </div>
                    <input type="password" name="password" id="password" 
                           class="form-input bg-[#111318] border-white/10 text-white focus:border-[#FF6B00] focus:ring-[#FF6B00]/10 placeholder:text-gray-600" 
                           placeholder="••••••••" required autocomplete="current-password">
                </div>

                <!-- Remember me -->
                <div class="flex items-center justify-between">
                    <label class="flex items-center text-xs text-gray-400 cursor-pointer">
                        <input type="checkbox" name="remember" class="rounded border-white/10 text-[#FF6B00] focus:ring-0 focus:ring-offset-0 bg-[#111318] mr-2">
                        Keep me signed in
                    </label>
                </div>

                <button type="submit" class="btn btn-primary w-full py-3">
                    <i class="fa-solid fa-right-to-bracket"></i> Sign In to Dashboard
                </button>
            </form>
        </div>

        <!-- Back to site Link -->
        <div class="text-center mt-6">
            <a href="{{ route('careers.home') }}" class="text-xs text-gray-500 hover:text-[#FF6B00] transition flex items-center justify-center gap-1.5 font-semibold">
                <i class="fa-solid fa-arrow-left"></i> Back to Careers Website
            </a>
        </div>

    </div>
</body>
</html>
