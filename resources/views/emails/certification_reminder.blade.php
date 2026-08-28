<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background-color: #f1f5f9; margin: 0; padding: 24px; color: #1e293b; }
        .card { max-width: 600px; margin: 0 auto; background: #ffffff; border-radius: 16px; overflow: hidden; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.1); }
        .header { background: #4f46e5; padding: 32px 24px; text-align: center; color: white; }
        .header.danger { background: #e11d48; }
        .content { padding: 32px 24px; }
        .badge { display: inline-block; padding: 6px 12px; border-radius: 9999px; font-weight: bold; font-size: 12px; }
        .badge-warning { background: #fef3c7; color: #92400e; }
        .badge-danger { background: #fee2e2; color: #991b1b; }
        .detail-table { width: 100%; border-collapse: collapse; margin: 20px 0; background: #f8fafc; border-radius: 8px; }
        .detail-table td { padding: 12px 16px; border-bottom: 1px solid #e2e8f0; font-size: 14px; }
        .detail-table td:first-child { font-weight: 600; color: #64748b; width: 40%; }
        .footer { text-align: center; padding: 20px; font-size: 12px; color: #94a3b8; border-top: 1px solid #e2e8f0; }
        .btn { display: inline-block; background: #4f46e5; color: white; padding: 12px 24px; text-decoration: none; border-radius: 8px; font-weight: 600; margin-top: 16px; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header {{ $type === 'H+5' ? 'danger' : '' }}">
            <h2 style="margin: 0; font-size: 20px;">
                @if($type === 'H-60')
                    📅 Pemberitahuan Masa Berlaku Sertifikasi (H-60)
                @elseif($type === 'H-30')
                    🔔 Pengingat Renewal Sertifikasi (H-30)
                @elseif($type === 'H-5')
                    ⚠️ Peringatan Kritis Masa Berlaku Sertifikasi (H-5)
                @else
                    🚨 Pemberitahuan Sertifikasi Expired (H+5)
                @endif
            </h2>
            <p style="margin: 6px 0 0; opacity: 0.9; font-size: 13px;">Learning Center Unit (LCU) - PT GMF AeroAsia Tbk</p>
        </div>

        <div class="content">
            <p>Halo <strong>{{ $certification->user->name }}</strong>,</p>

            @if($type === 'H-60')
                <p>Kami menginformasikan bahwa masa berlaku sertifikasi kompetensi Anda di bawah ini akan berakhir dalam <strong>60 hari (2 bulan) ke depan</strong>. Mohon persiapkan jadwal pelatihan renewal.</p>
            @elseif($type === 'H-30')
                <p>Pengingat kedua: Masa berlaku sertifikasi kompetensi Anda akan berakhir dalam <strong>30 hari (1 bulan) ke depan</strong>. Silakan hubungi bagian LCU atau atasan unit terkait.</p>
            @elseif($type === 'H-5')
                <p><strong>Penting:</strong> Sertifikasi kompetensi Anda berikut ini akan segera berakhir dalam <strong>5 hari ke depan</strong>.</p>
            @else
                <p>Kami menginformasikan bahwa masa berlaku sertifikasi kompetensi Anda berikut ini <strong>telah melewati batas waktu (expired 5 hari yang lalu)</strong>.</p>
            @endif

            <table class="detail-table">
                <tr>
                    <td>Nomor Pegawai</td>
                    <td><strong>{{ $certification->user->employee_number ?? '-' }}</strong></td>
                </tr>
                <tr>
                    <td>Nama Sertifikasi</td>
                    <td><strong>{{ $certification->certificate_name }}</strong></td>
                </tr>
                <tr>
                    <td>Tanggal Expired</td>
                    <td><strong style="color: {{ $type === 'H+5' ? '#e11d48' : '#d97706' }};">{{ $certification->expiry_date->format('d F Y') }}</strong></td>
                </tr>
                <tr>
                    <td>Status Reminder</td>
                    <td>
                        <span class="badge {{ $type === 'H+5' ? 'badge-danger' : 'badge-warning' }}">
                            {{ $type }} Milestone
                        </span>
                    </td>
                </tr>
            </table>


            <p style="font-size: 13px; color: #64748b; line-height: 1.6;">
                Harap segera berkoordinasi dengan Learning Center Unit (LCU) untuk prosedur pembaharuan atau penyerahan bukti kelulusan renewal sertifikasi Anda.
            </p>

            <div style="text-align: center; margin-top: 24px;">
                <a href="{{ url('/login') }}" class="btn">Buka Portal Sertifikasi Pegawai</a>
            </div>
        </div>

        <div class="footer">
            Email ini dikirimkan secara otomatis oleh Employee Certification Monitoring System (LCU).<br>
            Mohon tidak membalas langsung ke email ini.
        </div>
    </div>
</body>
</html>