<?php

namespace App\Http\Controllers\Dokter;

use App\Events\AntrianUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\DetailPeriksa;
use App\Models\Obat;
use App\Models\Periksa;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PeriksaPasienController extends Controller
{
    public function index()
    {
        $dokterId = Auth::id();

        $daftarPasien = DaftarPoli::with(['pasien', 'jadwalPeriksa', 'periksas'])
            ->whereHas('jadwalPeriksa', function ($query) use ($dokterId) {
                $query->where('id_dokter', $dokterId);
            })
            ->orderBy('no_antrian')
            ->get();

        return view('dokter.periksa-pasien.index', compact('daftarPasien'));
    }

    public function create($id)
    {
        $obats = Obat::all();
        return view('dokter.periksa-pasien.create', compact('obats', 'id'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'id_daftar_poli' => 'required|exists:daftar_poli,id',
            'obat_json' => 'required',
            'catatan' => 'nullable|string',
            'biaya_periksa' => 'required|integer',
        ]);

        $obatIds = json_decode($request->obat_json, true);

        $periksa = Periksa::create([
            'id_daftar_poli' => $request->id_daftar_poli,
            'tgl_periksa' => now(),
            'catatan' => $request->catatan,
            'biaya_periksa' => $request->biaya_periksa + 150000,
            'status_pembayaran' => 'menunggu_pembayaran',
        ]);

        if (is_array($obatIds)) {
            foreach ($obatIds as $idObat) {
                DetailPeriksa::create([
                    'id_periksa' => $periksa->id,
                    'id_obat' => $idObat,
                ]);
            }
        }

        // Ambil data jadwal dan cari antrian berikutnya yang belum diperiksa
        $daftarPoli = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'pasien'])->find($request->id_daftar_poli);
        if ($daftarPoli && $daftarPoli->jadwalPeriksa) {
            $jadwal = $daftarPoli->jadwalPeriksa;

            $nextAntrian = $jadwal->daftarPolis()
                ->whereDoesntHave('periksa')
                ->where('id', '!=', $daftarPoli->id)
                ->min('no_antrian');

            $jadwal->nomor_dilayani = $nextAntrian ?: 0;
            $jadwal->save();

            // Broadcast real-time antrian selesai
            broadcast(new AntrianUpdatedEvent(
                $jadwal,
                $jadwal->nomor_dilayani,
                'selesai',
                "Pemeriksaan nomor antrian #{$daftarPoli->no_antrian} selesai.",
                $daftarPoli->pasien->nama,
                $daftarPoli->no_antrian,
                $daftarPoli->id
            ));
        }

        return redirect()
            ->route('periksa-pasien.index')
            ->with('message', 'Data periksa berhasil disimpan dan antrian telah diperbarui.')
            ->with('type', 'success');
    }

    public function panggil($id)
    {
        $daftarPoli = DaftarPoli::with(['jadwalPeriksa.dokter.poli', 'pasien'])->findOrFail($id);
        $jadwal = $daftarPoli->jadwalPeriksa;

        if ($jadwal->id_dokter != Auth::id()) {
            abort(403, 'Akses tidak diizinkan.');
        }

        $jadwal->nomor_dilayani = $daftarPoli->no_antrian;
        $jadwal->save();

        // Broadcast real-time pemanggilan antrian
        broadcast(new AntrianUpdatedEvent(
            $jadwal,
            $daftarPoli->no_antrian,
            'dipanggil',
            "Memanggil nomor antrian #{$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}).",
            $daftarPoli->pasien->nama,
            $daftarPoli->no_antrian,
            $daftarPoli->id
        ));

        if (request()->ajax() || request()->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => "Antrian #{$daftarPoli->no_antrian} berhasil dipanggil.",
                'nomor_dilayani' => $daftarPoli->no_antrian,
            ]);
        }

        return redirect()
            ->back()
            ->with('message', "Memanggil nomor antrian #{$daftarPoli->no_antrian} ({$daftarPoli->pasien->nama}).")
            ->with('type', 'success');
    }
}
