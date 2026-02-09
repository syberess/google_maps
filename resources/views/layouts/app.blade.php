<!DOCTYPE html>
<html lang="tr" x-data="{ darkMode: localStorage.getItem('darkMode') === 'true', sidebarOpen: true }" :class="{ 'dark': darkMode }">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'SalesMap Pro')</title>
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            darkMode: 'class',
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['Inter', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#fdf4ff',
                            100: '#fae8ff',
                            200: '#f5d0fe',
                            300: '#f0abfc',
                            400: '#e879f9',
                            500: '#d946ef',
                            600: '#c026d3',
                            700: '#a21caf',
                            800: '#86198f',
                            900: '#701a75',
                        },
                        slate: {
                            850: '#172033',
                            950: '#0a0f1a',
                        }
                    }
                }
            }
        }
    </script>
    
    <!-- Alpine.js -->
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    
    <!-- Leaflet CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    
    <style>
        [x-cloak] { display: none !important; }
        
        body {
            font-family: 'Inter', sans-serif;
        }
        
        .sidebar-transition { 
            transition: margin-left 0.3s cubic-bezier(0.4, 0, 0.2, 1); 
        }
        
        .glass {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .glass-light {
            background: rgba(255, 255, 255, 0.7);
            backdrop-filter: blur(10px);
            -webkit-backdrop-filter: blur(10px);
        }
        
        .gradient-sidebar {
            background: linear-gradient(180deg, #1e1b4b 0%, #312e81 50%, #4338ca 100%);
        }
        
        .gradient-card {
            background: linear-gradient(135deg, rgba(99, 102, 241, 0.1) 0%, rgba(168, 85, 247, 0.1) 100%);
        }
        
        .hover-lift {
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 40px -10px rgba(99, 102, 241, 0.3);
        }
        
        .nav-active {
            background: linear-gradient(90deg, rgba(255,255,255,0.15) 0%, rgba(255,255,255,0.05) 100%);
            border-left: 3px solid #a78bfa;
        }
        
        .stat-card {
            position: relative;
            overflow: hidden;
        }
        
        .stat-card::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 100%;
            background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        }

        .scrollbar-thin::-webkit-scrollbar {
            width: 6px;
        }
        
        .scrollbar-thin::-webkit-scrollbar-track {
            background: transparent;
        }
        
        .scrollbar-thin::-webkit-scrollbar-thumb {
            background: rgba(255,255,255,0.2);
            border-radius: 3px;
        }
    </style>
    
    @stack('styles')
</head>
<body class="bg-slate-100 dark:bg-slate-950 min-h-screen">
    <div class="flex">
        <!-- Sidebar -->
        <aside x-show="sidebarOpen" 
               x-transition:enter="transition ease-out duration-300"
               x-transition:enter-start="-translate-x-full opacity-0"
               x-transition:enter-end="translate-x-0 opacity-100"
               x-transition:leave="transition ease-in duration-200"
               x-transition:leave-start="translate-x-0 opacity-100"
               x-transition:leave-end="-translate-x-full opacity-0"
               class="fixed left-0 top-0 h-full w-72 gradient-sidebar z-50 flex flex-col shadow-2xl">
            
            <!-- Logo -->
            <div class="p-6">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-gradient-to-br from-violet-400 to-fuchsia-500 rounded-2xl flex items-center justify-center shadow-lg shadow-violet-500/30">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                                  d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h1 class="text-xl font-bold text-white tracking-tight">SalesMap</h1>
                        <div class="flex items-center space-x-1">
                            <span class="text-xs text-violet-300">Enterprise</span>
                            <span class="w-1.5 h-1.5 bg-emerald-400 rounded-full animate-pulse"></span>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Search -->
            <div class="px-4 mb-4">
                <div class="relative">
                    <input type="text" placeholder="Hızlı arama..." 
                           class="w-full bg-white/10 border border-white/10 rounded-xl px-4 py-2.5 text-sm text-white placeholder-violet-300 focus:outline-none focus:ring-2 focus:ring-violet-400/50 focus:border-transparent">
                    <kbd class="absolute right-3 top-1/2 -translate-y-1/2 px-2 py-0.5 text-xs bg-white/10 text-violet-300 rounded">⌘K</kbd>
                </div>
            </div>
            
            <!-- Navigation -->
            <nav class="flex-1 px-3 space-y-1 overflow-y-auto scrollbar-thin">
                <p class="text-xs font-semibold text-violet-400 uppercase tracking-widest px-3 mb-3">Genel</p>
                
                <a href="{{ route('dashboard') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('dashboard') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-amber-400 to-orange-500 flex items-center justify-center shadow-lg shadow-orange-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 5a1 1 0 011-1h14a1 1 0 011 1v2a1 1 0 01-1 1H5a1 1 0 01-1-1V5zM4 13a1 1 0 011-1h6a1 1 0 011 1v6a1 1 0 01-1 1H5a1 1 0 01-1-1v-6zM16 13a1 1 0 011-1h2a1 1 0 011 1v6a1 1 0 01-1 1h-2a1 1 0 01-1-1v-6z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Kontrol Paneli</span>
                </a>
                
                <a href="{{ route('search') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('search*') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-emerald-400 to-teal-500 flex items-center justify-center shadow-lg shadow-teal-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Keşfet</span>
                </a>
                
                <p class="text-xs font-semibold text-violet-400 uppercase tracking-widest px-3 mt-6 mb-3">Yönetim</p>
                
                <a href="{{ route('companies.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('companies*') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-blue-400 to-indigo-500 flex items-center justify-center shadow-lg shadow-indigo-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
                            </svg>
                        </div>
                        <span class="font-medium">Firmalar</span>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-white/20 text-white rounded-full">
                        {{ \App\Models\Company::count() }}
                    </span>
                </a>
                
                <a href="{{ route('activities.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('activities*') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-pink-400 to-rose-500 flex items-center justify-center shadow-lg shadow-rose-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Aktiviteler</span>
                </a>
                
                <a href="{{ route('maps.index') }}" 
                   class="flex items-center justify-between px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('maps*') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="flex items-center space-x-3">
                        <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-cyan-400 to-sky-500 flex items-center justify-center shadow-lg shadow-sky-500/20">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/>
                            </svg>
                        </div>
                        <span class="font-medium">Rota Planlama</span>
                    </div>
                    <span class="px-2.5 py-1 text-xs font-semibold bg-emerald-500/30 text-emerald-300 rounded-full">
                        {{ \App\Models\Company::withCoordinates()->count() }}
                    </span>
                </a>
                
                <p class="text-xs font-semibold text-violet-400 uppercase tracking-widest px-3 mt-6 mb-3">Analitik</p>
                
                <a href="{{ route('reports.index') }}" 
                   class="flex items-center space-x-3 px-4 py-3 rounded-xl transition-all duration-200
                          {{ request()->routeIs('reports*') ? 'nav-active text-white' : 'text-violet-200 hover:bg-white/10 hover:text-white' }}">
                    <div class="w-9 h-9 rounded-lg bg-gradient-to-br from-violet-400 to-purple-500 flex items-center justify-center shadow-lg shadow-purple-500/20">
                        <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                    </div>
                    <span class="font-medium">Raporlar</span>
                </a>
            </nav>
            
            <!-- Bottom Section -->
            <div class="p-4 border-t border-white/10">
                <!-- Performance Card -->
                <div class="bg-white/10 rounded-2xl p-4 mb-4">
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-sm font-medium text-white">Performans</span>
                        <span class="text-xs text-emerald-400">+12%</span>
                    </div>
                    <div class="grid grid-cols-2 gap-3">
                        <div class="text-center bg-white/10 rounded-xl py-2">
                            <div class="text-xl font-bold text-white">
                                {{ \App\Models\Company::byStatus('musteri')->count() }}
                            </div>
                            <div class="text-xs text-violet-300">Müşteri</div>
                        </div>
                        <div class="text-center bg-white/10 rounded-xl py-2">
                            <div class="text-xl font-bold text-amber-400">
                                {{ \App\Models\Company::byStatus('muzakere')->count() }}
                            </div>
                            <div class="text-xs text-violet-300">Müzakere</div>
                        </div>
                    </div>
                </div>
                
                <!-- Theme Toggle -->
                <button @click="darkMode = !darkMode; localStorage.setItem('darkMode', darkMode)"
                        class="flex items-center justify-center space-x-2 w-full px-4 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 transition-all duration-200">
                    <template x-if="!darkMode">
                        <svg class="w-5 h-5 text-violet-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                        </svg>
                    </template>
                    <template x-if="darkMode">
                        <svg class="w-5 h-5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                        </svg>
                    </template>
                    <span class="text-sm text-violet-200" x-text="darkMode ? 'Aydınlık Mod' : 'Karanlık Mod'"></span>
                </button>
            </div>
        </aside>
        
        <!-- Main Content -->
        <main class="flex-1 sidebar-transition" :class="sidebarOpen ? 'ml-72' : 'ml-0'">
            <!-- Top Bar -->
            <header class="sticky top-0 z-40 bg-white/80 dark:bg-slate-900/80 backdrop-blur-xl border-b border-slate-200 dark:border-slate-800">
                <div class="flex items-center justify-between px-6 py-4">
                    <div class="flex items-center space-x-4">
                        <button @click="sidebarOpen = !sidebarOpen" 
                                class="p-2 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                            </svg>
                        </button>
                        
                        <div>
                            <h1 class="text-xl font-bold text-slate-800 dark:text-white">
                                @yield('page-title', 'Dashboard')
                            </h1>
                            <p class="text-sm text-slate-500 dark:text-slate-400">
                                {{ now()->translatedFormat('l, d F Y') }}
                            </p>
                        </div>
                    </div>
                    
                    <div class="flex items-center space-x-3">
                        @yield('header-actions')
                        
                        <!-- Notifications -->
                        <button class="relative p-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 hover:bg-slate-200 dark:hover:bg-slate-700 transition-all duration-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/>
                            </svg>
                            <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-rose-500 rounded-full"></span>
                        </button>
                        
                        <!-- Profile -->
                        <div class="flex items-center space-x-3 pl-3 border-l border-slate-200 dark:border-slate-700">
                            <div class="w-10 h-10 rounded-xl bg-gradient-to-br from-violet-500 to-purple-600 flex items-center justify-center text-white font-semibold shadow-lg shadow-violet-500/20">
                                A
                            </div>
                            <div class="hidden md:block">
                                <p class="text-sm font-semibold text-slate-700 dark:text-white">Admin</p>
                                <p class="text-xs text-slate-500 dark:text-slate-400">Yönetici</p>
                            </div>
                        </div>
                    </div>
                </div>
            </header>
            
            <!-- Page Content -->
            <div class="p-6">
                @yield('content')
            </div>
        </main>
    </div>
    
    <!-- Leaflet JS -->
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    
    @stack('scripts')
</body>
</html>
