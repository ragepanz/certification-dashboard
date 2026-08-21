<?php

namespace App\Console\Commands;

use App\Mail\CertificationReminderMail;
use App\Models\Certification;
use App\Models\ReminderLog;
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
    protected $description = 'Kirim email pengingat otomatis untuk sertifikasi yang akan expired (H-5) atau sudah expired (H+5)';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $today = Carbon::today();
        $this->info("Memulai pengecekan reminder sertifikasi per tanggal: " . $today->format('Y-m-d'));

        // 1. Pengecekan H-60 (Sisa 60 hari lagi / 2 bulan)
        $hMinus60Date = $today->copy()->addDays(60)->format('Y-m-d');
        $certsHMinus60 = Certification::with('user')
            ->whereDate('expiry_date', $hMinus60Date)
            ->get();
        $this->info("Ditemukan {$certsHMinus60->count()} sertifikasi pada H-60 ({$hMinus60Date}).");
        foreach ($certsHMinus60 as $cert) {
            $this->processReminder($cert, 'H-60');
        }

        // 2. Pengecekan H-30 (Sisa 30 hari lagi / 1 bulan)
        $hMinus30Date = $today->copy()->addDays(30)->format('Y-m-d');
        $certsHMinus30 = Certification::with('user')
            ->whereDate('expiry_date', $hMinus30Date)
            ->get();
        $this->info("Ditemukan {$certsHMinus30->count()} sertifikasi pada H-30 ({$hMinus30Date}).");
        foreach ($certsHMinus30 as $cert) {
            $this->processReminder($cert, 'H-30');
        }

        // 3. Pengecekan H-5 (Sisa 5 hari lagi)
        $hMinus5Date = $today->copy()->addDays(5)->format('Y-m-d');
        $certsHMinus5 = Certification::with('user')
            ->whereDate('expiry_date', $hMinus5Date)
            ->get();
        $this->info("Ditemukan {$certsHMinus5->count()} sertifikasi pada H-5 ({$hMinus5Date}).");
        foreach ($certsHMinus5 as $cert) {
            $this->processReminder($cert, 'H-5');
        }

        // 4. Pengecekan H+5 (Sudah expired 5 hari lalu)
        $hPlus5Date = $today->copy()->subDays(5)->format('Y-m-d');
        $certsHPlus5 = Certification::with('user')
            ->whereDate('expiry_date', $hPlus5Date)
            ->get();
        $this->info("Ditemukan {$certsHPlus5->count()} sertifikasi pada H+5 ({$hPlus5Date}).");
        foreach ($certsHPlus5 as $cert) {
            $this->processReminder($cert, 'H+5');
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
