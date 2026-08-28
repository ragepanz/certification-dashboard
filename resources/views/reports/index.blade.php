@extends('layouts.app', ['title' => 'Laporan & Export Sertifikasi', 'header' => 'Laporan Sertifikasi Pegawai', 'subtitle' => 'Filter, cetak, dan ekspor data masa berlaku sertifikasi ke CSV / Excel'])

@section('content')
<div class="space-y-5">
    <!-- Header Summary & Export Tools -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-3.5 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="file-bar-chart" class="w-5 h-5 text-indigo-400"></i>
                Pusat Laporan & Ekspor Data Sertifikasi
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Ditemukan <strong class="text-white font-bold">{{ $certifications->count() }}</strong> sertifikasi berdasarkan parameter filter.</p>
        </div>

        <div class="flex items-center gap-2.5">
            <a href="{{ route('reports.export-csv', request()->query()) }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-emerald-600/25 transition-all">
                <i data-lucide="download" class="w-4 h-4"></i>
                <span>Download File CSV</span>
            </a>
            <a href="{{ route('reports.print', request()->query()) }}" target="_blank" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/25 transition-all">
                <i data-lucide="printer" class="w-4 h-4"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Configuration -->
    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80">
        <form method="GET" action="{{ route('reports.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">
            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Unit Kerja</label>
                <select name="unit" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Unit</option>
                    @foreach($units as $u)
                        <option value="{{ $u }}" {{ ($filters['unit'] ?? '') === $u ? 'selected' : '' }}>{{ $u }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Status</label>
                <select name="status" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    <option value="">Semua Status</option>
                    <option value="active" {{ ($filters['status'] ?? '') === 'active' ? 'selected' : '' }}>Aktif (> 30 hari)</option>
                    <option value="warning" {{ ($filters['status'] ?? '') === 'warning' ? 'selected' : '' }}>Akan Expired (≤ 30 hari)</option>
                    <option value="expired" {{ ($filters['status'] ?? '') === 'expired' ? 'selected' : '' }}>Expired (Lewat Waktu)</option>
                </select>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Dari Expired</label>
                <input type="date" name="from_date" value="{{ $filters['from_date'] ?? '' }}"
                       class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Sampai Expired</label>
                <input type="date" name="to_date" value="{{ $filters['to_date'] ?? '' }}"
                       class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Terapkan</span>
                </button>
                @if(!empty(array_filter($filters ?? [])))
                    <a href="{{ route('reports.index') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Report Preview Table -->
    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="p-4 border-b border-slate-800/80 bg-slate-900/50 flex items-center justify-between">
            <h4 class="text-sm font-bold text-white flex items-center gap-2">
                <i data-lucide="table" class="w-4 h-4 text-indigo-400"></i>
                Pratinjau Laporan Data Sertifikasi ({{ $certifications->count() }} Data)
            </h4>
        </div>

        <div class="overflow-x-auto max-h-[550px] overflow-y-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead class="sticky top-0 bg-slate-900 shadow-md">
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider">
                        <th class="py-3.5 px-4">No.</th>
                        <th class="py-3.5 px-4">No. Pegawai</th>
                        <th class="py-3.5 px-4">Nama Pegawai</th>
                        <th class="py-3.5 px-4">Unit</th>
                        <th class="py-3.5 px-4">Nama Sertifikasi</th>
                        <th class="py-3.5 px-4">Tanggal Expired</th>
                        <th class="py-3.5 px-4">Sisa Waktu</th>
                        <th class="py-3.5 px-4">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($certifications as $index => $cert)
                        @php
                            $days = $cert->days_remaining;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 text-slate-400 text-xs">{{ $index + 1 }}</td>
                            <td class="py-3 px-4 font-mono font-semibold text-indigo-300 text-xs">{{ $cert->user->employee_number ?? '-' }}</td>
                            <td class="py-3 px-4 font-bold text-white text-xs">{{ $cert->user->name ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-300 text-xs">{{ $cert->user->unit ?? '-' }}</td>
                            <td class="py-3 px-4 font-semibold text-slate-200 text-xs">{{ $cert->certificate_name }}</td>
                            <td class="py-3 px-4 text-slate-300 text-xs">{{ $cert->expiry_date->format('d M Y') }}</td>
                            <td class="py-3 px-4 text-xs">
                                @if($cert->overridden_by_excel)
                                    @if($cert->status === 'active')
                                        <span class="text-emerald-400 font-bold">Valid (Excel)</span>
                                    @elseif($cert->status === 'warning')
                                        <span class="text-amber-400 font-bold">Akan Expired (Excel)</span>
                                    @else
                                        <span class="text-rose-400 font-bold">Expired (Excel)</span>
                                    @endif
                                @elseif($days < 0)
                                    <span class="text-rose-400 font-bold">Lewat {{ abs($days) }} hari</span>
                                @elseif($days == 0)
                                    <span class="text-rose-400 font-bold">Hari ini</span>
                                @elseif($days <= 30)
                                    <span class="text-amber-400 font-bold">Sisa {{ $days }} hari</span>
                                @else
                                    <span class="text-slate-400">Sisa {{ $days }} hari</span>
                                @endif
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
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-10 text-center text-slate-500">
                                <i data-lucide="file-x" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada data sertifikasi yang cocok dengan filter yang dipilih.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection