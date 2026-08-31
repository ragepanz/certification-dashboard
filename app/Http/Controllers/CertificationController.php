<?php

namespace App\Http\Controllers;

use App\Mail\CertificationReminderMail;
use App\Models\Certification;
use App\Models\CertificationLog;
use App\Models\ReminderLog;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\StreamedResponse;



class CertificationController extends Controller
{
    public function index(Request $request)
    {
        $query = Certification::with(['user', 'logs.actor']);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('unit')) {
            $unit = $request->input('unit');
            $query->whereHas('user', function ($uq) use ($unit) {
                $uq->where('unit', $unit);
            });
        }

        if ($request->filled('certificate_name')) {
            $query->where('certificate_name', $request->input('certificate_name'));
        }

        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        $certifications = $query->orderBy('expiry_date', 'asc')->paginate($perPage)->withQueryString();

        $units = User::whereNotNull('unit')->distinct()->pluck('unit');
        $certificateNames = Certification::distinct()->pluck('certificate_name');

        return view('certifications.index', [
            'certifications' => $certifications,
            'units' => $units,
            'certificateNames' => $certificateNames,
            'filters' => $request->all(),
        ]);
    }

    public function create()
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('certifications.create', compact('employees'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => ['required', 'exists:users,id'],
            'certificate_name' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        if ($request->hasFile('certificate_file')) {
            $validated['certificate_file'] = $request->file('certificate_file')->store('certificates', 'public');
        }

        $cert = Certification::create($validated);

        return redirect()->route('certifications.index')->with('success', "Sertifikasi '{$cert->certificate_name}' berhasil ditambahkan.");
    }

    public function show(Certification $certification)
    {
        $certification->load(['user', 'logs.actor', 'reminderLogs']);
        return view('certifications.show', compact('certification'));
    }

    public function edit(Certification $certification)
    {
        $employees = User::where('role', 'employee')->orderBy('name')->get();
        return view('certifications.edit', compact('certification', 'employees'));
    }

    public function update(Request $request, Certification $certification)
    {
        $validated = $request->validate([
            'certificate_name' => ['required', 'string', 'max:255'],
            'expiry_date' => ['required', 'date'],
            'renewal_notes' => ['nullable', 'string', 'max:1000'],
            'certificate_file' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:10240'],
        ]);

        $oldExpiry = $certification->expiry_date->format('Y-m-d');
        $newExpiry = Carbon::parse($validated['expiry_date'])->format('Y-m-d');

        if ($request->hasFile('certificate_file')) {
            // Delete old file if exists
            if ($certification->certificate_file && Storage::disk('public')->exists($certification->certificate_file)) {
                Storage::disk('public')->delete($certification->certificate_file);
            }
            $validated['certificate_file'] = $request->file('certificate_file')->store('certificates', 'public');
        }

        DB::transaction(function () use ($certification, $validated, $oldExpiry, $newExpiry) {
            $updateData = [
                'certificate_name' => $validated['certificate_name'],
                'expiry_date' => $validated['expiry_date'],
            ];

            if (isset($validated['certificate_file'])) {
                $updateData['certificate_file'] = $validated['certificate_file'];
            }

            // Update certificate record
            $certification->update($updateData);

            // If expiry date changed, write an audit log
            if ($oldExpiry !== $newExpiry) {
                CertificationLog::create([
                    'certification_id' => $certification->id,
                    'user_id' => Auth::id(),
                    'old_expiry_date' => $oldExpiry,
                    'new_expiry_date' => $newExpiry,
                    'notes' => $validated['renewal_notes'] ?? 'Perbaruan tanggal expired sertifikasi.',
                ]);
            }
        });

        return redirect()->route('certifications.show', $certification)->with('success', 'Data sertifikasi dan riwayat audit berhasil diperbarui.');
    }

    public function destroy(Certification $certification)
    {
        $name = $certification->certificate_name;
        if ($certification->certificate_file && Storage::disk('public')->exists($certification->certificate_file)) {
            Storage::disk('public')->delete($certification->certificate_file);
        }
        $certification->delete();
        return redirect()->route('certifications.index')->with('success', "Sertifikasi '{$name}' berhasil dihapus.");
    }

    /**
     * Trigger manual email reminder for test/urgent cases (Queued)
     */
    public function sendReminder(Certification $certification, Request $request)
    {
        $user = $certification->user;
        $days = $certification->days_remaining;

        $type = 'H-5';
        if ($days < 0) {
            $type = 'H+5';
        } elseif ($days > 30) {
            $type = 'H-60';
        } elseif ($days > 5) {
            $type = 'H-30';
        }

        try {
            Mail::to($user->email)->queue(new CertificationReminderMail($certification, $type));

            ReminderLog::create([
                'certification_id' => $certification->id,
                'type' => $type,
                'recipient_email' => $user->email,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            return back()->with('success', "Reminder {$type} berhasil dimasukkan ke antrean pengiriman email untuk {$user->email}.");
        } catch (\Exception $e) {
            ReminderLog::create([
                'certification_id' => $certification->id,
                'type' => $type,
                'recipient_email' => $user->email,
                'status' => 'failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            return back()->withErrors(['error' => "Gagal mengirim email: {$e->getMessage()}"]);
        }
    }

    /**
     * Download standard CSV/Excel template for bulk import
     */
    public function downloadTemplate(): StreamedResponse
    {
        $filename = 'template_import_sertifikasi.csv';
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel

            // Header row
            fputcsv($file, [
                'No Pegawai',
                'Nama Pegawai',
                'Email',
                'Unit',
                'Nama Sertifikasi',
                'Tanggal Expired (YYYY-MM-DD)',
            ]);

            // Sample rows matching import format
            fputcsv($file, [
                '533380',
                'DIAHYANI PUTRI',
                'diahyani.putri@gmf-aeroasia.co.id',
                'JKTTLF',
                'Human Factor',
                '2024-07-11',
            ]);
            fputcsv($file, [
                '580791',
                'TAUFIQ HIDAYAT',
                'taufiq.hidayat@gmf-aeroasia.co.id',
                'JKTTLF-5',
                'Safety Management System',
                '2025-01-15',
            ]);

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Export all/filtered certifications to CSV compatible with Excel (Model Tabel Bersih)
     */
    public function export(Request $request): StreamedResponse
    {
        $today = Carbon::today();
        $query = Certification::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('employee_number', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('unit')) {
            $unit = $request->input('unit');
            $query->whereHas('user', function ($uq) use ($unit) {
                $uq->where('unit', $unit);
            });
        }

        if ($request->filled('certificate_name')) {
            $query->where('certificate_name', $request->input('certificate_name'));
        }

        $certifications = $query->orderBy('expiry_date', 'asc')->get();
        $filename = 'data_sertifikasi_tabel_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($certifications) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel

            // Same header structure as the import template for seamless round-trip
            fputcsv($file, [
                'No Pegawai',
                'Nama Pegawai',
                'Email',
                'Unit',
                'Nama Sertifikasi',
                'Tanggal Expired',
                'Sisa Hari',
                'Status',
            ], ';'); // Semicolon delimiter for clean column separation in Excel

            foreach ($certifications as $cert) {
                fputcsv($file, [
                    $cert->user->employee_number ?? '-',
                    $cert->user->name ?? '-',
                    $cert->user->job_title ?? '-',
                    $cert->user->email ?? '-',
                    $cert->user->unit ?? '-',
                    $cert->certificate_name,
                    $cert->expiry_date ? $cert->expiry_date->format('Y-m-d') : '-',
                    $cert->days_remaining ?? '-',
                    $cert->status === 'expired' ? 'Expired' : ($cert->status === 'warning' ? 'Akan Expired' : 'Aktif'),
                ], ';');
            }

            fclose($file);
        }, 200, $headers);
    }

    /**
     * Export in Original Excel Matrix Format (1 Row per Employee with all 50+ training columns)
     */
    public function exportMatrix(Request $request): StreamedResponse
    {
        $query = User::where('role', 'employee')->with('certifications');

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

        $employees = $query->orderBy('name')->get();

        // Paired training modules (Date Column + Status Column)
        $pairedModules = [
            'Human Factor' => 'HF Status',
            'Safety Management System' => 'SMS Status',
            'GMF Quality System' => 'QS Status',
            'CASR Part 145' => 'CASR Status',
            'FAR Part 145' => 'FAR Status',
            'EASA Part 145' => 'EASA Status',
            'EASA Part M' => 'Part M Status',
            'Fuel Tank Safety' => 'FTS Status',
            'EWIS' => 'EWIS Status',
            'Dangerous Good' => 'DG Status',
        ];

        // Single date permanent modules (Column 28 to 67)
        $singleModules = [
            'ADTH', 'Basic Inspection', 'Basic Supervisory Training', 'Certifying Staff',
            'Fundamental Troubleshooting', 'Training of Trainer', 'Basic Planning',
            'Basic Engineering', 'Aircraft Familiarization', 'GMF Logistic Business',
            'Warehouse Management', 'Material Handling', 'Aircraft Exterior & Interior Cleaning',
            'GSE Type Rating', 'Aviation Legislation Module 10', 'Leadership Skill Training',
            'MRO Management', 'MRO Finance', 'Project Management basic', 'OLP',
            'Office Administration', 'Basic Accounting Module', 'Service Excelent',
            'Management for Secretary', 'RII', 'Lead Auditor Course', 'SWIFT Training',
            'IFE', 'Orientation Training', 'BCT', 'BAM', 'BATK', 'GAK',
            'Type Rating Level III Training', '1', '2', '3',
            'Simplified Technical English', 'Radio Telephony', 'B737 BCF',
        ];

        $filename = 'Training_Dinas_TN_Export_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($employees, $pairedModules, $singleModules) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM for Microsoft Excel

            // Build Exact Header Row matching Training Dinas TN.xlsx
            $headerRow = [
                'No',
                'Emp ID',
                'Name',
                'Job Title',
                'Unit',
            ];

            foreach ($pairedModules as $tName => $sName) {
                $headerRow[] = $tName;
                $headerRow[] = $sName;
            }

            foreach ($singleModules as $sMod) {
                $headerRow[] = $sMod;
            }

            fputcsv($file, $headerRow, ';');

            foreach ($employees as $idx => $emp) {
                $empCerts = $emp->certifications->keyBy('certificate_name');

                $row = [
                    $idx + 1,
                    $emp->employee_number ?? '-',
                    $emp->name,
                    $emp->job_title ?? '-',
                    $emp->unit ?? '-',
                ];

                // Paired columns: Date + Status
                foreach ($pairedModules as $tName => $sName) {
                    if (isset($empCerts[$tName])) {
                        $c = $empCerts[$tName];
                        $dateStr = $c->expiry_date ? $c->expiry_date->format('d/m/Y') : ($c->issue_date ? $c->issue_date->format('d/m/Y') : '');
                        $statusStr = $c->excel_status ? ucfirst($c->excel_status) : ($c->status === 'expired' ? 'Expired' : ($c->status === 'warning' ? 'Expiring' : 'Valid'));
                        $row[] = $dateStr;
                        $row[] = $statusStr;
                    } else {
                        $row[] = '';
                        $row[] = '';
                    }
                }

                // Single date modules
                foreach ($singleModules as $sMod) {
                    if (isset($empCerts[$sMod])) {
                        $c = $empCerts[$sMod];
                        $dateStr = $c->expiry_date ? $c->expiry_date->format('d/m/Y') : ($c->issue_date ? $c->issue_date->format('d/m/Y') : '');
                        $row[] = $dateStr;
                    } else {
                        $row[] = '';
                    }
                }

                fputcsv($file, $row, ';');
            }

            fclose($file);
        }, 200, $headers);
    }


    /**
     * Import data from CSV / Excel format
     */
    public function import(Request $request)
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:csv,txt'],
        ]);

        $file = $request->file('file');
        $handle = fopen($file->getRealPath(), 'r');
        $header = fgetcsv($handle, 1000, ',');

        $successCount = 0;
        $errors = [];

        DB::beginTransaction();
        try {
            while (($row = fgetcsv($handle, 1000, ',')) !== false) {
                if (count($row) < 6) continue;

                $empNumber = trim($row[0]);
                $empName = trim($row[1]);
                $empEmail = trim($row[2]);
                $empUnit = trim($row[3]);
                $certName = trim($row[4]);
                $expiryDate = trim($row[5]);

                if (!$empNumber || !$certName || !$expiryDate) continue;

                // Fallback email if empty
                if (!$empEmail) {
                    $cleanName = strtolower(preg_replace('/[^a-zA-Z0-9]/', '.', $empName));
                    $empEmail = "{$cleanName}.{$empNumber}@gmf-aeroasia.co.id";
                }

                // Find or create employee
                $user = User::firstOrCreate(
                    ['employee_number' => $empNumber],
                    [
                        'email' => $empEmail,
                        'name' => $empName,
                        'unit' => $empUnit ?: 'TN',
                        'role' => 'employee',
                        'password' => bcrypt('password'),
                    ]
                );

                // Check and parse dates - only expiry date is required
                $parsedExpiry = null;

                try {
                    $parsedExpiry = $expiryDate ? Carbon::parse($expiryDate)->format('Y-m-d') : null;
                } catch (\Exception $de) {
                    continue;
                }

                // Create or update certification
                Certification::updateOrCreate(
                    [
                        'user_id' => $user->id,
                        'certificate_name' => $certName,
                    ],
                    [
                        'expiry_date' => $parsedExpiry,
                    ]
                );

                $successCount++;
            }
            DB::commit();
            fclose($handle);

            return redirect()->route('certifications.index')->with('success', "Berhasil memproses dan mengimpor {$successCount} data sertifikasi.");
        } catch (\Exception $e) {
            DB::rollBack();
            fclose($handle);
            return back()->withErrors(['file' => 'Format file tidak valid atau terjadi error: ' . $e->getMessage()]);
        }
    }
}

