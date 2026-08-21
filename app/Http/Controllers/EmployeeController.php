<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class EmployeeController extends Controller
{
    public function index(Request $request)
    {
        $query = User::where('role', 'employee')->withCount('certifications');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('employee_number', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit')) {
            $query->where('unit', $request->input('unit'));
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $employees = $query->orderBy('name')->paginate($perPage)->withQueryString();
        $units = User::whereNotNull('unit')->distinct()->pluck('unit');

        return view('employees.index', [
            'employees' => $employees,
            'units' => $units,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        return view('employees.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'employee_number' => ['required', 'string', 'unique:users,employee_number'],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email'],
            'unit' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        $validated['role'] = 'employee';
        $validated['password'] = Hash::make($validated['password'] ?? 'password');

        User::create($validated);

        return redirect()->route('employees.index')->with('success', 'Pegawai baru berhasil didaftarkan.');
    }

    public function show(User $employee)
    {
        $employee->load(['certifications.logs.actor']);
        return view('employees.show', compact('employee'));
    }

    public function edit(User $employee)
    {
        return view('employees.edit', compact('employee'));
    }

    public function update(Request $request, User $employee)
    {
        $validated = $request->validate([
            'employee_number' => ['required', 'string', Rule::unique('users')->ignore($employee->id)],
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', Rule::unique('users')->ignore($employee->id)],
            'unit' => ['required', 'string', 'max:255'],
            'password' => ['nullable', 'string', 'min:6'],
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);

        return redirect()->route('employees.show', $employee)->with('success', 'Data pegawai berhasil diperbarui.');
    }

    public function destroy(User $employee)
    {
        $name = $employee->name;
        $employee->delete();
        return redirect()->route('employees.index')->with('success', "Data pegawai {$name} berhasil dihapus.");
    }
}
