<?php

namespace App\Http\Controllers\Dokter;

use App\Http\Controllers\Controller;
use App\Models\Periksa;
use App\Services\ExcelExportService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RiwayatPasienController extends Controller
{
    public function index()
    {
        $riwayatPasien = Periksa::whereHas('daftarPoli.jadwalPeriksa', function($query) {
            $query->where('id_dokter', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter',
            'detailPeriksas.obat'
        ])->orderBy('tgl_periksa', 'desc')->paginate(10);

        return view('dokter.riwayat-pasien.index', compact('riwayatPasien'));
    }

    public function show($id)
    {
        $periksa = Periksa::whereHas('daftarPoli.jadwalPeriksa', function($query) {
            $query->where('id_dokter', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])->findOrFail($id);

        $jumlahObat = $periksa->detailPeriksas->count();

        return view('dokter.riwayat-pasien.show', compact('periksa', 'jumlahObat'));
    }

    public function export()
    {
        $riwayatPasien = Periksa::whereHas('daftarPoli.jadwalPeriksa', function($query) {
            $query->where('id_dokter', Auth::id());
        })->with([
            'daftarPoli.pasien',
            'daftarPoli.jadwalPeriksa.dokter.poli',
            'detailPeriksas.obat'
        ])->orderBy('tgl_periksa', 'desc')->get();

        $headers = ['No', 'No. Antrian', 'Nama Pasien', 'No. RM', 'Keluhan', 'Tanggal Periksa', 'Catatan Dokter', 'Obat Diresepkan', 'Total Biaya (Rp)'];
        $rows = [];
        foreach ($riwayatPasien as $index => $riwayat) {
            $daftarObat = $riwayat->detailPeriksas->map(fn($d) => $d->obat->nama_obat ?? '-')->implode(', ');
            $rows[] = [
                $index + 1,
                $riwayat->daftarPoli->no_antrian,
                $riwayat->daftarPoli->pasien->nama ?? '-',
                $riwayat->daftarPoli->pasien->no_rm ?? '-',
                $riwayat->daftarPoli->keluhan ?? '-',
                \Carbon\Carbon::parse($riwayat->tgl_periksa)->format('d/m/Y H:i'),
                $riwayat->catatan ?: '-',
                $daftarObat ?: 'Tidak ada obat',
                (int)$riwayat->biaya_periksa,
            ];
        }

        return ExcelExportService::export('riwayat-pasien-' . date('Ymd_His') . '.xls', $headers, $rows, 'Riwayat Pasien');
    }
}