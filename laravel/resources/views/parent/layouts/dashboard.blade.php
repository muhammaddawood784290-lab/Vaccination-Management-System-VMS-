<!DOCTYPE html>
<html lang="en" class="h-full">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('page-title', 'Parent') - VMS</title>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="h-full bg-gray-50">
    <div class="flex h-full">
        {{-- Sidebar --}}
        <aside class="w-64 bg-emerald-900 text-white flex flex-col fixed h-full">
            {{-- Logo --}}
            <div class="p-5 border-b border-emerald-800">
                <a href="{{ route('parent.dashboard') }}" class="flex items-center gap-3">
                    <div class="w-9 h-9 rounded-lg bg-emerald-600 flex items-center justify-center">
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="white" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                    </div>
                    <div>
                        <p class="text-sm font-bold">VMS</p>
                        <p class="text-[10px] text-emerald-300">Parent Portal</p>
                    </div>
                </a>
            </div>

            {{-- Navigation --}}
            <nav class="flex-1 p-4 space-y-1 overflow-y-auto">
                @php
                    $navItems = [
                        ['label' => 'Dashboard', 'route' => 'parent.dashboard', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/></svg>'],
                        ['label' => 'My Children', 'route' => 'parent.children.index', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 00-4-4H5a4 4 0 00-4 4v2"/><circle cx="9" cy="7" r="4"/></svg>'],
                        ['label' => 'Schedule', 'route' => 'parent.vaccinations.schedule', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="4" width="18" height="18" rx="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>'],
                        ['label' => 'Hospitals', 'route' => 'parent.hospitals.index', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 21h18"/><path d="M5 21V7l7-4 7 4v14"/><path d="M9 21v-6h6v6"/></svg>'],
                        ['label' => 'Appointments', 'route' => 'parent.appointments.index', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>'],
                        ['label' => 'History', 'route' => 'parent.vaccinations.history', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><path d="M9 12l2 2 4-4"/></svg>'],
                        ['label' => 'My Requests', 'route' => 'parent.requests.index', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8z"/><path d="M14 2v6h6"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/></svg>'],
                        ['label' => 'Settings', 'route' => 'parent.settings', 'icon' => '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="3"/><path d="M19.4 15a1.65 1.65 0 00.33 1.82l.06.06a2 2 0 010 2.83 2 2 0 01-2.83 0l-.06-.06a1.65 1.65 0 00-1.82-.33 1.65 1.65 0 00-1 1.51V21a2 2 0 01-4 0v-.09A1.65 1.65 0 009 19.4a1.65 1.65 0 00-1.82.33l-.06.06a2 2 0 01-2.83-2.83l.06-.06A1.65 1.65 0 004.68 15a1.65 1.65 0 00-1.51-1H3a2 2 0 010-4h.09A1.65 1.65 0 004.6 9a1.65 1.65 0 00-.33-1.82l-.06-.06a2 2 0 012.83-2.83l.06.06A1.65 1.65 0 009 4.68a1.65 1.65 0 001-1.51V3a2 2 0 014 0v.09a1.65 1.65 0 001 1.51 1.65 1.65 0 001.82-.33l.06-.06a2 2 0 012.83 2.83l-.06.06A1.65 1.65 0 0019.4 9a1.65 1.65 0 001.51 1H21a2 2 0 010 4h-.09a1.65 1.65 0 00-1.51 1z"/></svg>'],
                    ];
                @endphp
                @foreach($navItems as $item)
                    @php $isActive = request()->routeIs($item['route'] . '*') || request()->routeIs($item['route']); @endphp
                    <a href="{{ route($item['route']) }}" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm transition {{ $isActive ? 'bg-emerald-600 text-white' : 'text-emerald-100 hover:bg-emerald-800 hover:text-white' }}">
                        <span class="flex-shrink-0">{!! $item['icon'] !!}</span>
                        <span class="flex-1">{{ $item['label'] }}</span>
                    </a>
                @endforeach
            </nav>

            {{-- Footer --}}
            <div class="p-4 border-t border-emerald-800">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold">
                        {{ substr(auth()->user()->name ?? 'P', 0, 1) }}
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium text-white truncate">{{ auth()->user()->name ?? 'Parent' }}</p>
                        <p class="text-xs text-emerald-300 truncate">{{ auth()->user()->email ?? '' }}</p>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="w-full flex items-center gap-2 px-3 py-2 text-sm text-emerald-300 hover:text-white hover:bg-emerald-800 rounded-lg transition">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M9 21H5a2 2 0 01-2-2V5a2 2 0 012-2h4"/><polyline points="16 17 21 12 16 7"/><line x1="21" y1="12" x2="9" y2="12"/></svg>
                        Logout
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main Content --}}
        <div class="flex-1 ml-64">
            {{-- Top Bar --}}
            <header class="bg-white border-b border-gray-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                <h1 class="text-lg font-semibold text-gray-900">@yield('page-title', 'Dashboard')</h1>
                <div class="flex items-center gap-4">
                    <a href="{{ route('parent.notifications') }}" class="relative p-2 text-gray-400 hover:text-gray-600 rounded-lg hover:bg-gray-100 transition">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M18 8A6 6 0 006 8c0 7-3 9-3 9h18s-3-2-3-9"/><path d="M13.73 21a2 2 0 01-3.46 0"/></svg>
                        @php $unreadCount = auth()->user()->unreadNotifications()->count(); @endphp
                        @if($unreadCount > 0)
                            <span class="absolute -top-0.5 -right-0.5 w-4 h-4 bg-red-500 text-white text-[10px] font-bold rounded-full flex items-center justify-center">{{ $unreadCount > 9 ? '9+' : $unreadCount }}</span>
                        @endif
                    </a>
                    <a href="{{ route('parent.profile') }}" class="flex items-center gap-2">
                        <div class="w-8 h-8 rounded-full bg-emerald-600 flex items-center justify-center text-white text-sm font-semibold">
                            {{ substr(auth()->user()->name ?? 'P', 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-gray-700 hidden md:block">{{ auth()->user()->name ?? 'Parent' }}</span>
                    </a>
                </div>
            </header>

            {{-- Flash Messages --}}
            @if(session('success'))
                <div class="mx-6 mt-4">
                    <x-flash type="success" :message="session('success')" />
                </div>
            @endif
            @if(session('error'))
                <div class="mx-6 mt-4">
                    <x-flash type="error" :message="session('error')" />
                </div>
            @endif

            {{-- Page Content --}}
            <main class="p-6">
                @yield('content')
            </main>
        </div>
    </div>
</body>
</html>
