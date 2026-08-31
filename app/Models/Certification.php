<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Certification extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'certificate_name',
        'issue_date',
        'expiry_date',
        'excel_status',
        'certificate_file',
    ];


    protected function casts(): array
    {
        return [
            'issue_date' => 'date',
            'expiry_date' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function logs()
    {
        return $this->hasMany(CertificationLog::class)->orderBy('created_at', 'desc');
    }

    public function reminderLogs()
    {
        return $this->hasMany(ReminderLog::class)->orderBy('sent_at', 'desc');
    }

    /**
     * Get remaining days until expiry.
     * Negative value means expired.
     */
    public function getDaysRemainingAttribute(): ?int
    {
        if (stripos($this->certificate_name, 'Dangerous Good') !== false) {
            return null;
        }

        if ($this->expiry_date === null) {
            return null;
        }

        $today = Carbon::today();
        $expiry = Carbon::parse($this->expiry_date)->startOfDay();
        return (int) $today->diffInDays($expiry, false);
    }

    /**
     * Get certification status:
     * - 'expired': < 0 days
     * - 'warning': >= 0 and <= 60 days (Mendekati Expired)
     * - 'active': > 60 days
     * - 'active': null (expiry_date is set to null = Permanent / Tidak Berakhir)
     *
     * Jika ada excel_status (dari kolom "Status" di Excel), status Excel
     * menjadi sumber kebenaran dan meng-override perhitungan tanggal.
     */
    public function getStatusAttribute(): string
    {
        if (stripos($this->certificate_name, 'Dangerous Good') !== false) {
            return 'active';
        }

        // Jika excel_status kosong/null, anggap sebagai sertifikasi sekali ambil (Aktif, tidak pernah expired berdasarkan tanggal)
        if ($this->excel_status === null) {
            return 'active';
        }

        // Override dari Excel: valid -> Aktif, expiring -> Akan Expired, expired -> Expired
        $override = match ($this->excel_status) {
            'valid' => 'active',
            'expiring' => 'warning',
            'expired' => 'expired',
            default => null,
        };
        if ($override !== null) {
            return $override;
        }

        $days = $this->days_remaining;
        if ($days === null) {
            return 'active'; // Permanent / Tidak Berakhir
        }
        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 60) {
            return 'warning';
        }
        return 'active';
    }

    /**
     * Apakah status ini di-override oleh kolom Status di Excel
     * sehingga berbeda dengan perhitungan murni dari tanggal?
     */
    public function getOverriddenByExcelAttribute(): bool
    {
        if (stripos($this->certificate_name, 'Dangerous Good') !== false) {
            return false;
        }

        if ($this->excel_status === null) {
            return false;
        }

        $dateBased = $this->days_remaining === null
            ? 'active'
            : ($this->days_remaining < 0 ? 'expired' : ($this->days_remaining <= 60 ? 'warning' : 'active'));

        return $this->status !== $dateBased;
    }


    public function getStatusLabelAttribute(): string
    {
        return match ($this->status) {
            'expired' => 'Expired',
            'warning' => 'Akan Expired',
            'active' => 'Aktif',
        };
    }
}
