@extends('layouts.app', ['title' => 'Manajemen Akun', 'header' => 'Manajemen Akun Pengguna', 'subtitle' => 'Kelola akses login, role, dan reset password pengguna'])

@section('content')
<div class="space-y-5">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3.5 bg-slate-900/60 p-4 rounded-2xl border border-slate-800/80 backdrop-blur-md">
        <div>
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i data-lucide="shield" class="w-5 h-5 text-indigo-400"></i>
                Daftar Akun Pengguna
            </h3>
            <p class="text-xs text-slate-400 mt-0.5">Atur akun superadmin dan pegawai yang dapat login ke dashboard.</p>
        </div>
        <a href="{{ route('users.create') }}" class="inline-flex items-center gap-2 px-3.5 py-2 bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-semibold rounded-xl shadow-md shadow-indigo-600/30 transition-all">
            <i data-lucide="user-plus" class="w-4 h-4"></i>
            <span>Tambah Akun</span>
        </a>
    </div>

    <div class="p-4 rounded-2xl bg-slate-900/80 border border-slate-800/80">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-2.5">
            <div class="lg:col-span-2 relative">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-slate-400">
                    <i data-lucide="search" class="w-4 h-4"></i>
                </div>
                <input type="text" name="search" value="{{ $filters['search'] ?? '' }}" placeholder="Cari nama, nomor pegawai, email..." autocomplete="off" class="w-full pl-10 pr-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-white placeholder-slate-500 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>

            <select name="role" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Role</option>
                @foreach($roles as $value => $label)
                    <option value="{{ $value }}" {{ ($filters['role'] ?? '') === $value ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>

            <select name="unit" onchange="this.form.submit()" class="w-full px-3 py-2 bg-slate-800/70 border border-slate-700/80 rounded-xl text-slate-200 text-xs focus:outline-none focus:ring-2 focus:ring-indigo-500">
                <option value="">Semua Unit</option>
                @foreach($units as $unit)
                    <option value="{{ $unit }}" {{ ($filters['unit'] ?? '') === $unit ? 'selected' : '' }}>{{ $unit }}</option>
                @endforeach
            </select>

            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 py-2 px-3.5 bg-indigo-600 hover:bg-indigo-500 text-white font-semibold rounded-xl text-xs flex items-center justify-center gap-1.5 transition-colors">
                    <i data-lucide="filter" class="w-3.5 h-3.5"></i>
                    <span>Cari</span>
                </button>
                @if(!empty(array_filter($filters ?? [])))
                    <a href="{{ route('users.index') }}" class="p-2 bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-400 hover:text-white rounded-xl text-xs" title="Reset Filter">
                        <i data-lucide="rotate-ccw" class="w-3.5 h-3.5"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <div class="bg-slate-900/80 border border-slate-800/80 rounded-2xl overflow-hidden shadow-xl backdrop-blur-md">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="border-b border-slate-800 text-xs font-bold text-slate-400 uppercase tracking-wider bg-slate-900/60">
                        <th class="py-3.5 px-4">Pengguna</th>
                        <th class="py-3.5 px-4">No. Pegawai</th>
                        <th class="py-3.5 px-4">Email</th>
                        <th class="py-3.5 px-4">Unit</th>
                        <th class="py-3.5 px-4">Role</th>
                        <th class="py-3.5 px-4">Sertifikasi</th>
                        <th class="py-3.5 px-4 text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $item)
                        <tr class="hover:bg-slate-800/40 transition-colors">
                            <td class="py-3 px-4 font-bold text-white">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-7 h-7 rounded-full bg-slate-800 border border-slate-700 text-indigo-400 flex items-center justify-center font-bold text-xs flex-shrink-0">
                                        {{ substr($item->name, 0, 1) }}
                                    </div>
                                    <span>{{ $item->name }}</span>
                                </div>
                            </td>
                            <td class="py-3 px-4 font-mono font-semibold text-indigo-300">{{ $item->employee_number ?? '-' }}</td>
                            <td class="py-3 px-4 text-slate-300">{{ $item->email }}</td>
                            <td class="py-3 px-4 text-slate-300">
                                <span class="px-2 py-0.5 rounded-lg bg-slate-800/80 border border-slate-700/60 font-medium">{{ $item->unit ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                @if($item->role === 'superadmin')
                                    <span class="px-2.5 py-0.5 rounded-full bg-indigo-500/10 text-indigo-400 border border-indigo-500/20 font-bold">Superadmin</span>
                                @else
                                    <span class="px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 font-bold">Pegawai</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-300">{{ $item->certifications_count }} sertifikat</td>
                            <td class="py-3 px-4 text-right">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('users.show', $item) }}" class="p-1.5 bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white rounded-lg border border-slate-700 transition-colors" title="Detail Akun">
                                        <i data-lucide="eye" class="w-3.5 h-3.5"></i>
                                    </a>
                                    <a href="{{ route('users.edit', $item) }}" class="p-1.5 bg-indigo-600/20 hover:bg-indigo-600 text-indigo-300 hover:text-white rounded-lg border border-indigo-500/30 transition-colors" title="Edit Akun">
                                        <i data-lucide="edit" class="w-3.5 h-3.5"></i>
                                    </a>
                                    @if(auth()->id() !== $item->id)
                                        <form method="POST" action="{{ route('users.destroy', $item) }}" onsubmit="return confirm('Hapus akun {{ $item->name }}?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded-lg border border-rose-500/30 transition-colors" title="Hapus Akun">
                                                <i data-lucide="trash-2" class="w-3.5 h-3.5"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-10 text-center text-slate-500">
                                <i data-lucide="users" class="w-8 h-8 mx-auto text-slate-600 mb-2 opacity-50"></i>
                                <p class="text-sm font-medium">Tidak ada akun pengguna ditemukan.</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-3 border-t border-slate-800/80 bg-slate-900/50">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
