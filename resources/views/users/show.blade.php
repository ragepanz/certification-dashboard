@extends('layouts.app', ['title' => 'Detail Akun', 'header' => 'Detail Akun Pengguna', 'subtitle' => 'Informasi akun dan akses pengguna dashboard'])

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="rounded-3xl border border-slate-800/80 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-indigo-950/40 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-14 h-14 rounded-2xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-cyan-400 flex items-center justify-center shadow-lg shadow-indigo-500/20 ring-1 ring-white/10 text-xl font-bold text-white">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-lg font-bold text-white">{{ $user->name }}</h3>
                    <p class="text-sm text-slate-400">{{ $user->email }}</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('users.edit', $user) }}" class="inline-flex items-center gap-2 px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                    <i data-lucide="edit" class="w-4 h-4"></i>
                    <span>Edit Akun</span>
                </a>
                <a href="{{ route('users.index') }}" class="inline-flex items-center gap-2 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl border border-slate-700 transition-all">
                    <i data-lucide="arrow-left" class="w-4 h-4"></i>
                    <span>Kembali</span>
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Nomor Pegawai</p>
            <p class="mt-2 text-sm font-semibold text-white">{{ $user->employee_number ?? '-' }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Role</p>
            <p class="mt-2 text-sm font-semibold text-white">{{ $user->role === 'superadmin' ? 'Superadmin' : 'Pegawai' }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Unit Kerja</p>
            <p class="mt-2 text-sm font-semibold text-white">{{ $user->unit ?? '-' }}</p>
        </div>
        <div class="p-5 rounded-2xl bg-slate-900/80 border border-slate-800/80">
            <p class="text-xs text-slate-400 uppercase font-bold tracking-wider">Total Sertifikasi</p>
            <p class="mt-2 text-sm font-semibold text-white">{{ $user->certifications->count() }} sertifikat</p>
        </div>
    </div>
</div>
@endsection
