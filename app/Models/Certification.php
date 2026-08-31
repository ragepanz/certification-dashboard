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
     * - 'active': > 60 days atau Permanent (expiry_date is null)
     */
    public function getStatusAttribute(): string
    {
        if (stripos($this->certificate_name, 'Dangerous Good') !== false) {
            return 'active';
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
     * Selalu false karena sistem kini 100% database driven.
     */
    public function getOverriddenByExcelAttribute(): bool
    {
        return false;
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

