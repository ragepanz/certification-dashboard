<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobTrainingMatrix extends Model
{
    use HasFactory;

    protected $fillable = [
        'job_title',
        'training_code',
        'training_name',
        'traintype',
        'validity_type',
        'no_need_training',
    ];

    protected function casts(): array
    {
        return [
            'no_need_training' => 'boolean',
        ];
    }

    /**
     * Check if a training for a specific job title is 2-Year (Requires Periodic Renewal).
     */
    public static function isPeriodic(string $jobTitle, string $trainingName): bool
    {
        $rule = self::whereRaw('LOWER(job_title) = ?', [strtolower(trim($jobTitle))])
            ->whereRaw('LOWER(training_name) = ?', [strtolower(trim($trainingName))])
            ->first();

        if ($rule) {
            return $rule->validity_type === '2-Year' && !$rule->no_need_training;
        }

        // Default fallback: Dangerous Good is permanent (false), others default true
        return stripos($trainingName, 'Dangerous Good') === false;
    }
}
