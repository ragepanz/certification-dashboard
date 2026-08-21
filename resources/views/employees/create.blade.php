@extends('layouts.app', ['title' => 'Tambah Pegawai Baru', 'header' => 'Tambah Pegawai Baru', 'subtitle' => 'Daftarkan data pegawai baru ke sistem'])

@section('content')
<div class="max-w-2xl mx-auto">
    <div class="p-8 bg-slate-900/80 border border-slate-800/80 rounded-3xl backdrop-blur-xl shadow-2xl">
        <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-800">
            <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center">
                <i data-lucide="user-plus" class="w-5 h-5"></i>
            </div>
            <div>
                <h3 class="text-base font-bold text-white">Formulir Identitas Pegawai</h3>
                <p class="text-xs text-slate-400">Email ini akan digunakan untuk pengiriman reminder masa berlaku sertifikasi</p>
            </div>
        </div>

        <form method="POST" action="{{ route('employees.store') }}" class="space-y-5">
            @csrf

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Nomor Pegawai <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="employee_number" value="{{ old('employee_number') }}" required
                           placeholder="contoh: PEG-1006"
                           class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                        Unit Kerja <span class="text-rose-400">*</span>
                    </label>
                    <input type="text" name="unit" value="{{ old('unit') }}" required
                           placeholder="contoh: Information Technology, Human Capital..."
                           class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Nama Lengkap Pegawai <span class="text-rose-400">*</span>
                </label>
                <input type="text" name="name" value="{{ old('name') }}" required
                       placeholder="contoh: Andika Pratama, S.Kom."
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Email Resmi Pegawai <span class="text-rose-400">*</span>
                </label>
                <input type="email" name="email" value="{{ old('email') }}" required
                       placeholder="contoh: andika.pratama@perusahaan.co.id"
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 uppercase tracking-wider mb-2">
                    Kata Sandi Default (Opsional)
                </label>
                <input type="password" name="password"
                       placeholder="Default: password (jika dikosongkan)"
                       class="w-full px-4 py-3 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <p class="text-[11px] text-slate-400 mt-1">Pegawai dapat mengganti kata sandi kapan saja di profilnya.</p>
            </div>

            <div class="pt-6 border-t border-slate-800 flex items-center justify-end gap-3">
                <a href="{{ route('employees.index') }}" class="px-5 py-2.5 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-indigo-600/30 transition-colors flex items-center gap-2">
                    <i data-lucide="user-plus" class="w-4 h-4"></i>
                    <span>Simpan Data Pegawai</span>
                </button>
            </div>
        </form>
    </div>
</div>
@endsection