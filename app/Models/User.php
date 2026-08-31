<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    protected $fillable = [
        'employee_number',
        'name',
        'email',
        'unit',
        'role',
        'job_title',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function certifications()
    {
        return $this->hasMany(Certification::class);
    }

    public function certificationLogs()
    {
        return $this->hasMany(CertificationLog::class);
    }

    public function isSuperAdmin(): bool
    {
        return $this->role === 'superadmin';
    }

    public function isEmployee(): bool
    {
        return $this->role === 'employee';
    }

    /**
     * Total required trainings based on JobTrainingMatrix (Training Mandatory).
     */
    public function getRequiredTrainingsCountAttribute(): int
    {
        if (!$this->job_title) {
            return 0;
        }

        return JobTrainingMatrix::whereRaw('LOWER(job_title) = ?', [strtolower(trim($this->job_title))])
            ->where('no_need_training', false)
            ->count();
    }

    /**
     * Total completed required trainings.
     */
    public function getCompletedTrainingsCountAttribute(): int
    {
        if (!$this->job_title) {
            return 0;
        }

        $requiredNames = JobTrainingMatrix::whereRaw('LOWER(job_title) = ?', [strtolower(trim($this->job_title))])
            ->where('no_need_training', false)
            ->pluck('training_name')
            ->map(fn($n) => strtolower(trim($n)))
            ->toArray();

        if (empty($requiredNames)) {
            return 0;
        }

        // Count employee certifications that match required trainings
        return $this->certifications()
            ->get()
            ->filter(fn($c) => in_array(strtolower(trim($c->certificate_name)), $requiredNames))
            ->count();
    }

    /**
     * Achievement percentage (Completed / Required).
     */
    public function getTrainingAchievementAttribute(): float
    {
        $req = $this->required_trainings_count;
        if ($req === 0) {
            return 100.0;
        }

        $comp = $this->completed_trainings_count;
        return round(($comp / $req) * 100, 1);
    }
}

