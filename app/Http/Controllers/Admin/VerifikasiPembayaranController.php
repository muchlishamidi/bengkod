<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\PembayaranDiverifikasiNotification;
use Illuminate\Http\Request;

class VerifikasiPembayaranController extends Controller
{
    /**
     * Tampilkan daftar tagihan pemeriksaan yang menunggu verifikasi admin.
     */
    public function index(Request $request)
    {
        $query = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ]);

        // Filter berdasarkan status jika ada
        if ($request->filled('status')) {
            $query->where('status_pembayaran', $request->status);
        }

        // Urutkan status menunggu_verifikasi paling atas
        $tagihans = $query->orderByRaw("FIELD(status_pembayaran, 'menunggu_verifikasi', 'menunggu_pembayaran', 'lunas')")
            ->orderBy('tgl_periksa', 'desc')
            ->paginate(10);

        return view('admin.pembayaran.index', compact('tagihans'));
    }

    /**
     * Tampilkan halaman detail verifikasi pembayaran dan preview bukti bayar.
     */
    public function show($id)
    {
        $periksa = Periksa::with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])->findOrFail($id);

        return view('admin.pembayaran.show', compact('periksa'));
    }

    /**
     * Konfirmasi pembayaran menjadi LUNAS.
     */
    public function konfirmasi($id)
    {
        $periksa = Periksa::with('daftarPoli.pasien')->findOrFail($id);

        $periksa->update([
            'status_pembayaran' => 'lunas',
        ]);

        // Notifikasi ke Pasien
        $pasien = $periksa->daftarPoli->pasien ?? null;
        if ($pasien) {
            $pasien->notify(new PembayaranDiverifikasiNotification($periksa));
        }

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('success', 'Pembayaran untuk tagihan #' . $periksa->id . ' (' . ($periksa->daftarPoli->pasien->nama ?? 'Pasien') . ') berhasil diverifikasi sebagai LUNAS.');
    }

    /**
     * Tolak bukti pembayaran jika gambar tidak valid/buram.
     */
    public function tolak(Request $request, $id)
    {
        $periksa = Periksa::findOrFail($id);

        $periksa->update([
            'status_pembayaran' => 'menunggu_pembayaran',
        ]);

        return redirect()
            ->route('admin.pembayaran.index')
            ->with('success', 'Bukti pembayaran ditolak. Status dikembalikan ke Menunggu Pembayaran agar pasien dapat mengunggah ulang.');
    }
}
