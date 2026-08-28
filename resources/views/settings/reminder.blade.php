@extends('layouts.app', ['title' => 'Pengaturan Reminder', 'header' => 'Pengaturan Reminder Email', 'subtitle' => 'Atur kapan reminder sertifikasi dikirim otomatis'])

@section('content')
<div class="max-w-4xl mx-auto space-y-5">
    <div class="rounded-3xl border border-slate-800/80 bg-gradient-to-r from-slate-900/90 via-slate-900/70 to-amber-950/40 p-6 shadow-xl backdrop-blur-xl">
        <div class="flex items-center gap-4">
            <div class="w-14 h-14 rounded-2xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                <i data-lucide="bell-ring" class="w-7 h-7"></i>
            </div>
            <div>
                <h3 class="text-lg font-bold text-white">Jadwal Reminder Otomatis</h3>
                <p class="text-sm text-slate-400">Tentukan sendiri interval pengiriman email sebelum dan setelah sertifikasi expired.</p>
            </div>
        </div>
    </div>

    @if($recentReminderLogs->isNotEmpty())
        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 shadow-xl backdrop-blur-xl overflow-hidden mt-5">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-800 bg-slate-900/60">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <i data-lucide="history" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Riwayat Reminder Terakhir</h3>
                    <p class="text-xs text-slate-400">10 log terakhir yang terkirim</p>
                </div>
            </div>

            <div class="p-4">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                            <th class="py-3.5 px-4">Tanggal Kirim</th>
                            <th class="py-3.5 px-4">Type</th>
                            <th class="py-3.5 px-4">Email</th>
                            <th class="py-3.5 px-4">Sertifikasi</th>
                            <th class="py-3.5 px-4 text-right">Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($recentReminderLogs as $log)
                            <tr class="hover:bg-slate-800/40 transition-colors">
                                <td class="py-3 px-4 text-slate-300">{{ $log->sent_at->format('d M Y H:i') }}</td>
                                <td class="py-3 px-4 text-slate-300">
                                    @if(str_starts_with($log->type, 'H-'))
                                        <span class="inline-flex items-center gap-0.5 text-amber-400 font-semibold">
                                            <i data-lucide="clock" class="w-3.5 h-3.5"></i>
                                            {{ $log->type }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-0.5 text-rose-400 font-semibold">
                                            <i data-lucide="alert-circle" class="w-3.5 h-3.5"></i>
                                            {{ $log->type }}
                                        </span>
                                    @endif
                                </td>
                                <td class="py-3 px-4 text-slate-300">{{ substr($log->recipient_email, 0, 30) }}</td>
                                <td class="py-3 px-4 text-slate-300">
                                    <p class="text-xs font-medium text-white">{{ $log->certification->certificate_name ?? '-' }}</p>
                                    <p class="text-xs text-slate-400 font-mono">{{ $log->certification->user->name ?? '-' }}</p>
                                </td>
                                <td class="py-3 px-4 text-right">
                                    <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $log->status === 'sent' ? 'bg-emerald-500/15 text-emerald-400 border border-emerald-500/30' : 'bg-rose-500/15 text-rose-400 border border-rose-500/30' }}">
                                        {{ $log->status === 'sent' ? 'Kirim' : 'Gagal' }}
                                    </span>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-5">
        <div class="lg:col-span-2 rounded-3xl border border-slate-800/80 bg-slate-900/80 shadow-xl backdrop-blur-xl overflow-hidden">
            <div class="flex items-center gap-3 px-5 py-4 border-b border-slate-800 bg-slate-900/60">
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                    <i data-lucide="settings" class="w-5 h-5"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Konfigurasi Hari Pengiriman</h3>
                    <p class="text-xs text-slate-400">Pisahkan beberapa nilai dengan koma</p>
                </div>
            </div>

            <form method="POST" action="{{ route('settings.reminder.update') }}" class="p-5 space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Reminder Sebelum Expired (H-)</label>
                    <input type="text" name="reminder_days_before" value="{{ old('reminder_days_before', $daysBefore) }}" required placeholder="contoh: 60,30,5" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <p class="text-[11px] text-slate-400 mt-1.5">Contoh: 60,30,5 berarti email dikirim H-60, H-30, dan H-5 sebelum expired.</p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">Reminder Setelah Expired (H+)</label>
                    <input type="text" name="reminder_days_after" value="{{ old('reminder_days_after', $daysAfter) }}" required placeholder="contoh: 5,10" class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-amber-500">
                    <p class="text-[11px] text-slate-400 mt-1.5">Contoh: 5,10 berarti email dikirim H+5 dan H+10 setelah expired.</p>
                </div>

                <div class="pt-5 border-t border-slate-800 flex justify-end">
                    <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-colors flex items-center gap-2">
                        <i data-lucide="save" class="w-4 h-4"></i>
                        <span>Simpan Pengaturan</span>
                    </button>
                </div>
            </form>
        </div>

        <div class="rounded-3xl border border-slate-800/80 bg-slate-900/80 p-5 shadow-xl backdrop-blur-xl h-fit">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                <i data-lucide="info" class="w-4 h-4 text-cyan-400"></i>
                Catatan
            </h4>
            <div class="mt-4 space-y-3 text-xs text-slate-400 leading-relaxed">
                <p>Pengaturan ini dipakai oleh proses otomatis <span class="text-slate-200 font-semibold">certification:send-reminders</span> saat dijalankan.</p>
                <p><span class="text-amber-400 font-semibold">Status: Belum aktif.</span> Scheduler otomatis masih dinonaktifkan di <code>routes/console.php</code> hingga dapat validasi.</p>
                <p>Reminder hanya dikirim satu kali untuk setiap tipe, misalnya H-30 hanya sekali per sertifikasi.</p>
            </div>
        </div>
    </div>
</div>
@endsection
