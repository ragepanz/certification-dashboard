@extends('layouts.app', ['title' => 'Katalog Jenis Sertifikasi', 'header' => 'Katalog Jenis Pelatihan & Sertifikasi', 'subtitle' => 'Daftar seluruh jenis sertifikasi kompetensi kedinasan beserta statistik peserta'])

@section('content')
<div class="space-y-5">
    <!-- Top Summary Banner -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800/80 flex items-center justify-between shadow-md">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Jenis Modul</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl font-extrabold text-white tracking-tight">{{ $totalModules }}</span>
                    <span class="text-xs text-slate-400">modul</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Tercatat di kurikulum kedinasan</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                <i data-lucide="layers" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800/80 flex items-center justify-between shadow-md">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Rekor Pemegang</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl font-extrabold text-cyan-400 tracking-tight">{{ number_format($totalHolders) }}</span>
                    <span class="text-xs text-slate-400">sertifikasi</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Terdistribusi ke 207 pegawai</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800/80 flex items-center justify-between shadow-md">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Ambang Batas Notifikasi</span>
                <div class="flex items-baseline gap-1.5 mt-1">
                    <span class="text-2xl font-extrabold text-amber-400 tracking-tight">60 Hari</span>
                </div>
                <p class="text-xs text-slate-500 mt-0.5">Masa peringatan awal renewal</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 border border-amber-500/20 text-amber-400 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- Search & Filter Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
        <form method="GET" action="{{ route('certificate-types.index') }}" class="flex-1 flex items-center gap-2">
            <div class="relative flex-1">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Cari jenis sertifikasi (contoh: Human Factor, Quality, EASA, dll)..."
                       class="w-full pl-10 pr-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center gap-1.5 transition-colors">
                <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                <span>Cari</span>
            </button>
            @if(!empty($search))
                <a href="{{ route('certificate-types.index') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset">
                    <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                </a>
            @endif
        </form>

        <a href="{{ route('certifications.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition-all">
            <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-400"></i>
            <span>Tambah Data Sertifikasi</span>
        </a>
    </div>

    <!-- Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3.5">
        @forelse($certificateTypes as $ctype)
            <div class="p-4 rounded-2xl bg-slate-900/70 border border-slate-800/80 hover:border-indigo-500/50 transition-all flex flex-col justify-between group shadow-md">
                <div>
                    <div class="flex items-start justify-between gap-2 mb-2.5">
                        <div class="w-8 h-8 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-xs flex-shrink-0 group-hover:scale-110 transition-transform">
                            <i data-lucide="award" class="w-4 h-4"></i>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full bg-slate-800 border border-slate-700 text-xs font-bold text-white">
                            {{ $ctype->total_count }} Pegawai
                        </span>
                    </div>

                    <h4 class="text-sm font-bold text-white leading-snug group-hover:text-indigo-300 transition-colors" title="{{ $ctype->certificate_name }}">
                        {{ $ctype->certificate_name }}
                    </h4>
                </div>

                <div class="mt-4 pt-3 border-t border-slate-800 flex items-center justify-between text-xs">
                    <!-- Status Dots -->
                    <div class="flex items-center gap-2">
                        <span class="inline-flex items-center gap-1 text-emerald-400 font-semibold" title="Aktif (>60 hari)">
                            <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                            {{ $ctype->active_count }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-amber-400 font-semibold" title="Akan Expired (≤60 hari)">
                            <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                            {{ $ctype->warning_count }}
                        </span>
                        <span class="inline-flex items-center gap-1 text-rose-400 font-semibold" title="Expired">
                            <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                            {{ $ctype->expired_count }}
                        </span>
                    </div>

                    <!-- View Details Link -->
                    <a href="{{ route('certificate-types.show', urlencode($ctype->certificate_name)) }}" 
                       class="inline-flex items-center gap-1 text-xs font-bold text-indigo-400 hover:text-indigo-300 hover:underline">
                        <span>Detail Pegawai</span>
                        <i data-lucide="chevron-right" class="w-3.5 h-3.5"></i>
                    </a>
                </div>
            </div>
        @empty
            <div class="col-span-full p-12 text-center text-slate-500 bg-slate-900/40 rounded-2xl border border-slate-800">
                <i data-lucide="layers" class="w-10 h-10 mx-auto text-slate-600 mb-2 opacity-50"></i>
                <p class="text-sm font-medium">Tidak ada jenis sertifikasi yang cocok dengan pencarian.</p>
            </div>
        @endforelse
    </div>
</div>
@endsection
