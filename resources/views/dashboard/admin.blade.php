@extends('layouts.app', ['title' => 'Dashboard Monitoring LCU', 'header' => 'LCU Certification Monitoring Dashboard', 'subtitle' => 'Pemantauan masa berlaku sertifikasi pegawai & status reminder otomatis'])

@section('content')
<div class="space-y-5">
    <!-- Top Action Bar & Summary Info -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <span class="w-2.5 h-2.5 rounded-full bg-indigo-500"></span>
                Ringkasan Eksekutif Sertifikasi
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Memantau <strong class="text-slate-200">{{ $totalCertifications }}</strong> sertifikasi dari <strong class="text-slate-200">{{ $totalEmployees }}</strong> pegawai aktif di seluruh unit.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('certifications.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Sertifikasi</span>
            </a>
            <a href="{{ route('reports.index') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Laporan & Export</span>
            </a>
        </div>
    </div>

    <!-- KPI Metric Cards Grid -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <!-- Card 1: Total Pegawai -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800/80 relative overflow-hidden group hover:border-indigo-500/50 transition-all duration-200 shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Pegawai</span>
                <div class="w-8 h-8 rounded-lg bg-indigo-500/10 border border-indigo-500/20 text-indigo-400 flex items-center justify-center">
                    <i data-lucide="users" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white tracking-tight">{{ $totalEmployees }}</span>
                <span class="text-xs text-slate-400">orang</span>
            </div>
            <p class="text-xs text-slate-500 mt-1">Terdaftar di unit TN & GMF</p>
        </div>

        <!-- Card 2: Total Sertifikasi -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-slate-800/80 relative overflow-hidden group hover:border-cyan-500/50 transition-all duration-200 shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Sertifikasi</span>
                <div class="w-8 h-8 rounded-lg bg-cyan-500/10 border border-cyan-500/20 text-cyan-400 flex items-center justify-center">
                    <i data-lucide="award" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-white tracking-tight">{{ $totalCertifications }}</span>
                <span class="text-xs text-slate-400">sertifikat</span>
            </div>
            <div class="flex items-center gap-1.5 text-xs text-emerald-400 mt-1">
                <i data-lucide="check" class="w-3.5 h-3.5"></i>
                <span>{{ $activeCount }} aktif (>60 hr)</span>
            </div>
        </div>

        <!-- Card 3: Sertifikasi Akan Expired (Warning) -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-amber-500/30 relative overflow-hidden group hover:border-amber-500/60 transition-all duration-200 shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Akan Expired (≤60 Hari)</span>
                <div class="w-8 h-8 rounded-lg bg-amber-500/10 border border-amber-500/30 text-amber-400 flex items-center justify-center">
                    <i data-lucide="clock" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-amber-400 tracking-tight">{{ $expiringCount }}</span>
                <span class="text-xs text-amber-300/80">perlu renewal</span>
            </div>
            <p class="text-xs text-amber-400/80 mt-1">Mendekati masa berlaku habis</p>
        </div>


        <!-- Card 4: Sertifikasi Expired (Danger) -->
        <div class="p-4 rounded-2xl bg-gradient-to-b from-slate-900/90 to-slate-900/40 border border-rose-500/30 relative overflow-hidden group hover:border-rose-500/60 transition-all duration-200 shadow-md">
            <div class="flex items-center justify-between mb-2">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400">Telah Expired</span>
                <div class="w-8 h-8 rounded-lg bg-rose-500/10 border border-rose-500/30 text-rose-400 flex items-center justify-center">
                    <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                </div>
            </div>
            <div class="flex items-baseline gap-1.5">
                <span class="text-3xl font-extrabold text-rose-400 tracking-tight">{{ $expiredCount }}</span>
                <span class="text-xs text-rose-300/80">lewat waktu</span>
            </div>
            <p class="text-xs text-rose-400/80 mt-1">Status masa berlaku berakhir</p>
        </div>
    </div>

    <!-- Chart & Recent Audit Log Overview -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
        <!-- Visual Status Breakdown Chart -->
        <div class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md flex flex-col justify-between">
            <div>
                <h4 class="text-sm font-bold text-white mb-0.5 flex items-center gap-2">
                    <i data-lucide="pie-chart" class="w-4 h-4 text-indigo-400"></i>
                    Proporsi Status Masa Berlaku
                </h4>
                <p class="text-xs text-slate-400 mb-2">Distribusi status sertifikasi keseluruhan</p>
            </div>

            <div class="relative flex items-center justify-center h-40 my-1">
                <canvas id="statusChart"></canvas>
            </div>

            <div class="grid grid-cols-3 gap-2 pt-3 border-t border-slate-800 text-center">
                <div class="p-2 rounded-xl bg-emerald-500/10 border border-emerald-500/20">
                    <p class="text-xs font-semibold text-emerald-400">Aktif</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $activeCount }}</p>
                </div>
                <div class="p-2 rounded-xl bg-amber-500/10 border border-amber-500/20">
                    <p class="text-xs font-semibold text-amber-400">Warning</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $expiringCount }}</p>
                </div>
                <div class="p-2 rounded-xl bg-rose-500/10 border border-rose-500/20">
                    <p class="text-xs font-semibold text-rose-400">Expired</p>
                    <p class="text-sm font-bold text-white mt-0.5">{{ $expiredCount }}</p>
                </div>
            </div>
        </div>

        <!-- Recent Audit Trail Logs -->
        <div class="lg:col-span-2 p-5 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md flex flex-col justify-between">
            <div>
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <h4 class="text-sm font-bold text-white flex items-center gap-2">
                            <i data-lucide="history" class="w-4 h-4 text-cyan-400"></i>
                            Audit Log Perubahan Terakhir
                        </h4>
                    </div>
                </div>

                <div class="space-y-2">
                    @forelse($recentLogs as $log)
                        <div class="p-3 rounded-xl bg-slate-800/50 border border-slate-700/60 flex items-start justify-between gap-3 text-xs">
                            <div class="flex items-start gap-3">
                                <div class="w-7 h-7 rounded-lg bg-indigo-500/20 text-indigo-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                    <i data-lucide="refresh-cw" class="w-3.5 h-3.5"></i>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-white">{{ $log->certification->certificate_name ?? 'Sertifikasi' }}</p>
                                    <p class="text-xs text-slate-300 mt-0.5">
                                        Pegawai: <span class="text-indigo-300 font-semibold">{{ $log->certification->user->name ?? '-' }}</span> 
                                        &bull; Expired: <span class="line-through text-rose-400">{{ $log->old_expiry_date ? $log->old_expiry_date->format('d M Y') : '-' }}</span> 
                                        &rarr; <span class="text-emerald-400 font-bold">{{ $log->new_expiry_date->format('d M Y') }}</span>
                                    </p>
                                </div>
                            </div>
                            <div class="text-right flex-shrink-0">
                                <span class="text-xs text-slate-400">{{ $log->created_at->diffForHumans() }}</span>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center text-slate-500 text-xs">
                            <i data-lucide="clock" class="w-8 h-8 mx-auto text-slate-600 mb-1 opacity-50"></i>
                            Belum ada riwayat perpanjangan sertifikasi.
                        </div>
                    @endforelse
                </div>
            </div>
            
            <div class="pt-3 border-t border-slate-800/80 text-right">
                <a href="{{ route('certifications.index') }}" class="text-xs font-semibold text-indigo-400 hover:text-indigo-300 inline-flex items-center gap-1">
                    Lihat semua sertifikasi &rarr;
                </a>
            </div>
        </div>
    </div>

    <!-- Highlight Widget: Direktori Jenis Sertifikasi -->
    <div class="p-5 rounded-2xl bg-slate-900/70 border border-slate-800/80 backdrop-blur-md space-y-3.5">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 pb-3 border-b border-slate-800">
            <div>
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="layers" class="w-4 h-4 text-indigo-400"></i>
                    Highlight Jenis Sertifikasi Terbanyak (Top Modul)
                </h4>
                <p class="text-xs text-slate-400 mt-0.5">Ringkasan modul pelatihan dengan pemegang terbanyak di kedinasan.</p>
            </div>
            
            <a href="{{ route('certificate-types.index') }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 text-xs font-semibold rounded-xl transition-all whitespace-nowrap">
                <span>Buka Katalog Lengkap ({{ $certificateTypes->count() }} Modul)</span>
                <i data-lucide="arrow-right" class="w-3.5 h-3.5"></i>
            </a>
        </div>

        <!-- Top 4 Highlights -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3">
            @foreach($certificateTypes->take(4) as $ctype)
                <a href="{{ route('certificate-types.show', urlencode($ctype->certificate_name)) }}"
                   class="p-3 rounded-xl bg-slate-800/50 hover:bg-slate-800/90 border border-slate-700/60 hover:border-indigo-500/50 transition-all flex flex-col justify-between group">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <span class="w-7 h-7 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center font-bold text-xs group-hover:scale-110 transition-transform">
                                <i data-lucide="award" class="w-4 h-4"></i>
                            </span>
                            <span class="px-2 py-0.5 rounded-full bg-slate-900 border border-slate-700/80 text-[10px] font-bold text-white">
                                {{ $ctype->total_count }} Pegawai
                            </span>
                        </div>
                        <h5 class="text-xs font-bold text-white leading-snug line-clamp-1 group-hover:text-indigo-300 transition-colors" title="{{ $ctype->certificate_name }}">
                            {{ $ctype->certificate_name }}
                        </h5>
                    </div>

                    <div class="mt-2.5 pt-2 border-t border-slate-700/50 flex items-center justify-between text-[11px]">
                        <div class="flex items-center gap-1.5">
                            <span class="inline-flex items-center gap-0.5 text-emerald-400 font-semibold" title="Aktif">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400"></span>
                                {{ $ctype->active_count }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-amber-400 font-semibold" title="Akan Expired">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-400"></span>
                                {{ $ctype->warning_count }}
                            </span>
                            <span class="inline-flex items-center gap-0.5 text-rose-400 font-semibold" title="Expired">
                                <span class="w-1.5 h-1.5 rounded-full bg-rose-400"></span>
                                {{ $ctype->expired_count }}
                            </span>
                        </div>
                        <span class="text-indigo-400 text-xs font-bold">&rarr;</span>
                    </div>
                </a>
            @endforeach
        </div>
    </div>


    <!-- Interactive Monitoring Table & Filter Section -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">

        <!-- Filter Header -->
        <div class="p-4 border-b border-slate-800/80 bg-slate-900/50">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-2 mb-3">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="table" class="w-4 h-4 text-indigo-400"></i>
                    Tabel Monitoring Masa Berlaku Sertifikasi Pegawai
                </h3>
            </div>

            <!-- Filter Controls Form -->
            <form method="GET" action="{{ route('dashboard') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-6 gap-2.5">
                <!-- Search Input -->
                <div class="lg:col-span-2 relative">
                    <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                        <i data-lucide="search" class="w-4 h-4"></i>
                    </div>
                    <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                           placeholder="Ketik kata kunci (cth: an, ivan, 533...)"
                           class="w-full pl-10 pr-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <!-- Unit Filter -->
                <div>
                    <select name="unit" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Unit Kerja</option>
                        @foreach($units as $u)
                            <option value="{{ $u }}" {{ ($filters['unit'] ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                        @endforeach
                    </select>
                </div>

                <!-- Status Filter -->
                <div>
                    <select name="status" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="">Semua Status</option>
                        <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif (> 60 hari)</option>
                        <option value="warning" {{ ($filters['status'] ?? '') === 'warning' ? 'selected' : '' }}>Akan Expired (≤ 60 hari)</option>
                        <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired (Lewat waktu)</option>
                    </select>
                </div>


                <!-- Per Page -->
                <div>
                    <select name="per_page" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 baris/hal</option>
                        <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris/hal</option>
                        <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris/hal</option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex items-center gap-2">
                    <button type="submit" class="flex-1 py-2 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                        <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                        <span>Cari</span>
                    </button>
                    @if(!empty(array_filter($filters ?? [])))
                        <a href="{{ route('dashboard') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Pencarian">
                            <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                        </a>
                    @endif
                </div>
            </form>

            @if(!empty($filters['search']))
                <div class="mt-2.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-between text-xs text-indigo-300">
                    <span>Hasil pencarian kata kunci <strong>"{{ $filters['search'] }}"</strong>: Menampilkan total <strong>{{ $certifications->total() }}</strong> sertifikat (Halaman {{ $certifications->currentPage() }} dari {{ $certifications->lastPage() }}).</span>
                    <a href="{{ route('dashboard') }}" class="text-xs text-rose-400 hover:underline">Hapus filter pencarian &times;</a>
                </div>
            @endif
        </div>

        <!-- Table Data -->
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4">Unit</th>
                        <th class="py-3.5 px-4">Nama Sertifikasi</th>
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
                            <td class="py-3 px-4 font-semibold text-indigo-300 text-xs">
                                {{ $cert->certificate_name }}
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
                                @elseif($days == 0)
                                    <span class="inline-flex items-center gap-1 font-bold text-rose-400">
                                        Expired Hari Ini
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
                                    <!-- Detail / Audit button -->
                                    <a href="{{ route('certifications.show', $cert) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Lihat Detail & Audit Log">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <!-- Edit / Renewal button -->
                                    <a href="{{ route('certifications.edit', $cert) }}" class="p-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg border border-indigo-500/30 transition-colors" title="Perpanjang / Edit">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <!-- Manual Reminder trigger -->
                                    <form method="POST" action="{{ route('certifications.send-reminder', $cert) }}" onsubmit="return confirm('Kirim email reminder sekarang ke {{ $cert->user->email }}?');">
                                        @csrf
                                        <button type="submit" class="p-1.5 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-lg border border-cyan-500/30 transition-colors" title="Kirim Reminder Manual">
                                            <i data-lucide="send" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-10 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada data sertifikasi yang cocok dengan filter.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination (Compact) -->
        @if($certifications->hasPages())
            <div class="p-2.5 border-t border-slate-800/80 bg-slate-900/50">
                {{ $certifications->links() }}
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const ctx = document.getElementById('statusChart').getContext('2d');
        new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Aktif (>60 hr)', 'Akan Expired (≤60 hr)', 'Expired'],
                datasets: [{
                    data: [{{ $activeCount }}, {{ $expiringCount }}, {{ $expiredCount }}],
                    backgroundColor: ['#10b981', '#f59e0b', '#f43f5e'],
                    borderWidth: 0,
                    hoverOffset: 6
                }]
            },

            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        backgroundColor: '#0f172a',
                        titleColor: '#fff',
                        bodyColor: '#cbd5e1',
                        borderColor: '#334155',
                        borderWidth: 1,
                        padding: 10,
                        boxPadding: 4,
                        usePointStyle: true
                    }
                },
                cutout: '72%'
            }
        });
    });
</script>
@endpush
@endsection