@extends('layouts.app', ['title' => 'Tambah Akun', 'header' => 'Tambah Akun Pengguna', 'subtitle' => 'Buat akun login baru untuk superadmin atau pegawai'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="p-8 bg-slate-900/80 border border-slate-800/80 rounded-3xl backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">Formulir Akun Pengguna</h3>
                <p class="text-xs text-slate-400">Password default jika dikosongkan adalah password123</p>
            </div>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nomor Pegawai <span class="text-rose-400">*</span></label>
                    <input type="text" name="employee_number" value="{{ old('employee_number') }}" required placeholder="contoh: PEG-1006" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Role <span class="text-rose-400">*</span></label>
                    <select name="role" required class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="employee" {{ old('role') === 'employee' ? 'selected' : '' }}>Pegawai</option>
                        <option value="superadmin" {{ old('role') === 'superadmin' ? 'selected' : '' }}>Superadmin</option>
                    </select>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Nama Lengkap <span class="text-rose-400">*</span></label>
                <input type="text" name="name" value="{{ old('name') }}" required placeholder="contoh: Andika Pratama" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Email <span class="text-rose-400">*</span></label>
                <input type="email" name="email" value="{{ old('email') }}" required placeholder="contoh: nama@perusahaan.co.id" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Unit Kerja <span class="text-rose-400">*</span></label>
                    <input type="text" name="unit" value="{{ old('unit') }}" required placeholder="contoh: LCU" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Password</label>
                    <input type="password" name="password" placeholder="Default: password123" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-colors flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Simpan Akun</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
