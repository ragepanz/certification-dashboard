<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    public function index(Request $request)
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

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'expired') {
                $query->whereDate('expiry_date', '<', $today);
            } elseif ($status === 'warning') {
                $query->whereDate('expiry_date', '>=', $today)
                      ->whereDate('expiry_date', '<=', $today->copy()->addDays(60));
            } elseif ($status === 'active') {
                $query->whereDate('expiry_date', '>', $today->copy()->addDays(60));
            }
        }

        $certifications = $query->orderBy('expiry_date', 'asc')->paginate(20)->withQueryString();

        $units = User::whereNotNull('unit')->distinct()->pluck('unit');
        $certificateNames = Certification::distinct()->pluck('certificate_name');

        return view('reports.index', [
            'certifications' => $certifications,
            'units' => $units,
            'certificateNames' => $certificateNames,
            'filters' => $request->all(),
        ]);
    }

    public function exportCsv(Request $request): StreamedResponse
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

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'expired') {
                $query->whereDate('expiry_date', '<', $today);
            } elseif ($status === 'warning') {
                $query->whereDate('expiry_date', '>=', $today)
                      ->whereDate('expiry_date', '<=', $today->copy()->addDays(60));
            } elseif ($status === 'active') {
                $query->whereDate('expiry_date', '>', $today->copy()->addDays(60));
            }
        }

        $certifications = $query->orderBy('expiry_date', 'asc')->get();

        $filename = 'laporan_sertifikasi_' . date('Ymd_His') . '.csv';

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename=\"{$filename}\"",
        ];

        return response()->stream(function () use ($certifications) {
            $file = fopen('php://output', 'w');
            // Write UTF-8 BOM for Excel compatibility
            fputs($file, "\xEF\xBB\xBF");

            // Header row
            fputcsv($file, [
                'No. Pegawai',
                'Nama Pegawai',
                'Email',
                'Unit Kerja',
                'Nama Sertifikasi',
                'Tanggal Terbit',
                'Tanggal Expired',
                'Sisa Hari',
                'Status',
            ], ';');

            foreach ($certifications as $cert) {
                fputcsv($file, [
                    $cert->user->employee_number ?? '-',
                    $cert->user->name ?? '-',
                    $cert->user->email ?? '-',
                    $cert->user->unit ?? '-',
                    $cert->certificate_name,
                    $cert->issue_date->format('Y-m-d'),
                    $cert->expiry_date->format('Y-m-d'),
                    $cert->days_remaining,
                    $cert->status === 'expired' ? 'Expired' : ($cert->status === 'warning' ? 'Akan Expired' : 'Aktif'),
                ], ';');
            }

            fclose($file);
        }, 200, $headers);
    }

    public function printView(Request $request)
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

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'expired') {
                $query->whereDate('expiry_date', '<', $today);
            } elseif ($status === 'warning') {
                $query->whereDate('expiry_date', '>=', $today)
                      ->whereDate('expiry_date', '<=', $today->copy()->addDays(60));
            } elseif ($status === 'active') {
                $query->whereDate('expiry_date', '>', $today->copy()->addDays(60));
            }
        }


        $certifications = $query->orderBy('expiry_date', 'asc')->get();

        return view('reports.print', [
            'certifications' => $certifications,
            'generatedAt' => now()->format('d M Y H:i:s'),
        ]);
    }
}
