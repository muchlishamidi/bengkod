<?php

namespace App\Http\Controllers\Pasien;

use App\Events\AntrianUpdatedEvent;
use App\Http\Controllers\Controller;
use App\Models\DaftarPoli;
use App\Models\JadwalPeriksa;
use App\Models\Poli;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PoliController extends Controller
{
    public function dashboard()
    {
        $user = Auth::user();

        // Antrian aktif pasien (yang belum diperiksa)
        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->whereDoesntHave('periksa')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->latest('id')
            ->first();

        // Daftar jadwal semua poli dan dokter
        $jadwals = JadwalPeriksa::with(['dokter.poli', 'daftarPolis.periksa'])->get();

        return view('pasien.dashboard', compact('user', 'antrianAktif', 'jadwals'));
    }

    public function get()
    {
        $user = Auth::user();

        // Cek apakah pasien sudah memiliki antrian aktif
        $antrianAktif = DaftarPoli::where('id_pasien', $user->id)
            ->whereDoesntHave('periksa')
            ->with(['jadwalPeriksa.dokter.poli'])
            ->first();

        $polis = Poli::all();
        $jadwal = JadwalPeriksa::with('dokter', 'dokter.poli')->get();

        return view('pasien.daftar', [
            'user' => $user,
            'polis' => $polis,
            'jadwals' => $jadwal,
            'antrianAktif' => $antrianAktif,
        ]);
    }

    public function submit(Request $request)
    {
        $request->validate([
            'id_poli' => 'required|exists:poli,id',
            'id_jadwal' => 'required|exists:jadwal_periksa,id',
            'keluhan' => 'required|string|min:3',
            'id_pasien' => 'required|exists:users,id',
        ], [
            'keluhan.required' => 'Keluhan wajib diisi untuk memudahkan dokter dalam pemeriksaan.',
            'keluhan.min' => 'Keluhan minimal berisi 3 karakter.',
            'id_poli.required' => 'Silakan pilih poliklinik tujuan.',
            'id_jadwal.required' => 'Silakan pilih jadwal periksa dokter.',
        ]);

        // Ketentuan: Pasien tidak dapat mendaftar ke dua poli sekaligus
        // Pasien hanya dapat mendaftar kembali setelah proses pemeriksaan selesai
        $antrianAktif = DaftarPoli::where('id_pasien', Auth::id())
            ->whereDoesntHave('periksa')
            ->with('jadwalPeriksa.dokter.poli')
            ->first();

        if ($antrianAktif) {
            $namaPoli = $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli';
            return redirect()
                ->back()
                ->with('error', "Anda masih memiliki antrian aktif di {$namaPoli} dengan Nomor Antrian #{$antrianAktif->no_antrian}. Anda hanya dapat mendaftar kembali setelah proses pemeriksaan selesai.")
                ->withInput();
        }

        $jumlahSudahDaftar = DaftarPoli::where('id_jadwal', $request->id_jadwal)->count();
        $noAntrianBaru = $jumlahSudahDaftar + 1;

        $daftar = DaftarPoli::create([
            'id_pasien' => Auth::id(),
            'id_jadwal' => $request->id_jadwal,
            'keluhan' => $request->keluhan,
            'no_antrian' => $noAntrianBaru,
        ]);

        $jadwal = JadwalPeriksa::with('dokter.poli')->find($request->id_jadwal);
        if ($jadwal && empty($jadwal->nomor_dilayani)) {
            $jadwal->nomor_dilayani = $noAntrianBaru;
            $jadwal->save();
        }

        if ($jadwal) {
            broadcast(new AntrianUpdatedEvent(
                $jadwal,
                $jadwal->nomor_dilayani,
                'daftar',
                "Pendaftaran baru antrian #{$noAntrianBaru} di " . ($jadwal->dokter->poli->nama_poli ?? 'Poli'),
                Auth::user()->nama,
                $noAntrianBaru,
                $daftar->id,
                $daftar->keluhan
            ));
        }

        return redirect()
            ->route('pasien.dashboard')
            ->with('success', 'Berhasil mendaftar ke Poli! Nomor antrian Anda adalah #' . $noAntrianBaru);
    }
}
