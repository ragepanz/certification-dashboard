@extends('layouts.app', ['title' => 'Pengaturan Profil', 'header' => 'Profil & Akun Pengguna', 'subtitle' => 'Kelola informasi identitas dan keamanan kata sandi'])

@section('content')
<div class="max-w-4xl space-y-4">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <!-- Profile Info Card (Compact) -->
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                        <i data-lucide="user" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white">Informasi Akun Saya</h3>
                        <p class="text-[10px] text-slate-400">Data profil Anda yang tersimpan di sistem LCU</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.update') }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">Role / Peran</label>
                            <input type="text" value="{{ $user->role === 'superadmin' ? 'Superadmin (LCU)' : 'Pegawai' }}" disabled
                                   class="w-full px-2.5 py-1.5 bg-slate-800/40 border border-slate-700/50 rounded-lg text-slate-400 text-xs cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-[10px] font-semibold text-slate-400 uppercase tracking-wider mb-1">No. Pegawai</label>
                            <input type="text" value="{{ $user->employee_number ?? '-' }}" disabled
                                   class="w-full px-2.5 py-1.5 bg-slate-800/40 border border-slate-700/50 rounded-lg text-slate-400 text-xs cursor-not-allowed">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-1">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', $user->name) }}" required
                               class="w-full px-2.5 py-1.5 bg-slate-800/70 border border-slate-700/80 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-1">Alamat Email</label>
                        <input type="email" name="email" value="{{ old('email', $user->email) }}" required
                               class="w-full px-2.5 py-1.5 bg-slate-800/70 border border-slate-700/80 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex justify-end">
                        <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-lg shadow-sm shadow-indigo-600/30 transition-colors flex items-center gap-1.5">
                            <i data-lucide="save" class="w-3.5 h-3.5"></i>
                            <span>Simpan Profil</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>

        <!-- Change Password Card (Compact) -->
        <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-lg flex flex-col justify-between">
            <div>
                <div class="flex items-center gap-2 mb-3 pb-3 border-b border-slate-800">
                    <div class="w-7 h-7 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center">
                        <i data-lucide="lock" class="w-4 h-4"></i>
                    </div>
                    <div>
                        <h3 class="text-xs font-bold text-white">Ganti Kata Sandi</h3>
                        <p class="text-[10px] text-slate-400">Perbarui kata sandi untuk keamanan akun Anda</p>
                    </div>
                </div>

                <form method="POST" action="{{ route('profile.password') }}" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-1">Password Saat Ini</label>
                        <input type="password" name="current_password" required
                               class="w-full px-2.5 py-1.5 bg-slate-800/70 border border-slate-700/80 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-1">Password Baru (Min. 8 karakter)</label>
                        <input type="password" name="password" required
                               class="w-full px-2.5 py-1.5 bg-slate-800/70 border border-slate-700/80 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div>
                        <label class="block text-[10px] font-semibold text-slate-300 uppercase tracking-wider mb-1">Konfirmasi Password Baru</label>
                        <input type="password" name="password_confirmation" required
                               class="w-full px-2.5 py-1.5 bg-slate-800/70 border border-slate-700/80 rounded-lg text-white text-xs focus:outline-none focus:ring-1 focus:ring-indigo-500">
                    </div>

                    <div class="pt-2 border-t border-slate-800 flex justify-end">
                        <button type="submit" class="px-3 py-1.5 bg-amber-600 hover:bg-amber-500 text-white text-xs font-semibold rounded-lg shadow-sm shadow-amber-600/30 transition-colors flex items-center gap-1.5">
                            <i data-lucide="key" class="w-3.5 h-3.5"></i>
                            <span>Update Password</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection