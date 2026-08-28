@extends('layouts.app', ['title' => 'Perpanjang & Edit Sertifikasi', 'header' => 'Perpanjangan / Edit Sertifikasi', 'subtitle' => 'Perbarui tanggal expired sertifikasi & catat riwayat audit'])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="p-8 bg-slate-900/80 border border-slate-800/80 rounded-3xl backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <i data-lucide="edit" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">Edit Data & Perpanjangan Masa Berlaku</h3>
                <p class="text-xs text-slate-400">Setiap perubahan tanggal expired akan otomatis dicatat dalam Audit Log</p>
            </div>
        </div>

        <form method="POST" action="{{ route('certifications.update', $certification) }}" enctype="multipart/form-data" class="space-y-5">
            @csrf
            @method('PUT')

            <div class="p-4 rounded-2xl bg-slate-800/60 border border-slate-700/60 flex items-center justify-between">
                <div>
                    <span class="text-xs text-slate-400">Pegawai Pemilik:</span>
                    <p class="text-sm font-bold text-white">{{ $certification->user->name ?? '-' }}</p>
                    <p class="text-xs text-indigo-300">{{ $certification->user->employee_number ?? '-' }} &bull; {{ $certification->user->unit ?? '-' }}</p>
                </div>
                <div class="text-right">
                    <span class="text-xs text-slate-400">Status Saat Ini:</span>
                    <div class="mt-1">
                        @if($certification->status === 'expired')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                @if($certification->overridden_by_excel)
                                    Expired (Excel)
                                @else
                                    Expired (Lewat {{ abs($certification->days_remaining) }} hr)
                                @endif
                            </span>
                        @elseif($certification->status === 'warning')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                @if($certification->overridden_by_excel)
                                    Akan Expired (Excel)
                                @else
                                    Akan Expired (Sisa {{ $certification->days_remaining }} hr)
                                @endif
                            </span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                                @if($certification->overridden_by_excel)
                                    Valid (Excel)
                                @else
                                    Aktif (Sisa {{ $certification->days_remaining }} hr)
                                @endif
                            </span>
                        @endif
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Nama Sertifikasi <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="certificate_name" value="{{ old('certificate_name', $certification->certificate_name) }}" required
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Tanggal Expired / Masa Berlaku Baru <span class="text-rose-400">*</span>
                </label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $certification->expiry_date->format('Y-m-d')) }}" required
                       class="w-full px-4 py-3 bg-slate-800/70 border border-indigo-500/50 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Tanggal expired lama: <span class="text-amber-400 font-semibold">{{ $certification->expiry_date->format('d F Y') }}</span></p>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Upload Berkas Bukti Baru (PDF / JPG / PNG) <span class="text-slate-500 font-normal">(Opsional)</span>
                </label>
                @if($certification->certificate_file)
                    <div class="mb-2 p-2.5 rounded-xl bg-slate-800/80 border border-slate-700 flex items-center justify-between text-xs text-slate-300">
                        <span class="flex items-center gap-2 text-indigo-300">
                            <i data-lucide="file-check" class="w-4 h-4 text-emerald-400"></i>
                            Berkas sudah terlampir sebelumnya
                        </span>
                        <a href="{{ Storage::url($certification->certificate_file) }}" target="_blank" class="text-xs text-cyan-400 hover:underline flex items-center gap-1">
                            <i data-lucide="external-link" class="w-3.5 h-3.5"></i>
                            Lihat Berkas Saat Ini
                        </a>
                    </div>
                @endif
                <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 bg-slate-800/80 rounded-xl border border-slate-700 p-2">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Catatan Perpanjangan / Alasan Perubahan (Audit Trail)
                </label>
                <textarea name="renewal_notes" rows="3"
                          placeholder="contoh: Perpanjangan masa berlaku berdasarkan hasil kelulusan renewal exam / verifikasi sertifikat baru..."
                          class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">{{ old('renewal_notes') }}</textarea>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">

                <a href="{{ route('certifications.show', $certification) }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-colors flex items-center gap-2">
                    <i data-lucide="refresh-cw" class="w-4 h-4"></i>
                    <span>Simpan & Catat Audit Log</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection