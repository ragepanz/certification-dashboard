@extends('layouts.app', ['title' => 'Tambah Sertifikasi Baru', 'header' => 'Tambah Sertifikasi Pegawai', 'subtitle' => 'Input data sertifikasi baru ke sistem'])

@section('content')
<div class="max-w-3xl mx-auto">
    <div class="p-8 bg-slate-900/80 border border-slate-800/80 rounded-3xl backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <i data-lucide="plus-circle" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">Formulir Data Sertifikasi</h3>
                <p class="text-xs text-slate-400">Pastikan tanggal expired diisi dengan akurat</p>
            </div>
        </div>

        <form method="POST" action="{{ route('certifications.store') }}" enctype="multipart/form-data" class="space-y-5">
            @csrf

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Pegawai Pemilik Sertifikasi <span class="text-rose-400">*</span>
                </label>
                <select name="user_id" required class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">-- Pilih Pegawai --</option>
                    @foreach($employees as $emp)
                        <option value="{{ $emp->id }}" {{ old('user_id') == $emp->id ? 'selected' : '' }}>
                            {{ $emp->name }} ({{ $emp->employee_number ?? '-' }} &bull; {{ $emp->unit ?? '-' }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Nama Sertifikasi <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="certificate_name" value="{{ old('certificate_name') }}" required
                       placeholder="contoh: Human Factor, Safety Management System, EASA Part 145..."
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Tanggal Expired / Masa Berlaku <span class="text-rose-400">*</span>
                </label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date') }}" required
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Upload Berkas Bukti Sertifikat (PDF / JPG / PNG) <span class="text-slate-500 font-normal">(Opsional, Maks 10MB)</span>
                </label>
                <input type="file" name="certificate_file" accept=".pdf,.jpg,.jpeg,.png"
                       class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 bg-slate-800/80 rounded-xl border border-slate-700 p-2">
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">

                <a href="{{ route('certifications.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-colors flex items-center gap-2">
                    <i data-lucide="save" class="w-4 h-4"></i>
                    <span>Simpan Sertifikasi</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection