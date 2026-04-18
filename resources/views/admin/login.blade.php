<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Login — {{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Fira+Code:wght@400;500&display=swap" rel="stylesheet">
    
    <!-- Icons -->
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">

    <!-- Scripts and Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="antialiased bg-dark-950 text-dark-200 selection:bg-neon-500/20 selection:text-neon-400">
    <div class="min-h-screen flex flex-col items-center justify-center p-6 relative overflow-hidden">
        <!-- Background Grid Decoration -->
        <div class="absolute inset-0 bg-cyber-grid bg-[length:32px_32px] pointer-events-none opacity-40"></div>
        
        <!-- Ambient Blobs -->
        <div class="absolute top-[-10%] left-[-10%] w-[40%] h-[40%] bg-neon-500/10 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute bottom-[10%] right-[-5%] w-[30%] h-[30%] bg-emerald-600/10 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="w-full max-w-md relative z-10 animate-fade-up">
            {{-- Logo --}}
            <div class="flex flex-col items-center gap-6 mb-12">
                <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                    <div class="w-12 h-12 rounded-2xl bg-neon-500 flex items-center justify-center text-dark-950 shadow-neon-lg group-hover:scale-110 transition-transform">
                        <i class="ri-code-s-slash-line text-2xl font-bold"></i>
                    </div>
                    <span class="font-display font-black text-dark-100 text-3xl tracking-tighter">Patrick<span class="text-neon-500">.cms</span></span>
                </a>
                <div class="text-center space-y-2">
                    <h1 class="text-xl font-bold text-dark-100 tracking-tight">System Access</h1>
                    <p class="text-dark-500 text-xs font-bold uppercase tracking-[0.2em]">Enter credentials to continue</p>
                </div>
            </div>

            <x-card padding="p-8" class="shadow-neon-lg border-dark-800/50">
                <form action="{{ route('admin.login.store') }}" method="POST" class="space-y-6">
                    @csrf
                    
                    @if(session('error'))
                        <div class="p-3 rounded-xl bg-red-500/10 border border-red-500/20 text-red-400 text-xs font-bold flex items-center gap-3">
                            <i class="ri-error-warning-line text-lg"></i>
                            <span>{{ session('error') }}</span>
                        </div>
                    @endif

                    <x-input label="Email Address" name="email" type="email" placeholder="admin@example.com" required="true" :error="$errors->first('email')" />
                    
                    <div class="space-y-2">
                        <div class="flex items-center justify-between px-1">
                            <label class="block text-xs font-semibold text-dark-400 uppercase tracking-widest">Password</label>
                            <a href="#" class="text-[10px] font-black uppercase tracking-widest text-dark-600 hover:text-neon-400 transition-colors">Forgot?</a>
                        </div>
                        <x-input name="password" type="password" placeholder="••••••••" required="true" :error="$errors->first('password')" />
                    </div>

                    <div class="flex items-center gap-3 px-1">
                        <input type="checkbox" name="remember" id="remember" class="w-4 h-4 rounded bg-dark-800 border-dark-700 text-neon-500 focus:ring-neon-500/20 focus:ring-offset-dark-950">
                        <label for="remember" class="text-xs text-dark-500 font-medium">Keep me logged in</label>
                    </div>

                    <x-button variant="neon" size="lg" type="submit" class="w-full">
                        <i class="ri-login-box-line"></i> Sign In to Dashboard
                    </x-button>
                </form>
            </x-card>

            <p class="text-center mt-8 text-dark-600 text-[10px] font-black uppercase tracking-[0.2em]">
                &copy; {{ date('Y') }} Patrick CMS — All Systems Operational
            </p>
        </div>
    </div>
</body>
</html>
