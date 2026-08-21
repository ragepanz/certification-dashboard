<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CertificationLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'certification_id',
        'user_id',
        'old_expiry_date',
        'new_expiry_date',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'old_expiry_date' => 'date',
            'new_expiry_date' => 'date',
        ];
    }

    public function certification()
    {
        return $this->belongsTo(Certification::class);
    }

    public function actor()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
