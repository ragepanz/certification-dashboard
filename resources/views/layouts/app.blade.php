<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-900 text-slate-100">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - LCU Certification Monitoring</title>
    
    <!-- Google Fonts: Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Tailwind CSS CDN -->
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    fontFamily: {
                        sans: ['"Plus Jakarta Sans"', 'sans-serif'],
                    },
                    colors: {
                        brand: {
                            50: '#eef2ff',
                            100: '#e0e7ff',
                            400: '#818cf8',
                            500: '#6366f1',
                            600: '#4f46e5',
                            700: '#4338ca',
                        }
                    }
                }
            }
        }
    </script>
    <!-- Alpine.js & Chart.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-slate-950 text-slate-100 antialiased flex flex-col font-sans" x-data="{ sidebarOpen: false }">
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar Navigation -->
        <aside class="fixed inset-y-0 z-50 flex flex-col w-64 bg-slate-900/95 backdrop-blur-xl border-r border-slate-800/80 transition-transform duration-300 ease-in-out lg:static lg:translate-x-0"
               :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'">
            
            <!-- App Logo / Header -->
            <div class="flex items-center justify-between h-16 px-4 border-b border-slate-800/80 bg-slate-900/50">
                <div class="flex items-center space-x-3">
                    <div class="w-9 h-9 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-md shadow-indigo-500/20 ring-1 ring-white/20">
                        <i data-lucide="award" class="w-5 h-5 text-white"></i>
                    </div>
                    <div>
                        <h1 class="text-base font-bold tracking-tight text-white leading-tight">LCU Monitor</h1>
                        <p class="text-xs font-medium text-slate-400">Certification Dashboard</p>
                    </div>
                </div>
                <button @click="sidebarOpen = false" class="lg:hidden text-slate-400 hover:text-white p-1.5 rounded-lg">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <!-- Navigation Links -->
            <div class="flex-1 flex flex-col justify-between overflow-y-auto px-3.5 py-4 space-y-4">
                <div class="space-y-1.5">
                    <div class="px-2.5 mb-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Menu Utama
                    </div>

                    <!-- Dashboard -->
                    <a href="{{ route('dashboard') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="layout-dashboard" class="w-4 h-4"></i>
                        <span>Dashboard</span>
                    </a>

                    @if(auth()->user()->isSuperAdmin())
                        <!-- Jenis Sertifikasi / Training Modules -->
                        <a href="{{ route('certificate-types.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('certificate-types.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span>Jenis Sertifikasi</span>
                        </a>

                        <!-- Certifications Management -->
                        <a href="{{ route('certifications.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('certifications.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="badge-check" class="w-4 h-4"></i>
                            <span>Data Sertifikasi</span>
                        </a>

                        <!-- Employees Management -->
                        <a href="{{ route('employees.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('employees.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="users" class="w-4 h-4"></i>
                            <span>Data Pegawai</span>
                        </a>


                        <!-- User / Akun Management -->
                        <a href="{{ route('users.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="shield" class="w-4 h-4"></i>
                            <span>Manajemen Akun</span>
                        </a>

                        <!-- Matriks Standar Training (Training Mandatory) -->
                        <a href="{{ route('matrix.index') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('matrix.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="layers" class="w-4 h-4"></i>
                            <span>Training Mandatory</span>
                        </a>

                        <!-- Reminder Settings -->
                        <a href="{{ route('settings.reminder') }}" 
                           class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('settings.*') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                            <i data-lucide="bell-ring" class="w-4 h-4"></i>
                            <span>Pengaturan Reminder</span>
                        </a>
                    @endif

                    <div class="pt-3 px-2.5 mb-1.5 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        Akun & Pengaturan
                    </div>

                    <!-- Profile -->
                    <a href="{{ route('profile') }}" 
                       class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl font-semibold text-sm transition-all duration-200 {{ request()->routeIs('profile') ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/30' : 'text-slate-300 hover:bg-slate-800/60 hover:text-white' }}">
                        <i data-lucide="user-cog" class="w-4 h-4"></i>
                        <span>Profil & Keamanan</span>
                    </a>
                </div>


            </div>

            <!-- User Footer Box -->
            <div class="p-3.5 border-t border-slate-800/80 bg-slate-900/40">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-3 overflow-hidden">
                        <div class="w-8 h-8 rounded-full bg-slate-800 border border-slate-700 flex items-center justify-center font-bold text-xs text-indigo-400 flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="truncate">
                            <p class="text-sm font-semibold text-white truncate">{{ auth()->user()->name }}</p>
                            <p class="text-xs text-slate-400 capitalize truncate">{{ auth()->user()->role === 'superadmin' ? 'Superadmin (LCU)' : (auth()->user()->unit ?? 'Pegawai') }}</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 rounded-lg transition-colors">
                            <i data-lucide="log-out" class="w-4 h-4"></i>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col min-w-0 overflow-hidden bg-slate-950">
            <!-- Top Navbar -->
            <header class="h-16 flex items-center justify-between px-5 lg:px-7 border-b border-slate-800/80 bg-slate-900/40 backdrop-blur-md">
                <div class="flex items-center gap-3.5">
                    <button @click="sidebarOpen = true" class="lg:hidden text-slate-400 hover:text-white p-2 rounded-lg bg-slate-800/50 border border-slate-700">
                        <i data-lucide="menu" class="w-5 h-5"></i>
                    </button>
                    <div>
                        <h2 class="text-base font-bold text-white leading-tight">{{ $header ?? 'Monitoring Dashboard' }}</h2>
                        <p class="text-xs text-slate-400 hidden sm:block">{{ $subtitle ?? 'Learning Center Unit - Monitoring Masa Berlaku Sertifikasi Pegawai' }}</p>
                    </div>
                </div>

                <div class="flex items-center gap-3">
                    <div class="hidden sm:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-800/60 border border-slate-700/60 text-xs text-slate-300">
                        <span class="w-2 h-2 rounded-full bg-amber-500"></span>
                        <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                    </div>

                    <a href="{{ route('profile') }}" class="flex items-center gap-2.5 px-3 py-1.5 rounded-xl bg-slate-800/40 hover:bg-slate-800 border border-slate-700/60 transition-colors">
                        <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold text-xs">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <span class="text-sm font-medium text-slate-200 hidden md:inline">{{ auth()->user()->name }}</span>
                    </a>
                </div>
            </header>

            <!-- Scrollable Content Body -->
            <main class="flex-1 overflow-y-auto p-5 lg:p-7">
                <!-- Alerts / Flash Messages -->
                @if(session('success'))
                    <div class="mb-4 flex items-center gap-2.5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-sm shadow-md">
                        <i data-lucide="check-circle" class="w-5 h-5 flex-shrink-0"></i>
                        <span>{{ session('success') }}</span>
                    </div>
                @endif

                @if($errors->any())
                    <div class="mb-4 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-sm shadow-md">
                        <div class="flex items-center gap-2 font-semibold mb-1">
                            <i data-lucide="alert-circle" class="w-5 h-5 flex-shrink-0"></i>
                            <span>Terdapat beberapa kesalahan:</span>
                        </div>
                        <ul class="list-disc list-inside space-y-0.5 text-xs text-rose-300 pl-1">
                            @foreach($errors->all() as $err)
                                <li>{{ $err }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @yield('content')
            </main>
        </div>
    </div>

    <!-- Initialize Lucide Icons -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
    @stack('scripts')
</body>
</html>
