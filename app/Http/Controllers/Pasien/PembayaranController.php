<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Notifications\BuktiPembayaranDiuploadNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PembayaranController extends Controller
{
    /**
     * Tampilkan daftar tagihan pemeriksaan pasien.
     */
    public function index()
    {
        $tagihans = Periksa::whereHas('daftarPoli', function($q) {
            $q->where('id_pasien', Auth::id());
        })->with([
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])->orderBy('tgl_periksa', 'desc')->paginate(10);

        return view('pasien.pembayaran.index', compact('tagihans'));
    }

    /**
     * Tampilkan detail tagihan dan form upload bukti bayar.
     */
    public function show($id)
    {
        $periksa = Periksa::whereHas('daftarPoli', function($q) {
            $q->where('id_pasien', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])->findOrFail($id);

        return view('pasien.pembayaran.show', compact('periksa'));
    }

    /**
     * Upload foto bukti pembayaran oleh pasien.
     */
    public function upload(Request $request, $id)
    {
        $periksa = Periksa::whereHas('daftarPoli', function($q) {
            $q->where('id_pasien', Auth::id());
        })->findOrFail($id);

        $request->validate([
            'bukti_pembayaran' => 'required|image|mimes:jpeg,png,jpg,webp|max:3072',
        ], [
            'bukti_pembayaran.required' => 'File bukti pembayaran wajib dipilih.',
            'bukti_pembayaran.image' => 'File bukti pembayaran harus berupa gambar.',
            'bukti_pembayaran.mimes' => 'Format gambar harus JPEG, PNG, JPG, atau WEBP.',
            'bukti_pembayaran.max' => 'Ukuran file gambar maksimal 3MB.',
        ]);

        // Hapus file lama jika ada dan pasien mengupload ulang
        if ($periksa->bukti_pembayaran && Storage::disk('public')->exists($periksa->bukti_pembayaran)) {
            Storage::disk('public')->delete($periksa->bukti_pembayaran);
        }

        // Simpan file baru di folder storage/app/public/bukti_pembayaran
        $path = $request->file('bukti_pembayaran')->store('bukti_pembayaran', 'public');

        $periksa->update([
            'bukti_pembayaran' => $path,
            'status_pembayaran' => 'menunggu_verifikasi',
            'tgl_bayar' => now(),
        ]);

        // Notifikasi ke Dokter yang memeriksa
        $dokter = $periksa->daftarPoli->jadwalPeriksa->dokter ?? null;
        if ($dokter) {
            $dokter->notify(new BuktiPembayaranDiuploadNotification($periksa));
        }

        return redirect()
            ->route('pasien.pembayaran.show', $periksa->id)
            ->with('success', 'Bukti pembayaran berhasil diunggah! Mohon menunggu verifikasi dari Admin.');
    }
}
