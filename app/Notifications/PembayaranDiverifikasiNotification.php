<?php

namespace App\Notifications;

use App\Models\Periksa;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class PembayaranDiverifikasiNotification extends Notification
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
        $namaPoli = $this->periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik';

        return [
            'title' => 'Pembayaran Diverifikasi Lunas',
            'message' => "Pembayaran untuk pemeriksaan Anda di {$namaPoli} (Tagihan #{$this->periksa->id}) telah diverifikasi oleh Admin sebagai LUNAS.",
            'url' => route('pasien.pembayaran.show', $this->periksa->id),
            'icon' => 'fas fa-circle-check',
            'periksa_id' => $this->periksa->id,
        ];
    }
}
