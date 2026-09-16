<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $title ?? 'VMS' }} — Vaccination Management System</title>
    @vite(["resources/css/app.css", "resources/js/app.js"])
    <style>
        .auth-grid-bg {
            background-image: linear-gradient(rgba(255,255,255,0.03) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.03) 1px, transparent 1px);
            background-size: 40px 40px;
        }
    </style>
</head>
<body class="bg-slate-50 font-sans antialiased">
    <div class="min-h-screen flex">
        {{-- Left Panel --}}
        <div class="hidden lg:flex lg:w-[480px] xl:w-[560px] flex-shrink-0 bg-[#111928] flex-col relative overflow-hidden">
            <div class="absolute inset-0 auth-grid-bg"></div>
            <div class="relative z-10 flex flex-col h-full px-12 py-12">
                {{-- Logo --}}
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-[10px] bg-[#1C64F2] flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.2">
                            <path d="M12 2l2 7h7l-6 4 2 7-5-4-5 4 2-7-6-4h7z"/>
                        </svg>
                    </div>
                    <span class="text-white font-semibold text-lg tracking-tight">VMS</span>
                </div>

                {{-- Main content --}}
                <div class="flex-1 flex flex-col justify-center">
                    <div class="mb-10">
                        <h2 class="text-3xl font-bold text-white leading-tight mb-4">
                            Vaccination Management<br>Made Simple
                        </h2>
                        <p class="text-gray-400 text-base leading-relaxed">
                            A centralized platform connecting parents, hospitals, and healthcare administrators to ensure every child receives timely, complete vaccination care.
                        </p>
                    </div>

                    {{-- Features --}}
                    <div class="space-y-4">
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-xl flex-shrink-0">👶</div>
                            <div>
                                <p class="text-sm font-semibold text-white">Child-centered records</p>
                                <p class="text-xs text-gray-400 mt-0.5">Complete vaccination history in one place</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-xl flex-shrink-0">🏥</div>
                            <div>
                                <p class="text-sm font-semibold text-white">Hospital coordination</p>
                                <p class="text-xs text-gray-400 mt-0.5">Seamless scheduling across certified facilities</p>
                            </div>
                        </div>
                        <div class="flex gap-4">
                            <div class="w-10 h-10 rounded-lg bg-white/5 flex items-center justify-center text-xl flex-shrink-0">📋</div>
                            <div>
                                <p class="text-sm font-semibold text-white">Smart scheduling</p>
                                <p class="text-xs text-gray-400 mt-0.5">AI-guided vaccination timeline reminders</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Stats --}}
                <div class="grid grid-cols-3 gap-4 pt-8 border-t border-white/10">
                    <div>
                        <p class="text-xl font-bold text-white">12,400+</p>
                        <p class="text-xs text-gray-500 mt-0.5">Children registered</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-white">48</p>
                        <p class="text-xs text-gray-500 mt-0.5">Partner hospitals</p>
                    </div>
                    <div>
                        <p class="text-xl font-bold text-white">98%</p>
                        <p class="text-xs text-gray-500 mt-0.5">Schedule compliance</p>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Panel --}}
        <div class="flex-1 flex flex-col items-center justify-center p-6 bg-slate-50">
            <div class="w-full max-w-[400px]">
                {{-- Mobile Logo --}}
                <div class="flex items-center gap-2 mb-8 lg:hidden">
                    <div class="w-8 h-8 rounded-lg bg-[#1C64F2] flex items-center justify-center">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2.5"><path d="M12 2l2 7h7l-6 4 2 7-5-4-5 4 2-7-6-4h7z"/></svg>
                    </div>
                    <span class="font-semibold text-gray-900">VMS</span>
                </div>

                @if(session('success'))
                    <div class="mb-4 p-4 bg-green-50 border border-green-200 rounded-lg text-sm text-green-700">
                        {{ session('success') }}
                    </div>
                @endif

                @yield('auth-content')
            </div>
        </div>
    </div>
</body>
</html>
