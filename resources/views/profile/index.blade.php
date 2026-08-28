@extends('layouts.app', ['title' => 'Pengaturan Profil', 'header' => 'Profil & Akun Pengguna', 'subtitle' => 'Kelola informasi identitas dan keamanan kata sandi'])

@section('content')
<div class="max-w-6xl mx-auto space-y-5">
    <div class="rounded-3xl border border-slate-800/80 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-indigo-950/40 p-5 lg:p-6 shadow-xl backdrop-blur-xl">
        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20 ring-1 ring-white/10">
                    <i data-lucide="user-cog" class="w-7 h-7 text-white"></i>
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">Profil & Keamanan Akun</h3>
                    <p class="text-sm text-slate-400">Perbarui identitas, email, dan kata sandi akun Anda di satu tempat.</p>
                </div>
            </div>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 px-3 py-2">
                    <p class="text-slate-400">Role</p>
                    <p class="mt-1 font-semibold text-white">{{ $user->role === 'superadmin' ? 'Superadmin (LCU)' : 'Pegawai' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 px-3 py-2">
                    <p class="text-slate-400">No. Pegawai</p>
                    <p class="mt-1 font-semibold text-white">{{ $user->employee_number ?? '-' }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 px-3 py-2 col-span-2 sm:col-span-1">
                    <p class="text-slate-400">Email</p>
                    <p class="mt-1 font-semibold text-white truncate">{{ $user->email }}</p>
                </div>
                <div class="rounded-2xl border border-slate-800 bg-slate-900/70 px-3 py-2 col-span-2 sm:col-span-1">
                    <p class="text-slate-400">Nama</p>
                    <p class="mt-1 font-semibold text-white truncate">{{ $user->name }}</p>
                </div>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 xl:grid-cols-2 gap-5 items-start">
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 shadow-xl backdrop-blur-xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-800 bg-slate-900/60">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <i data-lucide="user" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Informasi Akun Saya</h3>
                    <p class="text-xs text-slate-400">Data profil yang tersimpan di sistem LCU</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.update') }}" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">Role / Peran</label>
                        <input type="text" value="{{ $user->role === 'superadmin' ? 'Superadmin (LCU)' : 'Pegawai' }}" disabled class="w-full px-3 py-2.5 bg-slate-800/50 border border-slate-700/60 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-400 uppercase tracking-wider mb-1.5">No. Pegawai</label>
                        <input type="text" value="{{ $user->employee_number ?? '-' }}" disabled class="w-full px-3 py-2.5 bg-slate-800/50 border border-slate-700/60 rounded-xl text-slate-400 text-sm cursor-not-allowed">
                    </div>
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Nama Lengkap</label>
                    <input type="text" name="name" value="{{ old('name', $user->name) }}" required class="w-full px-3 py-2.5 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Alamat Email</label>
                    <input type="email" name="email" value="{{ old('email', $user->email) }}" required class="w-full px-3 py-2.5 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-indigo-600/25 transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Profil</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 shadow-xl backdrop-blur-xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-800 bg-slate-900/60">
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                    <i data-lucide="lock" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Ganti Kata Sandi</h3>
                    <p class="text-xs text-slate-400">Perbarui kata sandi untuk keamanan akun Anda</p>
                </div>
            </div>

            <form method="POST" action="{{ route('profile.password') }}" class="p-5 space-y-4">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password Saat Ini</label>
                    <input type="password" name="current_password" required class="w-full px-3 py-2.5 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Password Baru (Min. 8 karakter)</label>
                    <input type="password" name="password" required class="w-full px-3 py-2.5 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div>
                    <label class="block text-[11px] font-semibold text-slate-300 uppercase tracking-wider mb-1.5">Konfirmasi Password Baru</label>
                    <input type="password" name="password_confirmation" required class="w-full px-3 py-2.5 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                </div>

                <div class="pt-2 flex justify-end">
                    <button type="submit" class="px-4 py-2.5 bg-amber-600 hover:bg-amber-500 text-white text-sm font-semibold rounded-xl shadow-lg shadow-amber-600/25 transition-colors flex items-center gap-2">
                        <i data-lucide="key-round" class="w-4 h-4"></i>
                        <span>Update Password</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection