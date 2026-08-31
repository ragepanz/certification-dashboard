<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

// Helper: sertifikat tanpa expiry_date (null) tetap dihitung 'active' (tidak berakhir)

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $today = Carbon::today();

        // JIKA PEGAWAI: Tampilkan dashboard / sertifikasi pribadi
        if ($user->isEmployee()) {
            $myCertifications = Certification::where('user_id', $user->id)
                ->orderBy('expiry_date', 'asc')
                ->get();

            $totalMyCerts = $myCertifications->count();
            $myActive = $myCertifications->where('status', 'active')->count();
            $myWarning = $myCertifications->where('status', 'warning')->count();
            $myExpired = $myCertifications->where('status', 'expired')->count();

            return view('dashboard.employee', [
                'user' => $user,
                'certifications' => $myCertifications,
                'total' => $totalMyCerts,
                'active' => $myActive,
                'warning' => $myWarning,
                'expired' => $myExpired,
            ]);
        }

        // JIKA SUPERADMIN (LCU): Tampilkan Monitoring Dashboard Lengkap
        // 1. KPI Metrics
        $totalEmployees = User::where('role', 'employee')->count();
        $totalCertifications = Certification::count();

        $expiringCount = Certification::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate('expiry_date', '<=', $today->copy()->addDays(60))
            ->count();

        $expiredCount = Certification::whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->count();

        $activeCount = Certification::where(function ($q) use ($today) {
            $q->whereNull('expiry_date')
              ->orWhereDate('expiry_date', '>', $today->copy()->addDays(60));
        })->count();

        $permanentCount = Certification::whereNull('expiry_date')->count();

        // 2. Filter Parameters
        $query = Certification::with('user');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('certificate_name', 'like', "%{$search}%")
                  ->orWhereHas('user', function ($uq) use ($search) {
                      $uq->where('name', 'like', "%{$search}%")
                         ->orWhere('employee_number', 'like', "%{$search}%")
                         ->orWhere('unit', 'like', "%{$search}%")
                         ->orWhere('job_title', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('unit')) {
            $query->whereHas('user', function ($uq) use ($request) {
                $uq->where('unit', $request->input('unit'));
            });
        }

        if ($request->filled('certificate_name')) {
            $query->where('certificate_name', $request->input('certificate_name'));
        }

        if ($request->filled('status')) {
            $status = $request->input('status');
            if ($status === 'expired') {
                $query->whereNotNull('expiry_date')
                      ->whereDate('expiry_date', '<', $today);
            } elseif ($status === 'warning') {
                $query->whereNotNull('expiry_date')
                      ->whereDate('expiry_date', '>=', $today)
                      ->whereDate('expiry_date', '<=', $today->copy()->addDays(60));
            } elseif ($status === 'active') {
                $query->where(function ($q) use ($today) {
                    $q->whereNull('expiry_date')
                      ->orWhereDate('expiry_date', '>', $today->copy()->addDays(60));
                });
            } elseif ($status === 'permanent') {
                $query->whereNull('expiry_date');
            }
        }


        $perPage = (int) $request->input('per_page', 25);
        if (!in_array($perPage, [10, 25, 50, 100])) {
            $perPage = 25;
        }

        // Urutkan prioritas: Expired (1) -> Expiring/Warning (2) -> Aktif (3) -> Tanggal terdekat
        $certifications = $query->orderByRaw("
            CASE 
                WHEN expiry_date IS NOT NULL AND expiry_date < '{$today->format('Y-m-d')}' THEN 1
                WHEN expiry_date IS NOT NULL AND expiry_date >= '{$today->format('Y-m-d')}' AND expiry_date <= '{$today->copy()->addDays(60)->format('Y-m-d')}' THEN 2
                ELSE 3
            END ASC,
            expiry_date ASC
        ")->paginate($perPage)->withQueryString();

        // Distinct lists for filter dropdowns
        $units = User::whereNotNull('unit')->distinct()->pluck('unit');
        $certificateNames = Certification::distinct()->pluck('certificate_name');

        // 3. Breakdown per Jenis Sertifikasi / Training Module
        $certificateTypes = Certification::select('certificate_name')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN expiry_date IS NULL OR expiry_date > ? THEN 1 ELSE 0 END) as active_count", [$today->copy()->addDays(60)])
            ->selectRaw("SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date >= ? AND expiry_date <= ? THEN 1 ELSE 0 END) as warning_count", [$today, $today->copy()->addDays(60)])
            ->selectRaw("SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date < ? THEN 1 ELSE 0 END) as expired_count", [$today])
            ->groupBy('certificate_name')
            ->orderBy('total_count', 'desc')
            ->get();

        // Recent Audit logs for quick glance widget
        $recentLogs = \App\Models\CertificationLog::with(['certification', 'actor'])
            ->latest()
            ->take(5)
            ->get();

        return view('dashboard.admin', [
            'totalEmployees' => $totalEmployees,
            'totalCertifications' => $totalCertifications,
            'expiringCount' => $expiringCount,
            'expiredCount' => $expiredCount,
            'activeCount' => $activeCount,
            'permanentCount' => $permanentCount,
            'certifications' => $certifications,
            'units' => $units,
            'certificateNames' => $certificateNames,
            'certificateTypes' => $certificateTypes,
            'recentLogs' => $recentLogs,
            'filters' => $request->all(),
        ]);
    }
}

