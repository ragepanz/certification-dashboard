<?php

namespace App\Mail;

use App\Models\Certification;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class CertificationReminderMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    public Certification $certification;
    public string $type; // 'H-60', 'H-30', 'H-5', 'H+5'

    public function __construct(Certification $certification, string $type)
    {
        $this->certification = $certification;
        $this->type = $type;
    }

    public function envelope(): Envelope
    {
        $subject = match ($this->type) {
            'H-60' => '📅 [Pengingat H-60] Masa Berlaku Sertifikasi Segera Berakhir dalam 2 Bulan - ' . $this->certification->certificate_name,
            'H-30' => '🔔 [Pengingat H-30] Persiapan Renewal Sertifikasi (Sisa 1 Bulan) - ' . $this->certification->certificate_name,
            'H-5'  => '⚠️ [URGENT H-5] Sertifikasi Anda Akan Berakhir dalam 5 Hari - ' . $this->certification->certificate_name,
            'H+5'  => '🚨 [ESKALASI H+5] Sertifikasi Telah Expired 5 Hari Lalu - ' . $this->certification->certificate_name,
            default => 'Pemberitahuan Status Sertifikasi - ' . $this->certification->certificate_name,
        };

        return new Envelope(
            subject: $subject,
        );
    }


    public function content(): Content
    {
        return new Content(
            view: 'emails.certification_reminder',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
