@extends('layouts.app', ['title' => 'Training Mandatory (Job Matrix)', 'header' => 'Training Mandatory & Masa Berlaku', 'subtitle' => 'Konfigurasi masa berlaku pelatihan dinas (2-Year vs Forever) berdasarkan Job Title pegawai'])

@section('content')
<div class="space-y-6 max-w-7xl mx-auto" x-data="{ createModal: false, editModal: false, editItem: {} }">
    
    <!-- Top Executive Summary Cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Total Training Mandatory</p>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalRules }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-indigo-500/10 text-indigo-400 flex items-center justify-center border border-indigo-500/20">
                    <i data-lucide="layers" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Aturan pelatihan dinas untuk {{ count($jobTitles) }} jabatan.</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-amber-400 uppercase tracking-wider">Berkala 2 Tahun (2-Year)</p>
                    <h3 class="text-2xl font-black text-amber-300 mt-1">{{ $twoYearCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center border border-amber-500/20">
                    <i data-lucide="refresh-cw" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Memiliki masa berlaku & memicu email reminder.</p>
        </div>

        <div class="p-5 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold text-emerald-400 uppercase tracking-wider">Permanen (Forever)</p>
                    <h3 class="text-2xl font-black text-emerald-300 mt-1">{{ $foreverCount }}</h3>
                </div>
                <div class="w-10 h-10 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center border border-emerald-500/20">
                    <i data-lucide="shield-check" class="w-5 h-5"></i>
                </div>
            </div>
            <p class="text-[11px] text-slate-400 mt-2">Sertifikat sekali ambil / tanpa batas kedaluwarsa.</p>
        </div>
    </div>

    <!-- Actions & Filter Card -->
    <div class="p-6 rounded-2xl bg-slate-900/60 border border-slate-800 backdrop-blur-xl shadow-xl space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i data-lucide="sliders" class="w-5 h-5 text-indigo-400"></i>
                    <span>Daftar Training Mandatory</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Kelola masa berlaku setiap jenis pelatihan kedinasan berdasarkan Job Title pegawai.</p>
            </div>
            <button @click="createModal = true" class="inline-flex items-center gap-2 px-4 py-2.5 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30 transition-all hover:scale-[1.02] active:scale-[0.98]">
                <i data-lucide="plus" class="w-4 h-4"></i>
                <span>Tambah Training Mandatory</span>
            </button>
        </div>

        <!-- Filter Form -->
        <form method="GET" action="{{ route('matrix.index') }}" class="grid grid-cols-1 sm:grid-cols-4 gap-3 pt-2">
            <div class="relative">
                <i data-lucide="search" class="w-4 h-4 absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400"></i>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari Jabatan / Modul..." class="w-full pl-10 pr-4 py-2 bg-slate-800/80 border border-slate-700 rounded-xl text-xs text-white placeholder-slate-400 focus:outline-none focus:border-indigo-500">
            </div>

            <div>
                <select name="job_title" class="w-full px-3 py-2 bg-slate-800/80 border border-slate-700 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="">-- Semua Jabatan (Job Title) --</option>
                    @foreach($jobTitles as $jt)
                        <option value="{{ $jt }}" {{ ($filters['job_title'] ?? '') === $jt ? 'selected' : '' }}>{{ $jt }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <select name="validity_type" class="w-full px-3 py-2 bg-slate-800/80 border border-slate-700 rounded-xl text-xs text-slate-200 focus:outline-none focus:border-indigo-500">
                    <option value="">-- Semua Masa Berlaku --</option>
                    <option value="2-Year" {{ ($filters['validity_type'] ?? '') === '2-Year' ? 'selected' : '' }}>2-Year (Berkala 2 Tahun)</option>
                    <option value="Forever" {{ ($filters['validity_type'] ?? '') === 'Forever' ? 'selected' : '' }}>Forever (Permanen)</option>
                </select>
            </div>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-slate-800 hover:bg-slate-700 text-white text-xs font-semibold rounded-xl border border-slate-700 transition-colors">
                    Filter
                </button>
                @if(!empty($filters))
                    <a href="{{ route('matrix.index') }}" class="px-3 py-2 bg-slate-800/50 hover:bg-slate-800 text-slate-400 hover:text-white text-xs rounded-xl border border-slate-700 transition-colors" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-4 h-4"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Table Card -->
    <div class="bg-slate-900/60 border border-slate-800 rounded-2xl overflow-hidden shadow-xl backdrop-blur-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-900/80">
                        <th class="py-3.5 px-5">Jabatan (Job Title)</th>
                        <th class="py-3.5 px-4">Nama Pelatihan (Training Type)</th>
                        <th class="py-3.5 px-4">Masa Berlaku (Validity)</th>
                        <th class="py-3.5 px-4">Status Syarat</th>
                        <th class="py-3.5 px-5 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($matrices as $item)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3.5 px-5 font-bold text-white">
                                {{ $item->job_title }}
                            </td>
                            <td class="py-3.5 px-4 font-semibold text-indigo-300">
                                {{ $item->training_name }}
                            </td>
                            <td class="py-3.5 px-4 font-bold">
                                @if($item->validity_type === '2-Year' && !$item->no_need_training)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-amber-500/15 text-amber-300 border border-amber-500/30">
                                        <i data-lucide="refresh-cw" class="w-3 h-3 mr-1"></i>
                                        2-Year (Berkala 2 Tahun)
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-lg text-xs font-bold bg-emerald-500/15 text-emerald-300 border border-emerald-500/30">
                                        <i data-lucide="shield" class="w-3 h-3 mr-1"></i>
                                        Forever (Permanen)
                                    </span>
                                @endif
                            </td>
                            <td class="py-3.5 px-4">
                                @if($item->no_need_training)
                                    <span class="text-slate-400 font-medium">Opsional / No Need</span>
                                @else
                                    <span class="text-slate-300 font-medium">Mandatory</span>
                                @endif
                            </td>
                            <td class="py-3.5 px-5 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <button @click="editItem = {{ json_encode($item) }}; editModal = true" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-indigo-400 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Edit Aturan">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </button>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="py-12 text-center text-slate-500">
                                <i data-lucide="inbox" class="w-8 h-8 mx-auto text-slate-600 mb-2"></i>
                                <p class="text-sm font-medium">Belum ada aturan matriks yang cocok dengan filter pencarian.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($matrices->hasPages())
            <div class="p-4 border-t border-slate-800 bg-slate-900/40">
                {{ $matrices->links() }}
            </div>
        @endif
    </div>

    <!-- Create Modal -->
    <div x-show="createModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.outside="createModal = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="plus-circle" class="w-4 h-4 text-indigo-400"></i>
                    <span>Tambah Training Mandatory</span>
                </h3>
                <button @click="createModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form method="POST" action="{{ route('matrix.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan (Job Title) *</label>
                    <input type="text" name="job_title" required placeholder="Contoh: Aircraft Cabin Controller" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pelatihan (Training Type) *</label>
                    <input type="text" name="training_name" required placeholder="Contoh: Safety Management System" class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Masa Berlaku (Validity) *</label>
                    <select name="validity_type" required class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="2-Year">2-Year (Berkala 2 Tahun)</option>
                        <option value="Forever" selected>Forever (Permanen)</option>
                    </select>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="createModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30">Simpan Aturan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div x-show="editModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/70 backdrop-blur-sm">
        <div @click.outside="editModal = false" class="w-full max-w-lg bg-slate-900 border border-slate-800 rounded-3xl p-6 shadow-2xl space-y-4">
            <div class="flex items-center justify-between pb-3 border-b border-slate-800">
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i data-lucide="edit" class="w-4 h-4 text-indigo-400"></i>
                    <span>Edit Masa Berlaku Pelatihan</span>
                </h3>
                <button @click="editModal = false" class="text-slate-400 hover:text-white">
                    <i data-lucide="x" class="w-4 h-4"></i>
                </button>
            </div>

            <form :action="'/settings/matrix/' + editItem.id" method="POST" class="space-y-4">
                @csrf
                @method('PUT')
                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Jabatan (Job Title)</label>
                    <p class="text-sm font-bold text-white" x-text="editItem.job_title"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-400 mb-1">Nama Pelatihan (Training Type)</label>
                    <p class="text-sm font-semibold text-indigo-300" x-text="editItem.training_name"></p>
                </div>

                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Masa Berlaku (Validity) *</label>
                    <select name="validity_type" x-model="editItem.validity_type" required class="w-full px-3 py-2 bg-slate-800 border border-slate-700 rounded-xl text-xs text-white focus:outline-none focus:border-indigo-500">
                        <option value="2-Year">2-Year (Berkala 2 Tahun)</option>
                        <option value="Forever">Forever (Permanen)</option>
                    </select>
                </div>

                <div class="pt-2 flex justify-end gap-2">
                    <button type="button" @click="editModal = false" class="px-4 py-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold rounded-xl">Batal</button>
                    <button type="submit" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold rounded-xl shadow-lg shadow-indigo-600/30">Perbarui Aturan</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
