<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Laporan Masa Berlaku Sertifikasi Pegawai - LCU</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; color: #1e293b; margin: 20px; line-height: 1.5; }
        .header { text-align: center; border-bottom: 2px solid #0f172a; padding-bottom: 15px; margin-bottom: 20px; }
        .header h1 { margin: 0; font-size: 18px; text-transform: uppercase; color: #0f172a; }
        .header p { margin: 4px 0 0; font-size: 12px; color: #64748b; }
        .filter-info { margin-bottom: 15px; font-size: 11px; color: #475569; background: #f8fafc; padding: 8px 12px; border-radius: 4px; border: 1px solid #e2e8f0; }
        table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        th, td { border: 1px solid #cbd5e1; padding: 8px 10px; text-align: left; }
        th { background-color: #f1f5f9; font-weight: bold; font-size: 11px; text-transform: uppercase; color: #334155; }
        .status-expired { color: #dc2626; font-weight: bold; }
        .status-warning { color: #d97706; font-weight: bold; }
        .status-active { color: #16a34a; }
        .no-print { margin-bottom: 20px; }
        .btn-print { background: #4f46e5; color: white; border: none; padding: 8px 16px; border-radius: 6px; cursor: pointer; font-weight: bold; }
        @media print {
            .no-print { display: none; }
            body { margin: 0; }
        }
    </style>
</head>
<body>
    <div class="no-print">
        <button onclick="window.print()" class="btn-print">🖨️ Cetak Dokumen / Simpan ke PDF</button>
    </div>

    <div class="header">
        <h1>Learning Center Unit (LCU)</h1>
        <p>Laporan Monitoring Masa Berlaku Sertifikasi Pegawai</p>
        <p style="font-size: 11px; color: #94a3b8;">Dicetak pada: {{ date('d F Y H:i:s') }}</p>
    </div>

    <div class="filter-info">
        <strong>Ringkasan:</strong> Total {{ $certifications->count() }} Data Sertifikasi
        @if(!empty($filters['unit'])) | Unit: {{ $filters['unit'] }} @endif
        @if(!empty($filters['status'])) | Status: {{ ucfirst($filters['status']) }} @endif
    </div>

    <table>
        <thead>
            <tr>
                <th style="width: 4%;">No.</th>
                <th style="width: 12%;">No. Pegawai</th>
                <th style="width: 20%;">Nama Pegawai</th>
                <th style="width: 14%;">Unit Kerja</th>
                <th style="width: 28%;">Nama Sertifikasi</th>
                <th style="width: 16%;">Masa Berlaku</th>
                <th style="width: 12%;">Status</th>
            </tr>
        </thead>
        <tbody>
            @forelse($certifications as $index => $cert)
                @php
                    $days = $cert->days_remaining;
                @endphp
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $cert->user->employee_number ?? '-' }}</td>
                    <td><strong>{{ $cert->user->name ?? '-' }}</strong></td>
                    <td>{{ $cert->user->unit ?? '-' }}</td>
                    <td>{{ $cert->certificate_name }}</td>
                    <td>
                        @if($cert->expiry_date)
                            <strong>{{ $cert->expiry_date->format('d/m/Y') }}</strong>
                        @else
                            <span style="color: #16a34a; font-weight: bold;">Permanen</span>
                        @endif
                    </td>
                    <td>
                        @if($cert->expiry_date === null)
                            <span class="status-active">Permanen (Tanpa Expired)</span>
                        @elseif($cert->overridden_by_excel)
                            @if($cert->status === 'expired')
                                <span class="status-expired">Expired (Excel)</span>
                            @elseif($cert->status === 'warning')
                                <span class="status-warning">Akan Expired (Excel)</span>
                            @else
                                <span class="status-active">Valid (Excel)</span>
                            @endif
                        @elseif($cert->status === 'expired')
                            <span class="status-expired">Expired ({{ $days }} hr)</span>
                        @elseif($cert->status === 'warning')
                            <span class="status-warning">Akan Expired ({{ $days }} hr)</span>
                        @else
                            <span class="status-active">Aktif ({{ $days }} hr)</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" style="text-align: center; color: #94a3b8; padding: 20px;">
                        Tidak ada data sertifikasi.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>