<?php

namespace App\Console\Commands;

use App\Mail\CertificationReminderMail;
use App\Models\Certification;
use App\Models\ReminderLog;
use App\Models\Setting;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendCertificationRemindersCommand extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'certification:send-reminders';

    /**
     * The console command description.
     */
    protected $description = 'Kirim email pengingat otomatis untuk sertifikasi yang akan expired atau sudah expired berdasarkan pengaturan reminder di dashboard';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Memulai pengecekan reminder sertifikasi per tanggal: " . $today->format('Y-m-d'));

        $daysBefore = Setting::getDayList('reminder_days_before', [60, 30, 5]);
        $daysAfter = Setting::getDayList('reminder_days_after', [5]);

        $this->info("Pengaturan aktif — H-: " . implode(', ', $daysBefore) . " | H+: " . implode(', ', $daysAfter));

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
