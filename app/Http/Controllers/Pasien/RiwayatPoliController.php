<?php

namespace App\Http\Controllers\Pasien;

use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPoliController extends Controller
{
    public function index()
    {
        $riwayatPoli = DaftarPoli::where('id_pasien', Auth::id())
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksa'
            ])
            ->orderBy('id', 'desc')
            ->paginate(10);

        return view('pasien.riwayat.index', compact('riwayatPoli'));
    }

    public function show($id)
    {
        $daftarPoli = DaftarPoli::where('id_pasien', Auth::id())
            ->with([
                'jadwalPeriksa.dokter.poli',
                'periksa.detailPeriksas.obat'
            ])
            ->findOrFail($id);

        if (!$daftarPoli->periksa) {
            return redirect()
                ->route('pasien.riwayat.index')
                ->with('error', 'Pemeriksaan untuk pendaftaran ini belum dilakukan.');
        }

        return view('pasien.riwayat.show', compact('daftarPoli'));
    }
}
