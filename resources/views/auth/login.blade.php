<!DOCTYPE html>
<html lang="id" class="h-full bg-slate-950">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - LCU Certification Monitoring</title>
    
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <script src="https://cdn.tailwindcss.com"></script>
    <script src="https://unpkg.com/lucide@latest"></script>
</head>
<body class="h-full bg-slate-950 text-slate-100 flex items-center justify-center p-4 selection:bg-indigo-500 selection:text-white font-sans">
    
    <!-- Background Glow Effects -->
    <div class="fixed inset-0 overflow-hidden pointer-events-none -z-10">
        <div class="absolute -top-40 -left-40 w-96 h-96 bg-indigo-600/20 rounded-full blur-3xl"></div>
        <div class="absolute top-1/2 -right-40 w-96 h-96 bg-cyan-600/15 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 left-1/3 w-96 h-96 bg-purple-600/20 rounded-full blur-3xl"></div>
    </div>

    <div class="w-full max-w-md">
        <!-- Logo & Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 shadow-xl shadow-indigo-500/25 ring-1 ring-white/20 mb-4">
                <i data-lucide="award" class="w-8 h-8 text-white"></i>
            </div>
            <h1 class="text-2xl font-bold text-white tracking-tight">LCU Certification Monitor</h1>
            <p class="text-sm text-slate-400 mt-1.5">Employee Certification Monitoring & Reminder</p>
        </div>

        <!-- Login Card -->
        <div class="bg-slate-900/80 backdrop-blur-xl border border-slate-800/80 p-8 rounded-3xl shadow-2xl shadow-black/50">
            @if(session('success'))
                <div class="mb-5 p-3.5 rounded-xl bg-emerald-500/10 border border-emerald-500/30 text-emerald-400 text-xs">
                    {{ session('success') }}
                </div>
            @endif

            @if($errors->any())
                <div class="mb-5 p-3.5 rounded-xl bg-rose-500/10 border border-rose-500/30 text-rose-400 text-xs">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="login" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Email atau Nomor Pegawai
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="user" class="w-4 h-4"></i>
                        </div>
                        <input type="text" id="login" name="login" value="{{ old('login') }}" required autofocus
                               class="w-full pl-10 pr-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="contoh: admin@lcu.com atau PEG-1001">
                    </div>
                </div>

                <div>
                    <label for="password" class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Kata Sandi
                    </label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                            <i data-lucide="lock" class="w-4 h-4"></i>
                        </div>
                        <input type="password" id="password" name="password" required
                               class="w-full pl-10 pr-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:border-transparent transition-all"
                               placeholder="••••••••">
                    </div>
                </div>

                <div class="flex items-center justify-between text-xs">
                    <label class="flex items-center gap-2 cursor-pointer text-slate-400 hover:text-slate-300">
                        <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-700 bg-slate-800 text-indigo-600 focus:ring-indigo-500">
                        <span>Ingat saya</span>
                    </label>
                </div>

                <button type="submit" 
                        class="w-full py-3 px-4 bg-gradient-to-r from-indigo-600 to-indigo-700 hover:from-indigo-500 hover:to-indigo-600 text-white font-semibold rounded-xl text-sm shadow-lg shadow-indigo-600/30 hover:shadow-indigo-600/50 transition-all duration-200 flex items-center justify-center gap-2 group">
                    <span>Masuk ke Dashboard</span>
                    <i data-lucide="arrow-right" class="w-4 h-4 group-hover:translate-x-0.5 transition-transform"></i>
                </button>
            </form>

            <!-- Quick Demo Credentials Box -->
            <div class="mt-8 pt-6 border-t border-slate-800/80 text-xs text-slate-400">
                <p class="font-semibold text-slate-300 mb-2">Akun Demo Cepat:</p>
                <div class="grid grid-cols-2 gap-2 text-[11px]">
                    <div class="p-2.5 rounded-lg bg-slate-800/50 border border-slate-700/50">
                        <p class="font-semibold text-indigo-300">Superadmin (LCU):</p>
                        <p class="text-slate-400 truncate">admin@lcu.com</p>
                        <p class="text-slate-500">pass: password</p>
                    </div>
                    <div class="p-2.5 rounded-lg bg-slate-800/50 border border-slate-700/50">
                        <p class="font-semibold text-cyan-300">Pegawai (IT):</p>
                        <p class="text-slate-400 truncate">PEG-1001</p>
                        <p class="text-slate-500">pass: password</p>
                    </div>
                </div>
            </div>
        </div>

        <p class="text-center text-xs text-slate-500 mt-6">
            &copy; {{ date('Y') }} Learning Center Unit (LCU) &bull; All rights reserved.
        </p>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
            lucide.createIcons();
        });
    </script>
</body>
</html>