@extends('layouts.app', ['title' => 'Portal Sertifikasi Pegawai', 'header' => 'Portal Sertifikasi Saya', 'subtitle' => 'Kelola dan pantau status sertifikasi profesional Anda'])

@section('content')
<div class="space-y-5">
    <!-- User Welcome Banner -->
    <div class="p-5 rounded-2xl bg-gradient-to-r from-indigo-900/60 via-slate-900/80 to-slate-900/80 border border-indigo-500/30 backdrop-blur-xl relative overflow-hidden shadow-lg">
        <div class="absolute -right-10 -bottom-10 w-36 h-36 bg-indigo-500/15 rounded-full blur-2xl"></div>
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-3.5">
                <div class="w-12 h-12 rounded-xl bg-gradient-to-tr from-indigo-600 to-cyan-400 flex items-center justify-center font-bold text-lg text-white shadow-md shadow-indigo-500/30">
                    {{ substr($user->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">{{ $user->name }}</h3>
                    <p class="text-xs text-indigo-300 font-medium">
                        No. Pegawai: <strong class="text-white">{{ $user->employee_number ?? '-' }}</strong> &bull; Unit: <strong class="text-white">{{ $user->unit ?? '-' }}</strong> &bull; Email: <span class="text-slate-300">{{ $user->email }}</span>
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                    Akun Pegawai Aktif
                </span>
            </div>
        </div>
    </div>

    <!-- Personal Metrics -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-slate-400">Total Sertifikasi Saya</p>
                <p class="text-2xl font-extrabold text-white mt-0.5">{{ $total }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <i data-lucide="award" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-amber-400">Akan Expired (≤ 60 Hari)</p>
                <p class="text-2xl font-extrabold text-amber-400 mt-0.5">{{ $warning }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                <i data-lucide="clock" class="w-5 h-5"></i>
            </div>
        </div>

        <div class="p-4 rounded-xl bg-slate-900/70 border border-slate-800 flex items-center justify-between">
            <div>
                <p class="text-xs font-semibold text-rose-400">Telah Expired</p>
                <p class="text-2xl font-extrabold text-rose-400 mt-0.5">{{ $expired }}</p>
            </div>
            <div class="w-10 h-10 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center">
                <i data-lucide="alert-triangle" class="w-5 h-5"></i>
            </div>
        </div>
    </div>

    <!-- My Certifications List -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl p-5 backdrop-blur-md">
        <div class="flex items-center justify-between mb-4 pb-3 border-b border-slate-800">
            <div>
                <h4 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="badge-check" class="w-4 h-4 text-indigo-400"></i>
                    Daftar Sertifikasi Kompetensi Anda ({{ count($certifications) }} item)
                </h4>
            </div>
            <p class="text-xs text-slate-400 hidden sm:block">Pemantauan masa berlaku pelatihan & sertifikasi</p>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-3">
            @forelse($certifications as $cert)
                @php
                    $days = $cert->days_remaining;
                @endphp
                <div class="p-4 rounded-xl bg-slate-800/50 border {{ $cert->status === 'expired' ? 'border-rose-500/40 bg-rose-950/10' : ($cert->status === 'warning' ? 'border-amber-500/40 bg-amber-950/10' : 'border-slate-700/60') }} flex flex-col justify-between hover:bg-slate-800/90 transition-all">
                    <div>
                        <div class="flex items-start justify-between gap-2 mb-2">
                            <h5 class="text-sm font-bold text-white leading-snug truncate" title="{{ $cert->certificate_name }}">{{ $cert->certificate_name }}</h5>
                            @if($cert->status === 'expired')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30 flex-shrink-0">
                                    Expired
                                </span>
                            @elseif($cert->status === 'warning')
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30 flex-shrink-0">
                                    Akan Expired
                                </span>
                            @else
                                <span class="px-2 py-0.5 rounded text-xs font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 flex-shrink-0">
                                    Aktif
                                </span>
                            @endif
                        </div>

                        <div class="space-y-1 text-xs text-slate-300">
                            <div class="flex items-center justify-between">
                                <span class="text-slate-400">Expired:</span>
                                <span class="font-bold {{ $cert->status === 'expired' ? 'text-rose-400' : ($cert->status === 'warning' ? 'text-amber-400' : 'text-slate-100') }}">
                                    {{ $cert->expiry_date ? $cert->expiry_date->format('d M Y') : 'Permanent / Tidak Berakhir' }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-3 pt-2 border-t border-slate-700/60 flex items-center justify-between text-xs">
                        <span class="text-slate-400 text-xs">Sisa Masa:</span>
                        @if($cert->expiry_date === null)
                            <span class="font-bold text-emerald-400 flex items-center gap-1">
                                <i data-lucide="shield-check" class="w-3.5 h-3.5"></i>
                                Permanent
                            </span>
                        @elseif($cert->overridden_by_excel)
                            @if($cert->status === 'active')
                                <span class="font-bold text-emerald-400 flex items-center gap-1">
                                    <i data-lucide="badge-check" class="w-3.5 h-3.5"></i>
                                    Valid (Excel)
                                </span>
                            @elseif($cert->status === 'warning')
                                <span class="font-bold text-amber-400 flex items-center gap-1">
                                    <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                    Akan Expired (Excel)
                                </span>
                            @else
                                <span class="font-bold text-rose-400 flex items-center gap-1">
                                    <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                    Expired (Excel)
                                </span>
                            @endif
                        @elseif($days < 0)
                            <span class="font-bold text-rose-400 flex items-center gap-1">
                                <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                Lewat {{ abs($days) }} hari
                            </span>
                        @elseif($days <= 60)
                            <span class="font-bold text-amber-400 flex items-center gap-1">
                                <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                Sisa {{ $days }} hari
                            </span>
                        @else
                            <span class="font-semibold text-emerald-400">
                                {{ $days }} hari
                            </span>
                        @endif
                    </div>

                    @if($cert->certificate_file)
                        <div class="mt-2.5 pt-2 border-t border-slate-700/40 flex items-center justify-end">
                            <a href="{{ Storage::url($cert->certificate_file) }}" target="_blank" class="inline-flex items-center gap-1 text-[11px] text-cyan-400 hover:text-cyan-300 font-semibold underline">
                                <i data-lucide="file-text" class="w-3 h-3"></i>
                                <span>Unduh Berkas Sertifikat</span>
                            </a>
                        </div>
                    @endif
                </div>


            @empty
                <div class="col-span-full p-8 text-center text-slate-500">
                    <i data-lucide="award" class="w-10 h-10 mx-auto text-slate-600 mb-2 opacity-50"></i>
                    <p class="text-sm font-medium">Anda belum memiliki sertifikasi yang terdaftar di sistem.</p>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection