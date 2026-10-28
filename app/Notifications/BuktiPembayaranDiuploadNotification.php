<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class BuktiPembayaranDiuploadNotification extends Notification
{
    use Queueable;

    public Periksa $periksa;

    /**
     * Create a new notification instance.
     */
    public function __construct(Periksa $periksa)
    {
        $this->periksa = $periksa;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $namaPasien = $this->periksa->daftarPoli->pasien->nama ?? 'Pasien';
        $noRm = $this->periksa->daftarPoli->pasien->no_rm ?? '-';

        return [
            'title' => 'Bukti Pembayaran Diunggah',
            'message' => "Pasien {$namaPasien} ({$noRm}) telah mengunggah bukti pembayaran untuk tagihan periksa #{$this->periksa->id}.",
            'url' => route('riwayat-pasien.show', $this->periksa->id),
            'icon' => 'fas fa-receipt',
            'periksa_id' => $this->periksa->id,
        ];
    }
}
