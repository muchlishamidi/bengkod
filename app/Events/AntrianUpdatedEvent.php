<?php

namespace App\Events;

use App\Models\JadwalPeriksa;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AntrianUpdatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public int $idJadwal;
    public ?int $idDokter;
    public int $nomorDilayani;
    public ?string $namaPoli;
    public ?string $namaDokter;
    public string $status;
    public string $message;
    public ?string $namaPasien;
    public ?int $noAntrian;
    public ?int $idDaftarPoli;
    public ?string $keluhan;

    /**
     * Create a new event instance.
     */
    public function __construct(
        JadwalPeriksa $jadwal,
        ?int $nomorOverride = null,
        string $status = 'dipanggil',
        string $message = '',
        ?string $namaPasien = null,
        ?int $noAntrian = null,
        ?int $idDaftarPoli = null,
        ?string $keluhan = null
    ) {
        $jadwal->loadMissing(['dokter.poli']);
        $this->idJadwal = (int) $jadwal->id;
        $this->idDokter = (int) $jadwal->id_dokter;
        $this->nomorDilayani = $nomorOverride !== null ? (int) $nomorOverride : (int) $jadwal->nomor_dilayani;
        $this->namaPoli = $jadwal->dokter->poli->nama_poli ?? 'Poliklinik';
        $this->namaDokter = $jadwal->dokter->nama ?? 'Dokter';
        $this->status = $status;
        $this->message = $message ?: "Antrian #{$this->nomorDilayani} di {$this->namaPoli}";
        $this->namaPasien = $namaPasien;
        $this->noAntrian = $noAntrian;
        $this->idDaftarPoli = $idDaftarPoli;
        $this->keluhan = $keluhan;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, Channel>
     */
    public function broadcastOn(): array
    {
        return [
            new Channel('antrian-poli'),
        ];
    }

    /**
     * The event's broadcast name.
     */
    public function broadcastAs(): string
    {
        return 'AntrianUpdated';
    }

    /**
     * Data to broadcast with the event.
     *
     * @return array<string, mixed>
     */
    public function broadcastWith(): array
    {
        return [
            'id_jadwal' => $this->idJadwal,
            'id_dokter' => $this->idDokter,
            'nomor_dilayani' => $this->nomorDilayani,
            'nama_poli' => $this->namaPoli,
            'nama_dokter' => $this->namaDokter,
            'status' => $this->status,
            'message' => $this->message,
            'nama_pasien' => $this->namaPasien,
            'no_antrian' => $this->noAntrian,
            'id_daftar_poli' => $this->idDaftarPoli,
            'keluhan' => $this->keluhan,
        ];
    }
}
