@extends('layouts.app', ['title' => 'Detail Pegawai & Sertifikasi', 'header' => 'Profil & Sertifikasi Pegawai', 'subtitle' => 'Detail lengkap pegawai dan daftar sertifikasi yang dimiliki'])

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Back & Quick Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('employees.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Daftar Pegawai</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('employees.edit', $employee) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold rounded-xl border border-slate-700 transition-all">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Edit Identitas</span>
            </a>
            <a href="{{ route('certifications.create') }}?user_id={{ $employee->id }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Sertifikasi Pegawai Ini</span>
            </a>
        </div>
    </div>

    <!-- Employee Profile Card -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-2xl">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-6 pb-6 border-b border-slate-800">
            <div class="flex items-center gap-4">
                <div class="w-16 h-16 rounded-2xl bg-gradient-to-tr from-indigo-600 to-cyan-400 flex items-center justify-center font-bold text-2xl text-white shadow-lg shadow-indigo-500/30">
                    {{ substr($employee->name, 0, 1) }}
                </div>
                <div>
                    <h3 class="text-xl font-bold text-white">{{ $employee->name }}</h3>
                    <p class="text-xs text-indigo-300">
                        Nomor Pegawai: <strong class="text-white">{{ $employee->employee_number ?? '-' }}</strong> &bull; Unit: <strong class="text-white">{{ $employee->unit ?? '-' }}</strong>
                    </p>
                    <p class="text-[11px] text-slate-400 mt-1">Email: {{ $employee->email }}</p>
                </div>
            </div>
            <div>
                <span class="px-3 py-1.5 rounded-xl bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 text-xs font-bold">
                    {{ $employee->certifications->count() }} Sertifikasi Terdaftar
                </span>
            </div>
        </div>

        <div class="pt-6">
            <h4 class="text-sm font-bold text-white mb-4 flex items-center gap-2">
                <i data-lucide="award" class="w-4 h-4 text-indigo-400"></i>
                Daftar Sertifikasi Milik Pegawai
            </h4>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @forelse($employee->certifications as $cert)
                    @php
                        $days = $cert->days_remaining;
                    @endphp
                    <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/60 flex flex-col justify-between hover:bg-slate-800/80 transition-all">
                        <div>
                            <div class="flex items-start justify-between gap-3 mb-2">
                                <h5 class="text-sm font-bold text-white">{{ $cert->certificate_name }}</h5>
                                @if($cert->status === 'expired')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                        Expired
                                    </span>
                                @elseif($cert->status === 'warning')
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                        Akan Expired
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                        Aktif
                                    </span>
                                @endif
                            </div>

                            <div class="space-y-1 text-xs text-slate-300">
                                <p class="text-slate-400">Expired: <span class="font-semibold {{ $cert->status === 'expired' ? 'text-rose-400' : ($cert->status === 'warning' ? 'text-amber-400' : 'text-slate-200') }}">{{ $cert->expiry_date ? $cert->expiry_date->format('d/m/Y') : 'Permanen' }}</span></p>
                            </div>
                        </div>

                        <div class="mt-4 pt-3 border-t border-slate-700/60 flex items-center justify-between text-xs">
                            @if($cert->overridden_by_excel)
                                <span class="{{ $cert->status === 'expired' ? 'text-rose-400 font-bold' : ($cert->status === 'warning' ? 'text-amber-400 font-bold' : 'text-emerald-400 font-bold') }}">
                                    {{ $cert->status === 'expired' ? 'Expired (Excel)' : ($cert->status === 'warning' ? 'Akan Expired (Excel)' : 'Valid (Excel)') }}
                                </span>
                            @else
                                <span class="{{ $days < 0 ? 'text-rose-400 font-bold' : ($days <= 30 ? 'text-amber-400 font-bold' : 'text-slate-400') }}">
                                    {{ $days < 0 ? 'Lewat ' . abs($days) . ' hari' : 'Sisa ' . $days . ' hari' }}
                                </span>
                            @endif
                            <div class="flex items-center gap-1.5">
                                <a href="{{ route('certifications.show', $cert) }}" class="p-1 text-slate-400 hover:text-white" title="Detail & Riwayat">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                </a>
                                <a href="{{ route('certifications.edit', $cert) }}" class="p-1 text-indigo-400 hover:text-indigo-300" title="Perpanjang">
                                    <i data-lucide="edit" class="w-4 h-4"></i>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-2 p-8 text-center text-slate-500 text-xs">
                        Pegawai ini belum memiliki sertifikasi yang tercatat.
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>
@endsection