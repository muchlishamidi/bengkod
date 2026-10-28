# Poliklinik App v2 - Fast Track Web Developer

Sistem Informasi Manajemen Poliklinik Berbasis Web yang dikembangkan untuk memenuhi penugasan **Fast Track Web Developer Bengkel Koding Ganjil 2026/2027**. Aplikasi ini dibangun menggunakan framework **Laravel 12**, **Tailwind CSS v4**, **DaisyUI v5**, serta server WebSocket **Laravel Reverb**.

---

## Informasi Mahasiswa & Pengumpulan
- **Nama Mahasiswa**: *MUCHLIS HAMIDI*
- **NIM**: *A11.2023.15163*
- **Mata Kuliah / Program**: Bengkel Koding

---

## Akun Pengujian (Default Seeded Users)

| Role | Email | Password | Hak Akses Utama |
| :--- | :--- | :--- | :--- |
| **Admin** | `admin@gmail.com` | `admin` | Kelola Poli, Dokter, Pasien, Obat, Verifikasi Pembayaran, Export Data |
| **Dokter** | `dokter@gmail.com` | `dokter` | Kelola Jadwal Praktik, Panggil & Periksa Pasien, Riwayat Pasien, Export Jadwal & Riwayat |
| **Pasien** | `pasien@gmail.com` | `pasien` | Dashboard Antrian Live, Pendaftaran Poli, Riwayat Pendaftaran, Upload Bukti Pembayaran |

---

## Panduan Instalasi & Menjalankan Aplikasi

### 1. Prasyarat Sistem
- PHP >= 8.2 (Disarankan PHP 8.2 / 8.4)
- Composer
- Node.js & NPM
- MySQL / MariaDB (Laragon / XAMPP)

### 2. Langkah Menjalankan
1. **Clone repository & masuk ke direktori**:
   ```bash
   git clone <link-repo-anda>
   cd poliklinik-app
   ```
2. **Install Dependensi PHP & Node.js**:
   ```bash
   composer install
   npm install
   npm run build
   ```
