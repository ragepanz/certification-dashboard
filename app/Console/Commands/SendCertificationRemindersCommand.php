<?php

namespace App\Console\Commands;

use App\Mail\CertificationReminderMail;
use App\Models\Certification;
use App\Models\ReminderLog;
use App\Models\Setting;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCertificationRemindersCommand extends Command
{
    protected $signature = 'certification:send-reminders';

    protected $description = 'Kirim email pengingat otomatis untuk sertifikasi yang akan expired atau sudah expired berdasarkan pengaturan reminder di dashboard';

    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Memulai pengecekan reminder sertifikasi per tanggal: " . $today->format('Y-m-d'));

        $daysBefore = Setting::getDayList('reminder_days_before', [60, 30, 5]);
        $daysAfter = Setting::getDayList('reminder_days_after', [5]);

        $this->info("Pengaturan aktif — H-: " . implode(', ', $daysBefore) . " | H+: " . implode(', ', $daysAfter));

        // Special reminders for Group Head and Division Head
        $this->processSpecialReminders();

        foreach ($daysBefore as $offset) {
            $targetDate = $today->copy()->addDays($offset)->format('Y-m-d');
            $certs = Certification::with('user')
                ->whereDate('expiry_date', $targetDate)
                ->where(function ($q) {
                    // Sertifikat yang masih Valid menurut Excel tidak perlu diingatkan
                    $q->whereNull('excel_status')->orWhere('excel_status', '!=', 'valid');
                })
                ->get();
            $this->info("Ditemukan {$certs->count()} sertifikasi pada H-{$offset} ({$targetDate}).");
            foreach ($certs as $cert) {
                $this->processReminder($cert, "H-{$offset}");
            }
        }

        foreach ($daysAfter as $offset) {
            $targetDate = $today->copy()->subDays($offset)->format('Y-m-d');
            $certs = Certification::with('user')
                ->whereDate('expiry_date', $targetDate)
                ->where(function ($q) {
                    // Sertifikat yang masih Valid menurut Excel tidak perlu diingatkan
                    $q->whereNull('excel_status')->orWhere('excel_status', '!=', 'valid');
                })
                ->get();
            $this->info("Ditemukan {$certs->count()} sertifikasi pada H+{$offset} ({$targetDate}).");
            foreach ($certs as $cert) {
                $this->processReminder($cert, "H+{$offset}");
            }
        }

        $this->info("Proses pengiriman reminder sertifikasi selesai!");
        return Command::SUCCESS;
    }

    protected function processSpecialReminders(): void
    {
        // Group Head and Division Head mendapat pengingat khusus untuk Human Factor dan Safety Management System
        $specialPositions = ['Group Head', 'Division Head'];
        $specialCertificates = ['Human Factor', 'Safety Management System'];

        $users = User::whereIn('jabatan', $specialPositions)->get();

        foreach ($users as $user) {
            foreach ($specialCertificates as $certName) {
                $cert = Certification::where('user_id', $user->id)
                    ->where('certificate_name', 'like', "%{$certName}%")
                    ->first();

                if (!$cert) {
                    $this->info("Tidak ditemukan sertifikasi {$certName} untuk {$user->name} ({$user->jabatan})");
                    continue;
                }

                // Cek apakah sudah pernah dikirimkan log reminder tipe khusus ini
                $alreadySent = ReminderLog::where('certification_id', $cert->id)
                    ->where('type', 'special')
                    ->where('status', 'sent')
                    ->exists();

                if ($alreadySent) {
                    $this->line("Special reminder untuk '{$cert->certificate_name}' ({$user->email}) sudah pernah dikirim sebelumnya. Dilewati.");
                    continue;
                }

                // Cek status sertifikasi - hanyalah active, expired, atau warning (bukan permanent)
                if ($cert->status === 'active' || $cert->status === 'warning' || $cert->status === 'expired') {
                    try {
                        Mail::to($user->email)->send(new CertificationReminderMail($cert, 'special'));

                        ReminderLog::create([
                            'certification_id' => $cert->id,
                            'type' => 'special',
                            'recipient_email' => $user->email,
                            'status' => 'sent',
                            'sent_at' => now(),
                        ]);

                        $this->info("✔ Berhasil mengirim special reminder ke {$user->email} ({$cert->certificate_name})");
                    } catch (\Exception $e) {
                        Log::error("Gagal mengirim special reminder sertifikasi ID {$cert->id}: " . $e->getMessage());

                        ReminderLog::create([
                            'certification_id' => $cert->id,
                            'type' => 'special',
                            'recipient_email' => $user->email,
                            'status' => 'failed',
                            'sent_at' => now(),
                            'error_message' => $e->getMessage(),
                        ]);

                        $this->error("✖ Gagal mengirim special reminder ke {$user->email}: " . $e->getMessage());
                    }
                }
            }
        }
    }

    protected function processReminder(Certification $cert, string $type): void
    {
        $user = $cert->user;
        if (!$user || !$user->email) {
            $this->warn("User untuk sertifikasi ID {$cert->id} tidak ditemukan atau tidak memiliki email.");
            return;
        }

        // Cek apakah sudah pernah dikirimkan log reminder tipe ini untuk sertifikasi ini
        $alreadySent = ReminderLog::where('certification_id', $cert->id)
            ->where('type', $type)
            ->where('status', 'sent')
            ->exists();

        if ($alreadySent) {
            $this->line("Reminder {$type} untuk '{$cert->certificate_name}' ({$user->email}) sudah pernah dikirim sebelumnya. Dilewati.");
            return;
        }

        try {
            Mail::to($user->email)->send(new CertificationReminderMail($cert, $type));

            ReminderLog::create([
                'certification_id' => $cert->id,
                'type' => $type,
                'recipient_email' => $user->email,
                'status' => 'sent',
                'sent_at' => now(),
            ]);

            $this->info("✔ Berhasil mengirim reminder {$type} ke {$user->email} ({$cert->certificate_name})");
        } catch (\Exception $e) {
            Log::error("Gagal mengirim reminder sertifikasi ID {$cert->id}: " . $e->getMessage());

            ReminderLog::create([
                'certification_id' => $cert->id,
                'type' => $type,
                'recipient_email' => $user->email,
                'status' => 'failed',
                'sent_at' => now(),
                'error_message' => $e->getMessage(),
            ]);

            $this->error("✖ Gagal mengirim reminder {$type} ke {$user->email}: " . $e->getMessage());
        }
    }
}