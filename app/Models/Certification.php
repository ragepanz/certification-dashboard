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
    public function getDaysRemainingAttribute(): int
    {
        $today = Carbon::today();
        $expiry = Carbon::parse($this->expiry_date)->startOfDay();
        return (int) $today->diffInDays($expiry, false);
    }

    /**
     * Get certification status:
     * - 'expired': < 0 days
     * - 'warning': >= 0 and <= 60 days (Mendekati Expired)
     * - 'active': > 60 days
     */
    public function getStatusAttribute(): string
    {
        $days = $this->days_remaining;
        if ($days < 0) {
            return 'expired';
        } elseif ($days <= 60) {
            return 'warning';
        }
        return 'active';
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
