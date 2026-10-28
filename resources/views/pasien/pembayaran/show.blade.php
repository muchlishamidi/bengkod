<x-layouts.app title="Detail Tagihan & Pembayaran">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.pembayaran.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Detail Tagihan & Pembayaran
            </h2>
            <p class="text-xs text-slate-500">
                Tagihan Pemeriksaan #{{ $periksa->id }} &bull; Tanggal {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d F Y') }}
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Rincian Biaya & Pemeriksaan --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card Info Pasien & Poli --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">
                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
                        <i class="fas fa-notes-medical text-primary"></i>
                        Informasi Pemeriksaan
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Poli Tujuan</span>
                            <span class="font-semibold text-slate-800">
                                {{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Dokter Pemeriksa</span>
                            <span class="font-semibold text-slate-800">
                                Dr. {{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Nomor Antrian</span>
                            <span class="font-semibold text-slate-800">
                                #{{ $periksa->daftarPoli->no_antrian }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Tanggal Periksa</span>
                            <span class="text-slate-700">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d/m/Y H:i') }} WIB
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Rincian Obat & Jasa --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Rincian Tagihan Medis
                    </h3>
                </div>

                <div class="card-body p-0">
                    <table class="table w-full text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Komponen Biaya</th>
                                <th class="px-6 py-3">Keterangan</th>
                                <th class="px-6 py-3 text-right">Subtotal</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            {{-- Biaya Dokter --}}
                            <tr>
                                <td class="px-6 py-3.5 font-medium text-slate-800">
                                    Jasa Pemeriksaan Dokter
                                </td>
                                <td class="px-6 py-3.5 text-slate-500 text-xs">
                                    Tarif dasar periksa
                                </td>
                                <td class="px-6 py-3.5 text-right font-medium text-slate-700">
                                    Rp 150.000
                                </td>
                            </tr>

                            {{-- Obat-obatan --}}
                            @php $totalObat = 0; @endphp
                            @if($periksa->detailPeriksas && $periksa->detailPeriksas->count() > 0)
                                @foreach($periksa->detailPeriksas as $detail)
                                @php $totalObat += ($detail->obat->harga ?? 0); @endphp
                                <tr>
                                    <td class="px-6 py-3.5 text-slate-700">
                                        {{ $detail->obat->nama_obat ?? 'Obat' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-slate-500 text-xs">
                                        {{ $detail->obat->kemasan ?? '-' }}
                                    </td>
                                    <td class="px-6 py-3.5 text-right text-slate-700">
                                        Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}
                                    </td>
                                </tr>
                                @endforeach
                            @endif
                        </tbody>
                    </table>

                    <div class="p-6 bg-slate-50/50 border-t border-slate-200 flex items-center justify-between">
                        <span class="font-bold text-slate-800 text-base">Total yang Harus Dibayar</span>
                        <span class="text-2xl font-black text-[#2d4499]">
                            Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            {{-- Rekening Pembayaran --}}
            <div class="card bg-indigo-50/60 border border-indigo-100 rounded-2xl p-6">
                <h4 class="font-bold text-indigo-950 text-sm mb-3 flex items-center gap-2">
                    <i class="fas fa-building-columns text-indigo-600"></i>
                    Petunjuk Rekening Transfer Klinik
                </h4>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 text-xs text-indigo-900">
                    <div class="bg-white p-3 rounded-xl border border-indigo-100">
                        <span class="font-semibold block text-slate-700">Bank Central Asia (BCA)</span>
                        <span class="font-mono text-sm font-bold text-indigo-600 select-all">123-456-7890</span>
                        <span class="block text-slate-500 mt-0.5">a.n. Poliklinik App Bengkel Koding</span>
                    </div>
                    <div class="bg-white p-3 rounded-xl border border-indigo-100">
                        <span class="font-semibold block text-slate-700">Bank Mandiri</span>
                        <span class="font-mono text-sm font-bold text-indigo-600 select-all">987-654-3210</span>
                        <span class="block text-slate-500 mt-0.5">a.n. Poliklinik App Bengkel Koding</span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Status & Form Upload Bukti Pembayaran --}}
        <div class="space-y-6">

            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">

                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center justify-between">
                        <span>Status Pembayaran</span>
                        @if($periksa->status_pembayaran === 'lunas')
                        <span class="badge badge-success text-white text-xs font-semibold">
                            <i class="fas fa-check-circle mr-1"></i> Lunas
                        </span>
                        @elseif($periksa->status_pembayaran === 'menunggu_verifikasi')
                        <span class="badge badge-info text-white text-xs font-semibold">
                            <i class="fas fa-clock mr-1"></i> Menunggu Verifikasi
                        </span>
                        @else
                        <span class="badge badge-warning text-xs font-semibold">
                            <i class="fas fa-hourglass-start mr-1"></i> Belum Dibayar
                        </span>
                        @endif
                    </h3>

                    {{-- Kondisi 1: Lunas --}}
                    @if($periksa->status_pembayaran === 'lunas')
                    <div class="alert alert-success text-xs rounded-xl mb-4">
                        <i class="fas fa-circle-check text-base"></i>
                        <span>Pembayaran Anda telah diverifikasi oleh Admin. Tagihan ini telah LUNAS.</span>
                    </div>

                    @if($periksa->bukti_pembayaran)
                    <div class="space-y-2">
                        <span class="text-xs font-medium text-slate-500">Bukti Pembayaran:</span>
                        <a href="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" target="_blank" class="block border rounded-xl overflow-hidden hover:opacity-90 transition">
                            <img src="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="w-full h-48 object-cover">
                        </a>
                        <p class="text-[11px] text-slate-400 text-center">Klik gambar untuk melihat ukuran penuh</p>
                    </div>
                    @endif

                    {{-- Kondisi 2: Menunggu Verifikasi --}}
                    @elseif($periksa->status_pembayaran === 'menunggu_verifikasi')
                    <div class="alert alert-info text-xs rounded-xl mb-4 text-white">
                        <i class="fas fa-circle-info text-base"></i>
                        <span>Bukti transfer telah berhasil diunggah pada {{ $periksa->tgl_bayar ? \Carbon\Carbon::parse($periksa->tgl_bayar)->format('d/m/Y H:i') : '-' }}. Sedang dalam proses pengecekan admin.</span>
                    </div>

                    @if($periksa->bukti_pembayaran)
                    <div class="space-y-2 mb-4">
                        <span class="text-xs font-medium text-slate-500">Bukti Terunggah:</span>
                        <a href="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" target="_blank" class="block border rounded-xl overflow-hidden hover:opacity-90 transition">
                            <img src="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" alt="Bukti Pembayaran" class="w-full h-48 object-cover">
                        </a>
                        <p class="text-[11px] text-slate-400 text-center">Klik gambar untuk melihat ukuran penuh</p>
                    </div>
                    @endif

                    {{-- Form Upload Ulang jika ingin ganti bukti --}}
                    <div class="pt-4 border-t border-slate-100">
                        <p class="text-xs font-medium text-slate-700 mb-2">Ingin mengganti bukti pembayaran?</p>
                        <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}" method="POST" enctype="multipart/form-data">
                            @csrf
                            <input type="file" name="bukti_pembayaran" accept="image/*" class="file-input file-input-bordered file-input-sm w-full rounded-lg text-xs mb-3" required>
                            @error('bukti_pembayaran')
                            <p class="text-xs text-red-500 mb-2">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="btn btn-sm bg-slate-800 hover:bg-slate-900 text-white w-full rounded-lg text-xs">
                                <i class="fas fa-rotate mr-1"></i> Unggah Ulang Bukti
                            </button>
                        </form>
                    </div>

                    {{-- Kondisi 3: Belum Bayar --}}
                    @else
                    <div class="alert alert-warning text-xs rounded-xl mb-4">
                        <i class="fas fa-triangle-exclamation text-base"></i>
                        <span>Silakan transfer total biaya ke nomor rekening yang tertera, lalu unggah foto bukti transfer di bawah ini.</span>
                    </div>

                    <form action="{{ route('pasien.pembayaran.upload', $periksa->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">
                                Foto Bukti Transfer <span class="text-red-500">*</span>
                            </label>
                            <input type="file" name="bukti_pembayaran" id="buktiInput" accept="image/*"
                                class="file-input file-input-bordered file-input-primary w-full rounded-lg text-xs @error('bukti_pembayaran') file-input-error @enderror" required>
                            <span class="text-[11px] text-slate-400 mt-1 block">Format: JPG, PNG, WEBP. Maks 3MB.</span>

                            @error('bukti_pembayaran')
                            <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <button type="submit" class="btn bg-primary hover:bg-primary/90 text-white w-full rounded-xl text-sm font-semibold shadow-sm">
                            <i class="fas fa-upload mr-1 text-xs"></i>
                            Kirim Bukti Pembayaran
                        </button>
                    </form>
                    @endif

                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
