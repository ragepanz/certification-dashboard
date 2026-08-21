@extends('layouts.app', ['title' => 'Data Pegawai', 'header' => 'Manajemen Data Pegawai', 'subtitle' => 'Kelola data identitas pegawai dan riwayat sertifikasi'])

@section('content')
<div class="space-y-5">
    <!-- Action Header -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="users" class="w-5 h-5 text-indigo-400"></i>
                Daftar Pegawai
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Kelola data pegawai yang berhak memiliki sertifikasi dan menerima reminder.</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('employees.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
                <i data-lucide="user-plus" class="w-4 h-4"></i>
                <span>Tambah Pegawai Baru</span>
            </a>
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80">
        <form method="GET" action="{{ route('employees.index') }}" id="searchForm" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-2.5">
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}"
                       placeholder="Cari nama, no. pegawai, email (cth: iq, an)..."
                       autocomplete="off"
                       class="w-full pl-10 pr-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <select name="unit" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Unit Kerja</option>
                    @foreach($units as $u)
                        <option value="{{ $u }}" {{ ($filters['unit'] ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
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
                    <a href="{{ route('employees.index') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>

        @if(!empty($filters['search']))
            <div class="mt-2.5 px-3 py-1.5 rounded-lg bg-indigo-500/10 border border-indigo-500/20 flex items-center justify-between text-xs text-indigo-300">
                <span>Hasil pencarian kata kunci <strong>"{{ $filters['search'] }}"</strong>: Ditemukan <strong>{{ $employees->total() }}</strong> pegawai (Halaman {{ $employees->currentPage() }} dari {{ $employees->lastPage() }}).</span>
                <a href="{{ route('employees.index') }}" class="text-xs text-rose-400 hover:underline">Hapus filter pencarian &times;</a>
            </div>
        @endif
    </div>


    <!-- Data Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                        <th class="py-3.5 px-4">No. Pegawai</th>
                        <th class="py-3.5 px-4">Nama Lengkap</th>
                        <th class="py-3.5 px-4">Email Notifikasi</th>
                        <th class="py-3.5 px-4">Unit</th>
                        <th class="py-3.5 px-4">Total Sertifikasi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($employees as $emp)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-mono font-semibold text-indigo-300 text-xs">
                                {{ $emp->employee_number ?? '-' }}
                            </td>
                            <td class="py-3 px-4 font-bold text-white text-xs">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 text-indigo-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($emp->name, 0, 1) }}
                                    </div>
                                    <span>{{ $emp->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 text-slate-300 text-xs">
                                {{ $emp->email }}
                            </td>
                            <td class="py-3 px-4 text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800/80 border border-slate-700/60 text-xs font-medium">
                                    {{ $emp->unit ?? '-' }}
                                </span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold text-xs">
                                    {{ $emp->certifications_count }} sertifikat
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('employees.show', $emp) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Detail Pegawai">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('employees.edit', $emp) }}" class="p-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg border border-indigo-500/30 transition-colors" title="Edit Data">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <form method="POST" action="{{ route('employees.destroy', $emp) }}" onsubmit="return confirm('Hapus data pegawai ini beserta sertifikasinya?');">
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
                            <td colspan="6" class="py-10 text-center text-slate-500">
                                <i data-lucide="users" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada data pegawai ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($employees->hasPages())
            <div class="p-3 border-t border-slate-800/80 bg-slate-900/50">
                {{ $employees->links() }}
            </div>
        @endif
    </div>
</div>
@endsection