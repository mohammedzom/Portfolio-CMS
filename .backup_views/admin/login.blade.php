<!DOCTYPE html>
<html lang="en" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="Admin Login — Portfolio CMS">
    <link href="https://cdn.jsdelivr.net/npm/remixicon@4.5.0/fonts/remixicon.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <title>Admin Login — Portfolio CMS</title>
    <style>
        /* Animated background blobs */
        .blob {
            position: absolute;
            border-radius: 50%;
            filter: blur(80px);
            pointer-events: none;
        }

        /* Floating card animation */
        @keyframes float-slow {

            0%,
            100% {
                transform: translateY(0px) rotate(0deg);
            }

            33% {
                transform: translateY(-8px) rotate(1deg);
            }

            66% {
                transform: translateY(-4px) rotate(-1deg);
            }
        }

        .animate-float-slow {
            animation: float-slow 8s ease-in-out infinite;
        }

        /* Grid texture */
        .grid-texture {
            background-image:
                linear-gradient(oklch(0.80 0.004 255) 1px, transparent 1px),
                linear-gradient(90deg, oklch(0.80 0.004 255) 1px, transparent 1px);
            background-size: 48px 48px;
        }

        /* Input wrapper */
        .input-wrapper {
            display: flex;
            align-items: center;
            background: oklch(0.16 0.015 255);
            border: 1px solid oklch(0.25 0.012 255);
            border-radius: 0.75rem;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .input-wrapper:focus-within {
            border-color: oklch(0.66 0.17 195 / 0.6);
            box-shadow: 0 0 0 3px oklch(0.66 0.17 195 / 0.12);
        }
        .input-wrapper.is-error {
            border-color: oklch(0.65 0.20 25 / 0.7);
            box-shadow: 0 0 0 3px oklch(0.65 0.20 25 / 0.10);
        }
        .input-wrapper-icon {
            padding: 0 0.625rem 0 0.875rem;
            font-size: 1rem;
            color: oklch(0.35 0.010 255);
            pointer-events: none;
            flex-shrink: 0;
            line-height: 1;
        }
        .input-wrapper-btn {
            padding: 0 0.875rem 0 0.5rem;
            font-size: 1rem;
            color: oklch(0.35 0.010 255);
            cursor: pointer;
            flex-shrink: 0;
            line-height: 1;
            transition: color 0.2s ease;
            background: none;
            border: none;
            outline: none;
        }
        .input-wrapper-btn:hover { color: oklch(0.66 0.17 195); }
        .input-field {
            flex: 1;
            background: transparent;
            border: none;
            outline: none;
            padding: 0.75rem 0.875rem 0.75rem 0;
            color: oklch(0.93 0.002 255);
            font-size: 0.875rem;
            width: 100%;
            min-width: 0;
        }
        .input-field::placeholder { color: oklch(0.35 0.010 255); }
    </style>
</head>

<body class="antialiased min-h-screen flex items-center justify-center relative overflow-hidden"
    style="background-color: oklch(0.10 0.01 255);">

    {{-- Background grid texture --}}
    <div class="absolute inset-0 opacity-[0.025] grid-texture"></div>

    {{-- Ambient blobs --}}
    <div class="blob" style="width:500px;height:500px;background:oklch(0.66 0.17 195 / 0.10);top:-150px;left:-120px;">
    </div>
    <div class="blob"
        style="width:350px;height:350px;background:oklch(0.60 0.15 220 / 0.08);bottom:-80px;right:-80px;"></div>
    <div class="blob" style="width:280px;height:280px;background:oklch(0.66 0.17 195 / 0.06);top:40%;left:60%;"></div>

    {{-- Decorative floating card (left) --}}
    <div class="absolute left-8 top-1/4 hidden xl:flex flex-col gap-3 animate-float-slow opacity-60">
        <div class="glass neon-border rounded-2xl p-4 w-48">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-lg gradient-neon flex items-center justify-center">
                    <i class="ri-folder-4-line text-dark-950 text-xs"></i>
                </div>
                <span class="text-dark-300 text-xs font-medium">Projects</span>
            </div>
            <p class="text-neon-400 font-display font-bold text-2xl">12</p>
            <p class="text-dark-500 text-xs">Active</p>
        </div>
        <div class="glass neon-border rounded-2xl p-4 w-48">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-lg bg-neon-500/10 flex items-center justify-center">
                    <i class="ri-mail-line text-neon-500 text-xs"></i>
                </div>
                <span class="text-dark-300 text-xs font-medium">Messages</span>
            </div>
            <p class="text-neon-400 font-display font-bold text-2xl">3</p>
            <p class="text-dark-500 text-xs">Unread</p>
        </div>
    </div>

    {{-- Decorative floating card (right) --}}
    <div class="absolute right-8 top-1/3 hidden xl:flex flex-col gap-3 animate-float-slow opacity-60"
        style="animation-delay: 2s;">
        <div class="glass neon-border rounded-2xl p-4 w-44">
            <div class="flex items-center gap-2 mb-2">
                <div class="w-6 h-6 rounded-lg gradient-neon flex items-center justify-center">
                    <i class="ri-bar-chart-2-line text-dark-950 text-xs"></i>
                </div>
                <span class="text-dark-300 text-xs font-medium">Skills</span>
            </div>
            <p class="text-neon-400 font-display font-bold text-2xl">18</p>
            <p class="text-dark-500 text-xs">Technologies</p>
        </div>
        <div class="glass neon-border rounded-2xl p-4 w-44">
            <div class="flex items-center gap-2 mb-2">
                <span class="w-2 h-2 rounded-full bg-neon-500 animate-pulse"></span>
                <span class="text-dark-300 text-xs font-medium">Status</span>
            </div>
            <p class="text-neon-400 text-sm font-semibold">Online</p>
            <p class="text-dark-500 text-xs">Portfolio live</p>
        </div>
    </div>

    {{-- Login card --}}
    <div class="relative z-10 w-full max-w-md px-4 py-8">
        <div class="glass-strong neon-border rounded-3xl p-8 sm:p-10 animate-[fade-up_0.6s_ease_both]">

            {{-- Logo --}}
            <div class="flex justify-center mb-8">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-3 group">
                    <div
                        class="w-11 h-11 rounded-2xl gradient-neon flex items-center justify-center neon-glow group-hover:scale-110 transition-transform duration-300">
                        <i class="ri-code-s-slash-line text-dark-950 text-xl font-bold"></i>
                    </div>
                    <span class="font-display font-bold text-xl text-dark-100">
                        Mohammed<span class="text-neon-500">.cms</span>
                    </span>
                </a>
            </div>

            {{-- Heading --}}
            <div class="text-center mb-8">
                <h1 class="font-display font-bold text-2xl text-dark-100 mb-1">Welcome back</h1>
                <p class="text-dark-400 text-sm">Sign in to your admin dashboard</p>
            </div>

            {{-- Session Error --}}
            @if (session('error'))
                <div
                    class="flex items-center gap-3 mb-6 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/25 text-red-400 text-sm animate-[fade-up_0.3s_ease_both]">
                    <i class="ri-error-warning-line text-base shrink-0"></i>
                    <span>{{ session('error') }}</span>
                </div>
            @endif

            {{-- Validation Errors --}}
            @if ($errors->any())
                <div
                    class="flex items-start gap-3 mb-6 px-4 py-3 rounded-xl bg-red-500/10 border border-red-500/25 text-red-400 text-sm animate-[fade-up_0.3s_ease_both]">
                    <i class="ri-error-warning-line text-base shrink-0 mt-0.5"></i>
                    <ul class="space-y-0.5">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Form --}}
            <form id="login-form" method="POST" action="{{ route('admin.login.store') }}" class="space-y-5" novalidate>
                @csrf

                {{-- Email --}}
                <div class="space-y-1.5">
                    <label for="email" class="text-dark-300 text-sm font-medium">Email Address</label>
                    <div class="input-wrapper {{ $errors->has('email') ? 'is-error' : '' }}">
                        <i class="ri-mail-line input-wrapper-icon"></i>
                        <input id="email" type="email" name="email" value="{{ old('email') }}"
                            placeholder="admin@example.com" autocomplete="email" required
                            class="input-field">
                    </div>
                </div>

                {{-- Password --}}
                <div class="space-y-1.5">
                    <label for="password" class="text-dark-300 text-sm font-medium">Password</label>
                    <div class="input-wrapper {{ $errors->has('password') ? 'is-error' : '' }}">
                        <i class="ri-lock-line input-wrapper-icon"></i>
                        <input id="password" type="password" name="password" placeholder="••••••••"
                            autocomplete="current-password" required
                            class="input-field">
                        <button type="button" id="toggle-password" class="input-wrapper-btn"
                            aria-label="Toggle password visibility">
                            <i id="eye-icon" class="ri-eye-off-line"></i>
                        </button>
                    </div>
                </div>

                {{-- Remember me --}}
                <div class="flex items-center justify-between">
                    <label class="flex items-center gap-2 cursor-pointer group">
                        <div class="relative">
                            <input type="checkbox" name="remember" id="remember" class="sr-only peer">
                            <div
                                class="w-5 h-5 rounded-md border border-dark-600 peer-checked:gradient-neon peer-checked:border-neon-500 transition-all duration-200 flex items-center justify-center">
                                <i class="ri-check-line text-dark-950 text-xs opacity-0 peer-checked:opacity-100 transition-opacity duration-200"
                                    id="check-icon"></i>
                            </div>
                        </div>
                        <span class="text-dark-400 text-sm group-hover:text-dark-300 transition-colors">Remember
                            me</span>
                    </label>
                </div>

                {{-- Submit --}}
                <button id="submit-btn" type="submit"
                    class="w-full flex items-center justify-center gap-2 py-3.5 rounded-xl font-semibold text-dark-950 gradient-neon neon-glow hover:scale-[1.02] active:scale-[0.98] transition-transform duration-200 mt-2">
                    <span id="btn-text">Sign In</span>
                    <i id="btn-icon" class="ri-arrow-right-line text-base"></i>
                    <i id="btn-spinner" class="ri-loader-4-line text-base animate-spin hidden"></i>
                </button>
            </form>

            {{-- Divider --}}
            <div class="flex items-center gap-3 my-6">
                <div class="flex-1 h-px bg-dark-700"></div>
                <span class="text-dark-600 text-xs">secured access</span>
                <div class="flex-1 h-px bg-dark-700"></div>
            </div>

            {{-- Back to site --}}
            <div class="text-center">
                <a href="{{ route('home') }}"
                    class="inline-flex items-center gap-1.5 text-dark-500 hover:text-neon-400 text-sm transition-colors duration-200">
                    <i class="ri-arrow-left-line text-sm"></i>
                    Back to portfolio
                </a>
            </div>
        </div>

        {{-- Footer note --}}
        <p class="text-center text-dark-600 text-xs mt-6">
            &copy; {{ date('Y') }} Portfolio CMS. All rights reserved.
        </p>
    </div>

    <script>
        /* ── Toggle password visibility ── */
        const toggleBtn = document.getElementById('toggle-password');
        const passwordInput = document.getElementById('password');
        const eyeIcon = document.getElementById('eye-icon');

        toggleBtn?.addEventListener('click', () => {
            const isHidden = passwordInput.type === 'password';
            passwordInput.type = isHidden ? 'text' : 'password';
            eyeIcon.className = isHidden ? 'ri-eye-line text-base' : 'ri-eye-off-line text-base';
        });

        /* ── Custom checkbox visual ── */
        const rememberCheckbox = document.getElementById('remember');
        const checkIcon = document.getElementById('check-icon');

        rememberCheckbox?.addEventListener('change', () => {
            checkIcon.style.opacity = rememberCheckbox.checked ? '1' : '0';
        });

        /* ── Loading state on submit ── */
        const form = document.getElementById('login-form');
        const submitBtn = document.getElementById('submit-btn');
        const btnText = document.getElementById('btn-text');
        const btnIcon = document.getElementById('btn-icon');
        const btnSpinner = document.getElementById('btn-spinner');

        form?.addEventListener('submit', () => {
            submitBtn.disabled = true;
            submitBtn.classList.add('opacity-80', 'cursor-not-allowed');
            btnText.textContent = 'Signing in...';
            btnIcon.classList.add('hidden');
            btnSpinner.classList.remove('hidden');
        });
    </script>
</body>

</html>