3. **Konfigurasi Environment**:
   Salin file `.env.example` ke `.env` dan sesuaikan koneksi database MySQL:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=poliklinik_db
   DB_USERNAME=root
   DB_PASSWORD=

   BROADCAST_CONNECTION=reverb
   REVERB_APP_ID=423779
   REVERB_APP_KEY=ffau0rdmm9v8ysmmrhpi
   REVERB_APP_SECRET=i8665fn216nuqbca1eks
   REVERB_HOST="localhost"
   REVERB_PORT=8080
   REVERB_SCHEME=http

   VITE_REVERB_APP_KEY="${REVERB_APP_KEY}"
   VITE_REVERB_HOST="${REVERB_HOST}"
   VITE_REVERB_PORT="${REVERB_PORT}"
   VITE_REVERB_SCHEME="${REVERB_SCHEME}"
   ```
4. **Migrasi Database & Symbolic Link Storage**:
   ```bash
   php artisan key:generate
   php artisan migrate --seed
   php artisan storage:link
   ```
5. **Menjalankan Server**:
   Buka 2 jendela terminal:
   - **Terminal 1 (Web Server)**:
     ```bash
     php artisan serve
     ```
   - **Terminal 2 (WebSocket Server Reverb)**:
     ```bash
     php artisan reverb:start
     ```
   *(Opsional: tambahkan flag `--debug` pada reverb untuk melihat log transmisi live).*

---

## Dokumentasi Fitur yang Telah Dikembangkan

Berikut adalah dokumentasi rinci mengenai 7 fitur utama dan 2 fitur prasyarat sesuai dokumen spesifikasi penilaian:

---

### Fitur 1: Dashboard Pasien & Validasi Antrian

#### A. Penjelasan Fitur
Dashboard pasien dirancang untuk memberikan transparansi informasi antrian secara langsung. Halaman ini memuat dua komponen utama:
1. **Banner Antrian Aktif**: Tampil otomatis jika pasien memiliki antrian yang belum selesai diperiksa. Memuat informasi nama poliklinik, dokter yang bertugas, tanggal & jam praktik, nomor rekam medis, nomor antrian milik pasien, nomor antrian yang sedang dilayani saat ini (live update), serta pesan status giliran. Jika tidak memiliki antrian aktif, menampilkan empty state informatif disertai tombol pendaftaran.
2. **Tabel Poliklinik & Jadwal Berjalan**: Menampilkan daftar seluruh jadwal dokter di klinik beserta status nomor antrian yang sedang dilayani pada masing-masing jadwal secara real-time.

#### B. Cara Penggunaan
1. Login sebagai pasien (`pasien@gmail.com`).
2. Masuk ke halaman **Dashboard Pasien** (`/pasien/dashboard`).
3. Pasien dapat memantau pergerakan antriannya tanpa perlu me-refresh halaman.
4. Klik tombol **Daftar Poli Sekarang** jika belum memiliki nomor antrian aktif.

#### C. Constraint Teknis yang Diimplementasikan
- **Pencegahan Pendaftaran Ganda**: Pasien dilarang keras mendaftar ke dua poli berbeda dalam waktu bersamaan.
- **Validasi Keluhan Pasien**: Input keluhan bersifat wajib diisi (`required|string|min:3`) dengan pesan error validasi berbahasa Indonesia, untuk memastikan dokter mendapatkan informasi gejala awal dan mencegah error database (*SQL Column cannot be null*).
- **Validasi Server-Side**: Pada `PoliController::submit()`, dilakukan pengecekan relasi `whereDoesntHave('periksa')`. Jika pasien masih memiliki riwayat pendaftaran aktif yang belum memiliki catatan pemeriksaan, pendaftaran baru akan ditolak dengan pesan error yang jelas dan dialihkan kembali ke form pendaftaran.
- **Pendaftaran Ulang**: Pasien hanya diizinkan mendaftar kembali ke klinik setelah pemeriksaan pada antrian sebelumnya dinyatakan selesai oleh dokter.

---

### Fitur 2: Sistem Notifikasi Lonceng (Database Channel)

#### A. Penjelasan Fitur
Sistem notifikasi dalam aplikasi poliklinik menggunakan mekanisme database channel bawaan Laravel:
- Ikon lonceng interaktif pada navbar header dengan counter badge mengambang di sudut luar (`-top-1.5 -right-2.5` bergaris tepi `ring-2 ring-white`) sehingga angka jumlah notifikasi yang belum dibaca (`unreadCount`) tampil rapi tanpa menutupi badan ikon lonceng.
- Dropdown notifikasi responsif dengan indikator visual yang membedakan antara notifikasi yang belum dibaca (background kontras) dan sudah dibaca.
- Tombol aksi cepat untuk menandai notifikasi sebagai sudah dibaca (`markAsRead`) dan aksi *"Tandai Semua Dibaca"*.
- Halaman riwayat lengkap notifikasi pada route `/notifications`.

#### B. Cara Penggunaan
1. **Notifikasi ke Dokter**: Saat pasien selesai mengunggah foto bukti pembayaran, dokter pemeriksa menerima notifikasi: *"Pasien [Nama Pasien] telah mengunggah bukti pembayaran untuk pemeriksaan tanggal [Tgl]"*.
2. **Notifikasi ke Pasien**: Saat admin memverifikasi pembayaran menjadi lunas, pasien menerima notifikasi: *"Pembayaran Anda untuk pemeriksaan di Poli [Nama Poli] telah diverifikasi (Lunas)"*.
3. Pengguna dapat mengklik ikon lonceng pada navbar di pojok kanan atas untuk membaca dan menandai notifikasi.

#### C. Constraint Teknis yang Diimplementasikan
- **Laravel Notification Classes**: Menggunakan class khusus `PembayaranDiverifikasiNotification` dan `BuktiPembayaranDiuploadNotification` yang mengimplementasikan method `via($notifiable)` dengan return `['database']`.
- **Tabel `notifications`**: Disimpan pada tabel standar Laravel dengan format JSON pada kolom `data`.
- **Isolasi Keamanan Data**: Pengguna hanya dapat melihat dan menandai notifikasi miliknya sendiri melalui query scoped `Auth::user()->notifications` dan `Auth::user()->unreadNotifications`. Akses terhadap notifikasi pengguna lain dicegah sepenuhnya.
- **Pencegahan Duplikasi**: Satu aksi transaksi hanya memicu tepat satu instance notifikasi.

---

### Fitur 3: Riwayat Pendaftaran Poli (Role Pasien)

#### A. Penjelasan Fitur
Histori lengkap pendaftaran berobat pasien yang tercatat pada sistem:
- Menu baru **Riwayat Pendaftaran** pada navigasi sidebar pasien dengan ikon Font Awesome `<i class="fas fa-clock-rotate-left"></i>`.
- Tabel riwayat pendaftaran yang menampilkan nama poli, dokter pemeriksa, tanggal/jadwal periksa, nomor antrian, dan status pemeriksaan (Selesai / Menunggu).
- Halaman detail pemeriksaan terpisah yang menampilkan catatan dokter, daftar obat yang diresepkan beserta dosis/harga, dan rincian total biaya pemeriksaan.

#### B. Cara Penggunaan
1. Login sebagai akun Pasien.
2. Klik menu **Riwayat Pendaftaran** pada sidebar.
3. Klik tombol **Detail** pada antrian yang telah berstatus *Sudah Diperiksa* untuk melihat resep obat dan catatan dokter.

#### C. Constraint Teknis yang Diimplementasikan
- **Pengurutan Data**: Data diurutkan dari yang paling baru menggunakan `latest('id')`.
- **Eager Loading Anti-N+1**: Pengambilan data menggunakan `with(['jadwalPeriksa.dokter.poli', 'periksa.detailPeriksas.obat'])` untuk memastikan performa query efisien dan tidak membebani database.
- **Otorisasi Data**: Pasien hanya dapat melihat riwayat pendaftaran miliknya sendiri (`where('id_pasien', Auth::id())`).

---

### Fitur 4: Sistem Antrian Real-Time (Laravel Reverb & Echo)

#### A. Penjelasan Fitur
Fitur pembaruan antrian secara langsung tanpa membutuhkan reload atau refresh halaman browser:
- Menggunakan **Laravel Reverb** sebagai server WebSocket lokal berkecepatan tinggi dan **Laravel Echo** di sisi frontend.
- Terdapat indikator status live pada header Dashboard Pasien dan Periksa Pasien Dokter: badge hijau bertuliskan **`WebSocket Terhubung`**.
- Pada sisi dokter, terdapat tombol **Panggil** (`<i class="fas fa-bullhorn"></i>`) di samping tombol periksa untuk memanggil pasien berikutnya ke dalam ruang praktik secara AJAX.
- Begitu dokter mengklik tombol Panggil atau menyimpan hasil pemeriksaan, nomor antrian yang sedang dilayani pada banner aktif dan tabel jadwal di layar pasien langsung ter-update secara otomatis disertai efek visual highlight dan live toast notification.
- Di sisi dokter, apabila terdapat pasien baru yang mendaftar, baris antrian baru akan otomatis ditambahkan ke tabel dokter secara real-time tanpa reload.

#### B. Cara Penggunaan
1. Pastikan server Reverb berjalan (`php artisan reverb:start`).
2. Buka dua jendela browser berdampingan:
   - Jendela 1: Dashboard Pasien (`/pasien/dashboard`).
   - Jendela 2: Halaman Periksa Dokter (`/dokter/periksa-pasien`).
3. Pada jendela Dokter, klik tombol **Panggil** pada salah satu baris antrian.
4. Amati jendela Pasien: nomor antrian yang sedang dilayani pada papan nomor dan tabel jadwal langsung berubah seketika secara otomatis.

#### C. Constraint Teknis yang Diimplementasikan
- **Broadcaster Driver**: Driver `reverb` dikonfigurasi pada `.env` dan `config/broadcasting.php`.
- **Event `ShouldBroadcastNow`**: Event `App\Events\AntrianUpdatedEvent` mengimplementasikan interface `ShouldBroadcastNow` sehingga transmisi broadcast dikirimkan secara instan melalui socket tanpa tertahan antrian job queue worker.
- **Public Channel**: Event disiarkan melalui public channel `antrian-poli` dengan nama event `AntrianUpdated`.
- **Persistensi Database**: Kolom `nomor_dilayani` ditambahkan pada tabel `jadwal_periksa` sehingga status antrian tetap sinkron meskipun halaman baru dibuka pertama kali atau setelah browser ditutup.

---

### Fitur 5: Export Excel (Admin & Dokter)

#### A. Penjelasan Fitur
Fitur download data ke dalam format berkas spreadsheet Microsoft Excel (.xls) yang disediakan pada 5 halaman aplikasi:
1. **Export Data Dokter** (`/admin/dokter/export`) — Role Admin
2. **Export Data Pasien** (`/admin/pasien/export`) — Role Admin
3. **Export Data Obat** (`/admin/obat/export`) — Role Admin (termasuk kolom stok dan harga)
4. **Export Jadwal Periksa** (`/dokter/jadwal-periksa/export`) — Role Dokter
5. **Export Riwayat Pasien** (`/dokter/riwayat-pasien/export`) — Role Dokter

Setiap halaman indeks terkait dilengkapi dengan tombol hijau elegan **Export Excel** berikon `<i class="fas fa-file-excel"></i>`.

#### B. Cara Penggunaan
1. Login sebagai Admin atau Dokter.
2. Buka salah satu dari kelima halaman di atas.
3. Klik tombol hijau **Export Excel** di kanan atas header halaman. File `.xls` akan otomatis terunduh dan siap dibuka di Microsoft Excel / Google Sheets.

#### C. Constraint Teknis yang Diimplementasikan
- **Independensi Dependensi**: Dibuat menggunakan service mandiri `App\Services\ExcelExportService` berbasis XML SpreadsheetML yang menghasilkan dokumen native Excel dengan format sel rapi dan styling header warna biru tua. Solusi ini menjamin kompatibilitas 100% tanpa risiko kegagalan library eksternal akibat ekstensi zip PHP lokal yang dibatasi.
- **Kolom Stok Obat**: Melalui migrasi `2026_10_27_030933_add_stok_to_obat_table.php`, tabel `obat` ditambahkan kolom `stok` (integer). Form tambah, edit, dan tampilan tabel obat telah disesuaikan secara menyeluruh.
- **Filtering Data Kepemilikan Dokter**: Pada export jadwal periksa dan riwayat pasien dokter, data difilter secara ketat hanya mencakup jadwal dan pasien yang diperiksa oleh dokter yang sedang login (`id_dokter == Auth::id()`).

---

### Fitur 6: Upload Bukti Pembayaran & Verifikasi Admin

#### A. Penjelasan Fitur
Alur pembayaran digital atas tagihan pemeriksaan dan resep obat yang diterima pasien:
- Menu baru **Pembayaran** pada sidebar pasien: menampilkan daftar tagihan pemeriksaan yang belum dibayar, menunggu verifikasi, maupun yang telah lunas.
- Pasien dapat mengunggah foto struk/bukti transfer bank.
- Menu baru **Verifikasi Pembayaran** pada sidebar admin: admin dapat memeriksa detail tagihan, melihat pratinjau bukti transfer foto yang diunggah, serta menekan tombol **Konfirmasi Lunas** atau tombol **Tolak**.

#### B. Cara Penggunaan
1. **Pasien**: Masuk ke menu *Pembayaran*, pilih pemeriksaan yang belum dibayar, klik *Bayar / Upload Bukti*, pilih file gambar bukti bayar, lalu klik *Unggah Bukti Pembayaran*.
2. **Admin**: Masuk ke menu *Verifikasi Pembayaran*, klik tombol *Verifikasi* pada tagihan berstatus *Menunggu Verifikasi*, cek foto bukti bayar, lalu klik *Konfirmasi Lunas*. Status pembayaran otomatis berganti menjadi *Lunas*.

#### C. Constraint Teknis yang Diimplementasikan
- **Skema ENUM Database**: Kolom `status_pembayaran` pada tabel `periksa` menggunakan enum:
  - `'menunggu_pembayaran'` (default saat pemeriksaan baru dicatat dokter)
  - `'menunggu_verifikasi'` (setelah pasien berhasil mengunggah foto bukti)
  - `'lunas'` (setelah admin mengonfirmasi pembayaran)
- **Validasi File Ketat**: File bukti transfer divalidasi dengan aturan `required|image|mimes:jpeg,png,jpg,webp|max:3072` (maksimal 3MB).
- **Penyimpanan Berkas Terisolasi**: Berkas foto bukti bayar disimpan pada direktori aman `storage/app/public/bukti_pembayaran` dan diakses melalui symbolic link storage Laravel.

---

### Fitur 7: Ganti Password (Semua Role: Admin, Dokter, Pasien)

#### A. Penjelasan Fitur
Fitur pengelolaan keamanan akun yang dapat diakses oleh semua pengguna terdaftar:
- Dapat diakses melalui menu navigasi sidebar **Ganti Password** maupun menu dropdown profil pengguna pada header navbar.
- Form keamanan 3 kolom:
  1. Password Lama
  2. Password Baru
  3. Konfirmasi Password Baru

#### B. Cara Penggunaan
1. Login dengan role apa pun (Admin, Dokter, atau Pasien).
2. Klik avatar/nama Anda di header pojok kanan atas, lalu pilih **Ganti Password** (atau klik menu *Ganti Password* di sidebar).
3. Masukkan password lama yang saat ini digunakan.
4. Masukkan password baru minimal 8 karakter dan konfirmasikan kembali.
5. Klik **Simpan Perubahan Password**.

#### C. Constraint Teknis yang Diimplementasikan
- **Form Request Validation**: Divalidasi di sisi server menggunakan class `App\Http\Requests\UpdatePasswordRequest`:
  - `current_password`: Wajib diisi dan harus cocok dengan password pengguna di database (`Hash::check`).
  - `password`: Wajib diisi, minimal 8 karakter, wajib sama dengan kolom `password_confirmation`, dan **tidak boleh sama dengan password lama** (`different:current_password`).
- **Enkripsi Hash**: Password baru dienkripsi secara aman menggunakan fungsi hash algoritma Bcrypt bawaan Laravel (`Hash::make()`) sebelum disimpan ke basis data.
- **Keamanan Otorisasi**: Pengguna hanya memiliki wewenang untuk mengubah password akunnya sendiri yang sedang dalam keadaan terotentikasi.

---

### Penugasan Tambahan (Prasyarat Soal)

1. **Penugasan 1: Pagination Riwayat Pasien Dokter**:
   - Metode `RiwayatPasienController::index()` dikonfigurasi menggunakan Eloquent `paginate(10)` menggantikan pemanggilan seluruh baris `get()`.
   - Navigasi halaman pagination ditampilkan rapi pada footer tabel di `dokter/riwayat-pasien/index.blade.php`.
2. **Penugasan 2: Penghitungan Jumlah Obat Diresepkan**:
   - Pada `RiwayatPasienController::show($id)`, ditambahkan kalkulasi jumlah obat yang diresepkan: `$jumlahObat = $periksa->detailPeriksas->count();`.
   - Pada tampilan detail riwayat pasien, ditampilkan badge indikator informatif: `<span class="...">{{ $jumlahObat }} obat diresepkan</span>`.

---

## Standar Kualitas & Aturan Desain (Zero-Emoji Policy)

- **Bebas Karakter Emoji**: Seluruh antarmuka aplikasi, pesan alert flash session, dan notifikasi sistem murni menggunakan ikon **Font Awesome 7** (`<i class="fas fa-..."></i>`) atau komponen UI bawaan Tailwind & DaisyUI, tanpa menggunakan karakter emotikon/emoji.
- **Konsistensi UI/UX**: Mengadopsi palet warna modern, tata letak kartu rounded elegan, status badge berpenanda visual jelas, serta penanganan feedback form yang interaktif.
