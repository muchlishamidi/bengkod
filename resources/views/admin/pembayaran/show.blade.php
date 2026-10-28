<x-layouts.app title="Verifikasi Bukti Pembayaran">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.pembayaran.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Detail Verifikasi Pembayaran
            </h2>
            <p class="text-xs text-slate-500">
                Tagihan #{{ $periksa->id }} &bull; Pasien: {{ $periksa->daftarPoli->pasien->nama ?? '-' }} ({{ $periksa->daftarPoli->pasien->no_rm ?? '-' }})
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Kolom Kiri: Rincian Tagihan & Pasien --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Card Info Pasien & Pemeriksaan --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">
                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center gap-2">
                        <i class="fas fa-hospital-user text-primary"></i>
                        Data Pasien & Pemeriksaan
                    </h3>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 text-sm">
                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Nama Pasien</span>
                            <span class="font-semibold text-slate-800 text-base">
                                {{ $periksa->daftarPoli->pasien->nama ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Nomor Rekam Medis (No. RM)</span>
                            <span class="font-mono font-bold text-indigo-700">
                                {{ $periksa->daftarPoli->pasien->no_rm ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Poli / Dokter</span>
                            <span class="text-slate-700">
                                {{ $periksa->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }} &bull; Dr. {{ $periksa->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                            </span>
                        </div>

                        <div>
                            <span class="text-xs font-medium text-slate-400 block">Waktu Pemeriksaan</span>
                            <span class="text-slate-700">
                                {{ \Carbon\Carbon::parse($periksa->tgl_periksa)->format('d F Y, H:i') }} WIB
                            </span>
                        </div>

                        <div class="sm:col-span-2 pt-2 border-t border-slate-100">
                            <span class="text-xs font-medium text-slate-400 block">Catatan Dokter:</span>
                            <span class="text-slate-700 italic text-xs">
                                "{{ $periksa->catatan ?: 'Tidak ada catatan' }}"
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Card Rincian Obat & Biaya --}}
            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
                <div class="px-6 py-4 border-b border-slate-200 bg-slate-50/70">
                    <h3 class="font-bold text-slate-800 text-sm">
                        Rincian Tagihan Biaya Periksa
                    </h3>
                </div>

                <div class="card-body p-0">
                    <table class="table w-full text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-3">Deskripsi Tagihan</th>
                                <th class="px-6 py-3 text-right">Biaya</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr>
                                <td class="px-6 py-3.5 text-slate-800 font-medium">
                                    Jasa Pemeriksaan Dokter
                                </td>
                                <td class="px-6 py-3.5 text-right text-slate-700">
                                    Rp 150.000
                                </td>
                            </tr>

                            @if($periksa->detailPeriksas)
                                @foreach($periksa->detailPeriksas as $detail)
                                <tr>
                                    <td class="px-6 py-3.5 text-slate-700">
                                        {{ $detail->obat->nama_obat ?? 'Obat' }}
                                        <span class="text-xs text-slate-400 ml-1">({{ $detail->obat->kemasan ?? '-' }})</span>
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
                        <span class="font-bold text-slate-800 text-base">Total Tagihan</span>
                        <span class="text-2xl font-black text-[#2d4499]">
                            Rp {{ number_format($periksa->biaya_periksa, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

        </div>

        {{-- Kolom Kanan: Pratinjau Bukti & Aksi Verifikasi --}}
        <div class="space-y-6">

            <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
                <div class="card-body p-6">

                    <h3 class="font-bold text-slate-800 text-base mb-4 flex items-center justify-between">
                        <span>Bukti Pembayaran</span>
                        @if($periksa->status_pembayaran === 'lunas')
                        <span class="badge badge-success text-white text-xs font-semibold">
                            <i class="fas fa-check-circle mr-1"></i> Lunas
                        </span>
                        @elseif($periksa->status_pembayaran === 'menunggu_verifikasi')
                        <span class="badge badge-info text-white text-xs font-semibold">
                            <i class="fas fa-clock mr-1"></i> Perlu Verifikasi
                        </span>
                        @else
                        <span class="badge badge-warning text-xs font-semibold">
                            <i class="fas fa-hourglass-start mr-1"></i> Belum Bayar
                        </span>
                        @endif
                    </h3>

                    {{-- Foto Bukti --}}
                    @if($periksa->bukti_pembayaran)
                    <div class="space-y-2 mb-6">
                        <a href="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" target="_blank"
                            class="block border-2 border-slate-200 rounded-xl overflow-hidden hover:opacity-90 transition group relative">
                            <img src="{{ asset('storage/' . $periksa->bukti_pembayaran) }}" alt="Bukti Transfer" class="w-full h-64 object-cover">
                            <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 flex items-center justify-center text-white text-xs font-semibold transition gap-1.5">
                                <i class="fas fa-magnifying-glass-plus"></i> Buka Ukuran Penuh
                            </div>
                        </a>
                        <div class="flex items-center justify-between text-xs text-slate-400 pt-1">
                            <span>Waktu Upload:</span>
                            <span class="font-medium text-slate-600">{{ $periksa->tgl_bayar ? \Carbon\Carbon::parse($periksa->tgl_bayar)->format('d/m/Y H:i') : '-' }} WIB</span>
                        </div>
                    </div>
                    @else
                    <div class="py-12 text-center text-slate-400 bg-slate-50 rounded-xl border border-dashed border-slate-200 mb-6">
                        <i class="fas fa-image text-3xl mb-2 text-slate-300 block"></i>
                        <span class="text-xs">Pasien belum mengunggah foto bukti pembayaran.</span>
                    </div>
                    @endif

                    {{-- Tombol Aksi Verifikasi --}}
                    @if($periksa->status_pembayaran === 'menunggu_verifikasi')
                    <div class="space-y-3 pt-4 border-t border-slate-100">
                        <form action="{{ route('admin.pembayaran.konfirmasi', $periksa->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Apakah Anda yakin ingin memverifikasi pembayaran ini sebagai LUNAS?')"
                                class="btn bg-emerald-600 hover:bg-emerald-700 text-white w-full rounded-xl text-sm font-semibold shadow-sm">
                                <i class="fas fa-circle-check mr-1.5"></i>
                                Konfirmasi Pembayaran Lunas
                            </button>
                        </form>

                        <form action="{{ route('admin.pembayaran.tolak', $periksa->id) }}" method="POST">
                            @csrf
                            <button type="submit"
                                onclick="return confirm('Tolak bukti pembayaran ini? Status akan dikembalikan ke Belum Bayar agar pasien dapat mengupload ulang.')"
                                class="btn btn-ghost bg-red-50 hover:bg-red-100 text-red-600 w-full rounded-xl text-xs font-semibold">
                                <i class="fas fa-circle-xmark mr-1.5"></i>
                                Tolak Bukti Transfer
                            </button>
                        </form>
                    </div>
                    @elseif($periksa->status_pembayaran === 'lunas')
                    <div class="alert alert-success text-xs rounded-xl">
                        <i class="fas fa-check-double text-base"></i>
                        <span>Pembayaran ini telah diverifikasi dan berstatus <strong>LUNAS</strong>.</span>
                    </div>
                    @else
                    <div class="alert alert-warning text-xs rounded-xl">
                        <i class="fas fa-circle-info text-base"></i>
                        <span>Menunggu pasien melakukan transfer dan mengunggah foto bukti bayar.</span>
                    </div>
                    @endif

                </div>
            </div>

        </div>

    </div>

</x-layouts.app>
