<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Jadwal harian reminder otomatis sertifikasi (Dinonaktifkan sementara sesuai permintaan)
// Illuminate\Support\Facades\Schedule::command('certification:send-reminders')->dailyAt('08:00');

