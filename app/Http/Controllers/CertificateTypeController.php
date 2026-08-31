<?php

namespace App\Http\Controllers;

use App\Models\Certification;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;

class CertificateTypeController extends Controller
{
    /**
     * Display all Certificate Types with statistics
     */
    public function index(Request $request)
    {
        $today = Carbon::today();
        $query = Certification::select('certificate_name')
            ->selectRaw('COUNT(*) as total_count')
            ->selectRaw("SUM(CASE WHEN expiry_date IS NULL OR expiry_date > ? THEN 1 ELSE 0 END) as active_count", [$today->copy()->addDays(60)])
            ->selectRaw("SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date >= ? AND expiry_date <= ? THEN 1 ELSE 0 END) as warning_count", [$today, $today->copy()->addDays(60)])
            ->selectRaw("SUM(CASE WHEN expiry_date IS NOT NULL AND expiry_date < ? THEN 1 ELSE 0 END) as expired_count", [$today])
            ->groupBy('certificate_name');

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('certificate_name', 'like', "%{$search}%");
        }

        $certificateTypes = $query->orderBy('total_count', 'desc')->get();

        $totalModules = $certificateTypes->count();
        $totalHolders = $certificateTypes->sum('total_count');

        return view('certificate_types.index', [
            'certificateTypes' => $certificateTypes,
            'totalModules' => $totalModules,
            'totalHolders' => $totalHolders,
            'search' => $request->input('search', ''),
        ]);
    }

    /**
     * Show detail of a specific certificate type and its holders
     */
    public function show($name, Request $request)
    {
        $today = Carbon::today();
        $certificateName = urldecode($name);

        $query = Certification::with('user')
            ->where('certificate_name', $certificateName);

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->whereHas('user', function ($uq) use ($search) {
                $uq->where('name', 'like', "%{$search}%")
                   ->orWhere('employee_number', 'like', "%{$search}%");
            });
        }

        if ($request->filled('unit')) {
            $unit = $request->input('unit');
            $query->whereHas('user', function ($uq) use ($unit) {
                $uq->where('unit', $unit);
            });
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
            }
        }

        $certifications = $query->orderByRaw("
            CASE 
                WHEN expiry_date IS NOT NULL AND expiry_date < '{$today->format('Y-m-d')}' THEN 1
                WHEN expiry_date IS NOT NULL AND expiry_date >= '{$today->format('Y-m-d')}' AND expiry_date <= '{$today->copy()->addDays(60)->format('Y-m-d')}' THEN 2
                ELSE 3
            END ASC,
            expiry_date ASC
        ")->paginate(25)->withQueryString();

        $units = User::whereNotNull('unit')->distinct()->pluck('unit');

        $activeCount = Certification::where('certificate_name', $certificateName)->where(function ($q) use ($today) {
            $q->whereNull('expiry_date')
              ->orWhereDate('expiry_date', '>', $today->copy()->addDays(60));
        })->count();

        $warningCount = Certification::where('certificate_name', $certificateName)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '>=', $today)
            ->whereDate('expiry_date', '<=', $today->copy()->addDays(60))
            ->count();

        $expiredCount = Certification::where('certificate_name', $certificateName)
            ->whereNotNull('expiry_date')
            ->whereDate('expiry_date', '<', $today)
            ->count();

        return view('certificate_types.show', [
            'certificateName' => $certificateName,
            'certifications' => $certifications,
            'units' => $units,
            'activeCount' => $activeCount,
            'warningCount' => $warningCount,
            'expiredCount' => $expiredCount,
            'totalCount' => $certifications->total(),
            'filters' => $request->all(),
        ]);
    }
}

