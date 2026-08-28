<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::withCount('certifications');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('role')) {
            $query->where('role', $request->input('role'));
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->input('unit'));
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $users = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $roles = ['superadmin' => 'Superadmin', 'employee' => 'Pegawai'];
        $units = User::whereNotNull('unit')->distinct()->pluck('unit');

        return view('users.index', [
            'users' => $users,
            'roles' => $roles,
            'units' => $units,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('users.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => ['required', 'string', 'unique:users,employee_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'unit' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:superadmin,employee'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $validated['password'] = Hash::make($validated['password'] ?? 'password123');

        User::create($validated);

        return redirect()->route('users.index')->with('success', 'Akun pengguna berhasil dibuat.');
    }

    public function edit(User $user)
    {
        return view('users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'employee_number' => ['required', 'string', Rule::unique('users')->ignore($user->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'unit' => ['required', 'string', 'max:255'],
            'role' => ['required', 'in:superadmin,employee'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if ($request->user()->is($user) && $validated['role'] !== 'superadmin') {
            return back()->withErrors(['role' => 'Anda tidak dapat mengubah role akun sendiri.']);
        }

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return redirect()->route('users.show', $user)->with('success', 'Data akun pengguna berhasil diperbarui.');
    }

    public function show(User $user)
    {
        $user->load('certifications');
        return view('users.show', compact('user'));
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id) {
            return back()->withErrors(['error' => 'Anda tidak dapat menghapus akun Anda sendiri.']);
        }

        $name = $user->name;
        $user->delete();
        return redirect()->route('users.index')->with('success', "Akun pengguna {$name} berhasil dihapus.");
    }
}