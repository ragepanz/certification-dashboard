@extends('layouts.app', ['title' => 'Kelola Data Sertifikasi', 'header' => 'Manajemen Data Sertifikasi', 'subtitle' => 'Kelola sertifikasi pegawai, perpanjangan masa berlaku, dan audit log'])

@section('content')
<div class="space-y-5" x-data="{ importModalOpen: false }">
    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="badge-check" class="w-5 h-5 text-indigo-400"></i>
                Data Sertifikasi Pegawai
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Daftar seluruh sertifikasi yang tercatat di Learning Center Unit.</p>
        </div>
        <div class="flex flex-wrap items-center gap-2.5" x-data="{ exportDropdown: false }">
            <!-- Export Options Dropdown -->
            <div class="relative">
                <button @click="exportDropdown = !exportDropdown" @click.outside="exportDropdown = false" class="inline-flex items-center gap-2 px-3.5 py-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 text-xs font-semibold rounded-xl transition-all shadow-sm">
                    <i data-lucide="download" class="w-4 h-4 text-cyan-400"></i>
                    <span>Export Excel / CSV</span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5 text-slate-400"></i>
                </button>
                <div x-show="exportDropdown" x-cloak class="absolute right-0 mt-2 w-64 bg-slate-900 border border-slate-800 rounded-2xl shadow-2xl p-2 z-50 space-y-1"
                     x-transition:enter="transition ease-out duration-100"
                     x-transition:enter-start="opacity-0 scale-95"
                     x-transition:enter-end="opacity-100 scale-100">
                    <a href="{{ route('certifications.export-matrix', request()->query()) }}" class="flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-slate-800 text-left transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="file-spreadsheet" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Format Matriks Asli</p>
                            <p class="text-[11px] text-slate-400 leading-tight mt-0.5">1 Baris = 1 Pegawai dengan seluruh 50+ kolom sertifikasi (persis Excel asli)</p>
                        </div>
                    </a>
                    <a href="{{ route('certifications.export', request()->query()) }}" class="flex items-start gap-2.5 p-2.5 rounded-xl hover:bg-slate-800 text-left transition-colors">
                        <div class="w-8 h-8 rounded-lg bg-indigo-500/10 text-indigo-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                            <i data-lucide="table" class="w-4 h-4"></i>
                        </div>
                        <div>
                            <p class="text-xs font-bold text-white">Format Tabel Data Bersih</p>
                            <p class="text-[11px] text-slate-400 leading-tight mt-0.5">1 Baris = 1 Sertifikasi (Cocok untuk filter, pivot, dan re-import)</p>
                        </div>
                    </a>
                </div>
            </div>

            <!-- Import button -->
            <button @click="importModalOpen = true" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-emerald-600/20 transition-all">
                <i data-lucide="upload" class="w-4 h-4"></i>
                <span>Import Excel/CSV</span>
            </button>
            <!-- Add manual button -->
            <a href="{{ route('certifications.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                <i data-lucide="plus-circle" class="w-4 h-4"></i>
                <span>Tambah Sertifikasi</span>
            </a>
        </div>
    </div>


    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80">
        <form method="GET" action="{{ route('certifications.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari kata kunci (cth: an, ivan, 533...)"
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
                <input type="text" name="certificate_name" value="{{ $filters['certificate_name'] ?? '' }}"
                       placeholder="Filter spesifik nama sertifikasi..."
                       class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <select name="per_page" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="25" {{ request('per_page', 25) == 25 ? 'selected' : '' }}>25 baris/hal</option>
                    <option value="50" {{ request('per_page') == 50 ? 'selected' : '' }}>50 baris/hal</option>
                    <option value="100" {{ request('per_page') == 100 ? 'selected' : '' }}>100 baris/hal</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
                @if(!empty(array_filter($filters ?? [])))
                    <a href="{{ route('certifications.index') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>

        @if(!empty($filters['search']))
            <div class="mt-2.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-between text-xs text-indigo-300">
                <span>Hasil pencarian kata kunci <strong>"{{ $filters['search'] }}"</strong>: Menampilkan total <strong>{{ $certifications->total() }}</strong> sertifikat (Halaman {{ $certifications->currentPage() }} dari {{ $certifications->lastPage() }}).</span>
                <a href="{{ route('certifications.index') }}" class="text-xs text-rose-400 hover:underline">Hapus filter pencarian &times;</a>
            </div>
        @endif
    </div>

    <!-- Data Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                        <th class="py-3.5 px-4">Pegawai</th>
                        <th class="py-3.5 px-4">Unit</th>
                        <th class="py-3.5 px-4">Nama Sertifikasi</th>
                        <th class="py-3.5 px-4">Tanggal Expired</th>
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
                            <td class="py-3 px-4 font-bold text-xs {{ $cert->status === 'expired' ? 'text-rose-400' : ($cert->status === 'warning' ? 'text-amber-400' : 'text-slate-100') }}">
                                {{ $cert->expiry_date->format('d M Y') }}
                                <span class="block text-xs font-normal {{ $cert->status === 'expired' ? 'text-rose-400/90' : ($cert->status === 'warning' ? 'text-amber-400/90' : 'text-slate-400') }} mt-0.5">
                                    @if($cert->overridden_by_excel)
                                        {{ $cert->status === 'expired' ? 'Expired (Excel)' : ($cert->status === 'warning' ? 'Akan Expired (Excel)' : 'Valid (Excel)') }}
                                    @else
                                        {{ $days < 0 ? 'Lewat ' . abs($days) . ' hari' : ($days == 0 ? 'Hari ini' : 'Sisa ' . $days . ' hari') }}
                                    @endif
                                </span>
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
                                    <a href="{{ route('certifications.show', $cert) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Lihat Detail & Audit Log">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('certifications.edit', $cert) }}" class="p-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg border border-indigo-500/30 transition-colors" title="Perpanjang / Edit">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form method="POST" action="{{ route('certifications.destroy', $cert) }}" onsubmit="return confirm('Hapus sertifikasi ini secara permanen?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg border border-rose-500/30 transition-colors" title="Hapus">
                                            <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada data sertifikasi ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($certifications->hasPages())
            <div class="p-3 border-t border-slate-800/80 bg-slate-900/50">
                {{ $certifications->links() }}
            </div>
        @endif
    </div>

    <!-- Import Modal (Alpine.js) -->
    <div x-show="importModalOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95">
        
        <div class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl">
            <div class="flex items-center justify-between mb-4 pb-4 border-b border-slate-800">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center">
                        <i data-lucide="file-spreadsheet" class="w-5 h-5"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">Import Data Awal Excel / CSV</h4>
                        <p class="text-xs text-slate-400">Migrasi data sertifikasi yang sebelumnya ada di Excel</p>
                    </div>
                </div>
                <button @click="importModalOpen = false" class="text-slate-400 hover:text-white p-1">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('certifications.import') }}" enctype="multipart/form-data" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-2">Pilih File CSV / Excel Export</label>
                    <input type="file" name="file" accept=".csv,.txt" required
                           class="w-full text-xs text-slate-400 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-indigo-600 file:text-white hover:file:bg-indigo-500 bg-slate-800/80 rounded-xl border border-slate-700 p-2">
                </div>

                <div class="p-3.5 rounded-xl bg-slate-800/60 border border-slate-700/60 text-xs text-slate-300 space-y-2">
                    <div class="flex items-center justify-between">
                        <p class="font-bold text-indigo-300">Format Template CSV:</p>
                        <a href="{{ route('certifications.template') }}" class="inline-flex items-center gap-1 text-xs text-cyan-400 hover:text-cyan-300 font-semibold underline">
                            <i data-lucide="download" class="w-3.5 h-3.5"></i>
                            <span>Download Template CSV</span>
                        </a>
                    </div>
                    <p class="text-xs text-slate-400 font-mono bg-slate-900/80 p-2 rounded-lg border border-slate-800 break-all">
                        No Pegawai, Nama Pegawai, Email, Unit, Nama Sertifikasi, Tanggal Expired (YYYY-MM-DD)
                    </p>
                    <p class="text-xs text-slate-400">
                        &bull; File yang diekspor dari tombol <strong>Export Excel/CSV</strong> juga dapat langsung diunggah kembali ke sini tanpa ubah format.
                    </p>
                </div>

                <div class="flex items-center justify-end gap-2.5 pt-4 border-t border-slate-800">
                    <button type="button" @click="importModalOpen = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">
                        Batal
                    </button>
                    <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-lg shadow-emerald-600/30">
                        Unggah & Import Data
                    </button>
                </div>

            </form>
        </div>
    </div>
</div>
@endsection