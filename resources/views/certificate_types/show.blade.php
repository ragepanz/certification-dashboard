@extends('layouts.app', ['title' => 'Detail Jenis: ' . $certificateName, 'header' => 'Detail Modul Pelatihan & Pemegang', 'subtitle' => 'Daftar seluruh pegawai yang memiliki sertifikasi ' . $certificateName])

@section('content')
<div class="space-y-5">
    <!-- Back Link & Module Summary Banner -->
    <div class="flex items-center justify-between">
        <a href="{{ route('certificate-types.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Direktori Jenis Sertifikasi</span>
        </a>

        <div class="flex items-center gap-2">
            <a href="{{ route('certifications.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Pemegang Baru</span>
            </a>
        </div>
    </div>

    <!-- Stats Card for this Module -->
    <div class="p-6 rounded-3xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-2xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 pb-5 border-b border-slate-800">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center font-bold">
                    <i data-lucide="award" class="w-6 h-6"></i>
                </div>
                <div>
                    <span class="text-xs font-semibold text-indigo-400 uppercase tracking-wider">Modul Pelatihan</span>
                    <h3 class="text-xl font-extrabold text-white">{{ $certificateName }}</h3>
                    <p class="text-xs text-slate-400 mt-0.5">Total tercatat pada <strong>{{ $totalCount }}</strong> pegawai</p>
                </div>
            </div>

            <!-- Status Breakdown Badges -->
            <div class="flex items-center gap-2.5">
                <div class="px-3.5 py-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-center">
                    <p class="text-[11px] font-semibold text-emerald-400">Aktif (>60 hr)</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $activeCount }}</p>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-amber-500/10 border border-amber-500/20 text-center">
                    <p class="text-[11px] font-semibold text-amber-400">Warning (≤60 hr)</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $warningCount }}</p>
                </div>
                <div class="px-3.5 py-2 rounded-xl bg-rose-500/10 border border-rose-500/20 text-center">
                    <p class="text-[11px] font-semibold text-rose-400">Expired</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>

        <!-- Filter Bar -->
        <div class="pt-4">
            <form method="GET" action="{{ route('certificate-types.show', urlencode($certificateName)) }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Cari pegawai / No. Pegawai..."
                           class="w-full pl-10 pr-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <select name="unit" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Unit Kerja</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ ($filters['unit'] ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <select name="status" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif (> 60 hari)</option>
                        <option value="warning" {{ ($filters['status'] ?? '') === 'warning' ? 'selected' : '' }}>Akan Expired (≤ 60 hari)</option>
                        <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired</option>
                    </select>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>Filter</span>
                    </button>
                    @if(!empty(array_filter($filters ?? [])))
                        <a href="{{ route('certificate-types.show', urlencode($certificateName)) }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Filter">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>
        </div>
    </div>

    <!-- Table of Holders -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4">Unit Kerja</th>
                        <th class="py-3.5 px-4">Tanggal Terbit</th>
                        <th class="py-3.5 px-4">Tanggal Expired</th>
                        <th class="py-3.5 px-4">Sisa Waktu</th>
                        <th class="py-3.5 px-4">Status</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($certifications as $cert)
                        @php
                            $days = $cert->days_remaining;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-medium text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 text-indigo-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($cert->user->name ?? 'P', 0, 1) }}
                                    </div>
                                    <div>
                                        <p class="font-bold text-xs text-white leading-snug">{{ $cert->user->name ?? '-' }}</p>
                                        <p class="text-xs text-slate-400 font-mono">{{ $cert->user->employee_number ?? '-' }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-xs font-medium">
                                    {{ $cert->user->unit ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-300 text-xs">
                                {{ $cert->issue_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-xs {{ $days < 0 ? 'text-rose-400' : ($days <= 60 ? 'text-amber-400' : 'text-slate-100') }}">
                                {{ $cert->expiry_date->format('d M Y') }}
                            </td>
                            <td class="py-3 px-4 text-xs">
                                @if($days < 0)
                                    <span class="inline-flex items-center gap-1 font-bold text-rose-400">
                                        <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                        Lewat {{ abs($days) }} hari
                                    </span>
                                @elseif($days <= 60)
                                    <span class="inline-flex items-center gap-1 font-bold text-amber-400">
                                        <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                        Sisa {{ $days }} hari
                                    </span>
                                @else
                                    <span class="text-slate-400 font-medium">
                                        Sisa {{ $days }} hari
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4">
                                @if($cert->status === 'expired')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                                        Expired
                                    </span>
                                @elseif($cert->status === 'warning')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                                        Akan Expired
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                                        Aktif
                                    </span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('certifications.show', $cert) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Lihat Detail">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('certifications.edit', $cert) }}" class="p-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg border border-indigo-500/30 transition-colors" title="Edit / Perpanjang">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada data pegawai yang memegang sertifikasi ini sesuai filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certifications->hasPages())
            <div class="p-2.5 border-t border-slate-800/80 bg-slate-900/50">
                {{ $certifications->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
