<?php

use App\Http\Controllers\Admin\AdminPoliController;
use App\Http\Controllers\Admin\DokterController;
use App\Http\Controllers\Admin\ObatController;
use App\Http\Controllers\Admin\PasienController;
use App\Http\Controllers\Admin\VerifikasiPembayaranController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Dokter\JadwalPeriksaController;
use App\Http\Controllers\Dokter\PeriksaPasienController;
use App\Http\Controllers\Dokter\RiwayatPasienController;
use App\Http\Controllers\NotificationController;
use App\Http\Controllers\Pasien\PembayaranController;
use App\Http\Controllers\Pasien\PoliController;
use App\Http\Controllers\Pasien\RiwayatPoliController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return view('welcome');
});

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:admin'])->prefix('admin')->group(function () {
    Route::get('/dashboard', function () {
        return view('admin.dashboard');
    })->name('admin.dashboard');

    Route::get('/dokter/export', [DokterController::class, 'export'])->name('dokter.export');
    Route::get('/pasien/export', [PasienController::class, 'export'])->name('pasien.export');
    Route::get('/obat/export', [ObatController::class, 'export'])->name('obat.export');

    Route::resource('polis', AdminPoliController::class);
    Route::resource('dokter', DokterController::class);
    Route::resource('pasien', PasienController::class);
    Route::resource('obat', ObatController::class);

    Route::get('/pembayaran', [VerifikasiPembayaranController::class, 'index'])->name('admin.pembayaran.index');
    Route::get('/pembayaran/{id}', [VerifikasiPembayaranController::class, 'show'])->name('admin.pembayaran.show');
    Route::post('/pembayaran/{id}/konfirmasi', [VerifikasiPembayaranController::class, 'konfirmasi'])->name('admin.pembayaran.konfirmasi');
    Route::post('/pembayaran/{id}/tolak', [VerifikasiPembayaranController::class, 'tolak'])->name('admin.pembayaran.tolak');
});

/*
|--------------------------------------------------------------------------
| Dokter Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:dokter'])->prefix('dokter')->group(function () {
    Route::get('/dashboard', function () {
        return view('dokter.dashboard');
    })->name('dokter.dashboard');

    Route::get('/jadwal-periksa/export', [JadwalPeriksaController::class, 'export'])->name('jadwal-periksa.export');
    Route::resource('jadwal-periksa', JadwalPeriksaController::class);

    Route::get('/periksa-pasien', [PeriksaPasienController::class, 'index'])->name('periksa-pasien.index');
    Route::post('/periksa-pasien', [PeriksaPasienController::class, 'store'])->name('periksa-pasien.store');
    Route::get('/periksa-pasien/{id}', [PeriksaPasienController::class, 'create'])->name('periksa-pasien.create');
    Route::post('/periksa-pasien/{id}/panggil', [PeriksaPasienController::class, 'panggil'])->name('periksa-pasien.panggil');

    Route::get('/riwayat-pasien/export', [RiwayatPasienController::class, 'export'])->name('riwayat-pasien.export');
    Route::get('/riwayat-pasien', [RiwayatPasienController::class, 'index'])->name('riwayat-pasien.index');
    Route::get('/riwayat-pasien/{id}', [RiwayatPasienController::class, 'show'])->name('riwayat-pasien.show');
});

/*
|--------------------------------------------------------------------------
| Pasien Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth', 'role:pasien'])->prefix('pasien')->group(function () {
    Route::get('/dashboard', [PoliController::class, 'dashboard'])->name('pasien.dashboard');

    Route::get('/daftar', [PoliController::class, 'get'])->name('pasien.daftar');

    Route::post('/daftar', [PoliController::class, 'submit'])
        ->name('pasien.daftar.submit');

    Route::get('/riwayat', [RiwayatPoliController::class, 'index'])->name('pasien.riwayat.index');
    Route::get('/riwayat/{id}', [RiwayatPoliController::class, 'show'])->name('pasien.riwayat.show');

    Route::get('/pembayaran', [PembayaranController::class, 'index'])->name('pasien.pembayaran.index');
    Route::get('/pembayaran/{id}', [PembayaranController::class, 'show'])->name('pasien.pembayaran.show');
    Route::post('/pembayaran/{id}/upload', [PembayaranController::class, 'upload'])->name('pasien.pembayaran.upload');
});

/*
|--------------------------------------------------------------------------
| Profile & Password Routes (Semua Role)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    Route::get('/profile/password', [ProfileController::class, 'editPassword'])->name('profile.password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/notifications', [NotificationController::class, 'index'])->name('notifications.index');
    Route::post('/notifications/{id}/read', [NotificationController::class, 'markAsRead'])->name('notifications.markAsRead');
    Route::post('/notifications/read-all', [NotificationController::class, 'markAllAsRead'])->name('notifications.markAllAsRead');
});