@extends('layouts.app', ['title' => 'Detail Sertifikasi & Audit Log', 'header' => 'Detail Sertifikasi & Riwayat Perubahan', 'subtitle' => 'Informasi lengkap sertifikasi, histori perpanjangan, dan reminder log'])

@section('content')
<div class="space-y-8 max-w-5xl mx-auto">
    <!-- Header Back & Quick Action -->
    <div class="flex items-center justify-between">
        <a href="{{ route('certifications.index') }}" class="inline-flex items-center gap-2 text-xs font-semibold text-slate-400 hover:text-white transition-colors">
            <i data-lucide="arrow-left" class="w-4 h-4"></i>
            <span>Kembali ke Data Sertifikasi</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('certifications.edit', $certification) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/20 transition-all">
                <i data-lucide="edit" class="w-4 h-4"></i>
                <span>Perbarui / Perpanjang</span>
            </a>
            <form method="POST" action="{{ route('certifications.send-reminder', $certification) }}" onsubmit="return confirm('Kirim email pengingat sekarang ke {{ $certification->user->email }}?');">
                @csrf
                <button type="submit" class="inline-flex items-center gap-2 px-3.5 py-2 bg-cyan-600/20 hover:bg-cyan-600 text-cyan-300 hover:text-white rounded-xl border border-cyan-500/30 text-xs font-semibold transition-all">
                    <i data-lucide="send" class="w-4 h-4"></i>
                    <span>Kirim Email Pengingat</span>
                </button>
            </form>
        </div>
    </div>

    <!-- Certificate Information Card -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-2xl">
        <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 pb-6 border-b border-slate-800">
            <div>
                <span class="text-xs font-semibold uppercase tracking-wider text-indigo-400">Nama Sertifikasi</span>
                <h3 class="text-2xl font-extrabold text-white mt-1">{{ $certification->certificate_name }}</h3>
                <p class="text-xs text-slate-400 mt-1">ID Sistem: #CERT-{{ str_pad($certification->id, 5, '0', STR_PAD_LEFT) }}</p>
            </div>
            <div>
                @if($certification->status === 'expired')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-rose-500/15 text-rose-400 border border-rose-500/30">
                        <i data-lucide="alert-triangle" class="w-4 h-4"></i>
                        @if($certification->overridden_by_excel)
                            Status: Expired (Excel)
                        @else
                            Status: Expired (Lewat {{ abs($certification->days_remaining) }} Hari)
                        @endif
                    </span>
                @elseif($certification->status === 'warning')
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-amber-500/15 text-amber-400 border border-amber-500/30">
                        <i data-lucide="clock" class="w-4 h-4"></i>
                        @if($certification->overridden_by_excel)
                            Status: Akan Expired (Excel)
                        @else
                            Status: Akan Expired (Sisa {{ $certification->days_remaining }} Hari)
                        @endif
                    </span>
                @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-xs font-bold bg-emerald-500/15 text-emerald-400 border border-emerald-500/30">
                        <i data-lucide="check-circle" class="w-4 h-4"></i>
                        @if($certification->overridden_by_excel)
                            Status: Valid (Excel)
                        @else
                            Status: Aktif (Sisa {{ $certification->days_remaining }} Hari)
                        @endif
                    </span>
                @endif
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-6">
            <!-- Left Info: Pegawai -->
            <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-3 text-xs">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] text-slate-400 mb-2">Informasi Pegawai Pemilik</h4>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nama Pegawai:</span>
                    <strong class="text-white">{{ $certification->user->name ?? '-' }}</strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Nomor Pegawai:</span>
                    <span class="text-indigo-300 font-mono font-semibold">{{ $certification->user->employee_number ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Email Pegawai:</span>
                    <span class="text-slate-200">{{ $certification->user->email ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Jabatan (Job Title):</span>
                    <span class="text-indigo-200 font-medium">{{ $certification->user->job_title ?? '-' }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Unit Kerja:</span>
                    <span class="text-slate-200">{{ $certification->user->unit ?? '-' }}</span>
                </div>
            </div>

            <!-- Right Info: Masa Berlaku -->
            <div class="p-5 rounded-2xl bg-slate-800/50 border border-slate-700/60 space-y-3 text-xs">
                <h4 class="font-bold text-white uppercase tracking-wider text-[11px] text-slate-400 mb-2">Detail Masa Berlaku</h4>
                <div class="flex justify-between">
                    <span class="text-slate-400">Tanggal Expired:</span>
                    <strong class="{{ $certification->status === 'expired' ? 'text-rose-400' : 'text-emerald-400' }}">
                        {{ $certification->expiry_date->format('d F Y') }}
                    </strong>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Sisa Hari Menuju Expired:</span>
                    @if($certification->overridden_by_excel)
                        <span class="font-bold {{ $certification->status === 'expired' ? 'text-rose-400' : ($certification->status === 'warning' ? 'text-amber-400' : 'text-emerald-400') }}">
                            {{ $certification->status === 'expired' ? 'Expired (Excel)' : ($certification->status === 'warning' ? 'Akan Expired (Excel)' : 'Valid (Excel)') }}
                        </span>
                    @else
                        <span class="font-bold {{ $certification->days_remaining < 0 ? 'text-rose-400' : ($certification->days_remaining <= 60 ? 'text-amber-400' : 'text-slate-200') }}">
                            {{ $certification->days_remaining }} hari
                        </span>
                    @endif
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-400">Didaftarkan Pada:</span>
                    <span class="text-slate-400">{{ $certification->created_at->format('d/m/Y H:i') }}</span>
                </div>
                <div class="flex justify-between items-center pt-2 border-t border-slate-700/60">
                    <span class="text-slate-400">Berkas Bukti:</span>
                    @if($certification->certificate_file)
                        <a href="{{ Storage::url($certification->certificate_file) }}" target="_blank" class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-600/20 hover:bg-indigo-600 border border-indigo-500/30 text-indigo-300 hover:text-white rounded-lg font-semibold text-xs transition-colors">
                            <i data-lucide="file-text" class="w-3.5 h-3.5"></i>
                            <span>Buka / Unduh Berkas</span>
                        </a>
                    @else
                        <span class="text-slate-500 italic">Tidak ada berkas terlampir</span>
                    @endif
                </div>
            </div>
        </div>
    </div>


    <!-- Audit Log Section (PRD Requirement: Riwayat Perubahan) -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-cyan-500/10 text-cyan-400 flex items-center justify-center">
                <i data-lucide="history" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-base font-bold text-white">Riwayat Perubahan & Perpanjangan (Audit Log)</h4>
                <p class="text-xs text-slate-400">Catatan setiap perpanjangan dan perubahan tanggal expired sertifikasi ini</p>
            </div>
        </div>

        <div class="space-y-4">
            @forelse($certification->logs as $log)
                <div class="p-4 rounded-2xl bg-slate-800/40 border border-slate-700/60 flex flex-col md:flex-row md:items-center justify-between gap-4 text-xs">
                    <div class="space-y-1.5">
                        <div class="flex items-center gap-2">
                            <span class="px-2 py-0.5 rounded bg-indigo-500/20 text-indigo-300 font-semibold text-[10px]">
                                Perpanjangan Tanggal Expired
                            </span>
                            <span class="text-slate-400">&bull;</span>
                            <span class="text-slate-400">{{ $log->created_at->format('d F Y - H:i:s') }}</span>
                        </div>
                        <p class="text-sm font-semibold text-white">
                            Tanggal Expired: 
                            <span class="line-through text-rose-400">{{ $log->old_expiry_date ? $log->old_expiry_date->format('d M Y') : 'Awal' }}</span>
                            &rarr; 
                            <span class="text-emerald-400 font-bold">{{ $log->new_expiry_date->format('d M Y') }}</span>
                        </p>
                        @if($log->notes)
                            <p class="text-slate-300 italic">&ldquo;{{ $log->notes }}&rdquo;</p>
                        @endif
                    </div>

                    <div class="text-right flex-shrink-0">
                        <span class="text-slate-400">Dilakukan Oleh:</span>
                        <p class="font-bold text-indigo-300">{{ $log->actor->name ?? 'Administrator' }}</p>
                        <p class="text-[10px] text-slate-500">{{ $log->actor->email ?? '-' }}</p>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    <i data-lucide="check-circle" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                    Belum ada riwayat perpanjangan atau perubahan untuk sertifikasi ini.
                </div>
            @endforelse
        </div>
    </div>

    <!-- Reminder History -->
    <div class="p-8 rounded-3xl bg-slate-900/80 border border-slate-800/80 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-4 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center">
                <i data-lucide="mail-check" class="w-5 h-5"></i>
            </div>
            <div>
                <h4 class="text-base font-bold text-white">Riwayat Pengiriman Email Reminder</h4>
                <p class="text-xs text-slate-400">Log pengingat otomatis H-5 atau H+5 yang telah dikirim ke pegawai</p>
            </div>
        </div>

        <div class="space-y-3">
            @forelse($certification->reminderLogs as $rLog)
                <div class="p-3.5 rounded-2xl bg-slate-800/40 border border-slate-700/60 flex items-center justify-between text-xs">
                    <div class="flex items-center gap-3">
                        <span class="px-2.5 py-1 rounded-lg {{ $rLog->type === 'H-5' ? 'bg-amber-500/15 text-amber-400 border border-amber-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }} font-bold text-[11px]">
                            {{ $rLog->type }}
                        </span>
                        <div>
                            <p class="font-semibold text-white">Dikirim ke: {{ $rLog->recipient_email }}</p>
                            <p class="text-[10px] text-slate-400">{{ $rLog->sent_at ? $rLog->sent_at->format('d F Y, H:i:s') : '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold {{ $rLog->status === 'sent' ? 'bg-emerald-500/15 text-emerald-400' : 'bg-rose-500/15 text-rose-400' }}">
                            <i data-lucide="{{ $rLog->status === 'sent' ? 'check' : 'alert-circle' }}" class="w-3 h-3"></i>
                            {{ ucfirst($rLog->status) }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-500 text-xs">
                    Belum ada riwayat email reminder yang dikirim untuk sertifikasi ini.
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection