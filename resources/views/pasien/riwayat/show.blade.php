<x-layouts.app title="Detail Hasil Pemeriksaan">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('pasien.riwayat.index') }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Detail Hasil Pemeriksaan
            </h2>
            <p class="text-xs text-slate-500">
                Informasi diagnosis, catatan dokter, dan rincian biaya pemeriksaan Anda.
            </p>
        </div>
    </div>

    <div class="w-full flex flex-col gap-5">

        {{-- Informasi Pemeriksaan & Antrian --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800">Informasi Pendaftaran & Jadwal</h3>
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                        <i class="fas fa-check-circle text-xs"></i> Sudah Diperiksa
                    </span>
                </div>
                <div class="px-6 py-5 grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Poli Tujuan</span>
                        <span class="text-slate-800 font-semibold text-base">
                            {{ $daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli Umum' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Dokter Pemeriksa</span>
                        <span class="text-slate-800 font-semibold text-base">
                            Dr. {{ $daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Nomor Antrian</span>
                        <span class="text-slate-800 font-semibold">
                            Nomor Antrian #{{ $daftarPoli->no_antrian }}
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Jadwal Periksa</span>
                        <span class="text-slate-800">
                            {{ $daftarPoli->jadwalPeriksa->hari ?? '-' }} ({{ substr($daftarPoli->jadwalPeriksa->jam_mulai, 0, 5) }} - {{ substr($daftarPoli->jadwalPeriksa->jam_selesai, 0, 5) }})
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Waktu Pemeriksaan</span>
                        <span class="text-slate-800">
                            {{ \Carbon\Carbon::parse($daftarPoli->periksa->tgl_periksa)->format('d F Y, H:i') }} WIB
                        </span>
                    </div>

                    <div class="flex flex-col gap-1">
                        <span class="text-xs font-medium text-slate-400">Keluhan yang Disampaikan</span>
                        <span class="text-slate-700 italic">
                            "{{ $daftarPoli->keluhan ?: 'Tidak ada keluhan tertulis' }}"
                        </span>
                    </div>
                </div>
            </div>
        </div>

        {{-- Catatan Dokter --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-slate-200">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-stethoscope text-primary text-sm"></i>
                        Catatan Dokter / Diagnosis
                    </h3>
                </div>
                <div class="px-6 py-5 text-sm text-slate-700 leading-relaxed bg-slate-50/50">
                    {{ $daftarPoli->periksa->catatan ?: 'Tidak ada catatan dokter.' }}
                </div>
            </div>
        </div>

        {{-- Obat yang Diresepkan --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200">
            <div class="card-body p-0">
                <div class="px-6 py-4 border-b border-slate-200 flex items-center justify-between">
                    <h3 class="font-bold text-slate-800 flex items-center gap-2">
                        <i class="fas fa-pills text-primary text-sm"></i>
                        Obat yang Diresepkan
                    </h3>
                    <span class="text-xs font-semibold px-2.5 py-1 bg-blue-50 text-blue-600 rounded-full border border-blue-200">
                        {{ $daftarPoli->periksa->detailPeriksas ? $daftarPoli->periksa->detailPeriksas->count() : 0 }} obat
                    </span>
                </div>
                <div class="p-0">
                    @if($daftarPoli->periksa->detailPeriksas && $daftarPoli->periksa->detailPeriksas->count() > 0)
                    <table class="table table-zebra w-full text-sm">
                        <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                            <tr>
                                <th class="px-6 py-4">#</th>
                                <th class="px-6 py-4">Nama Obat</th>
                                <th class="px-6 py-4">Kemasan</th>
                                <th class="px-6 py-4 text-right">Harga</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($daftarPoli->periksa->detailPeriksas as $index => $detail)
                            <tr class="hover:bg-slate-50 transition">
                                <td class="px-6 py-4 text-slate-500">{{ $index + 1 }}</td>
                                <td class="px-6 py-4 font-semibold text-slate-800">
                                    {{ $detail->obat->nama_obat ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-500">
                                    {{ $detail->obat->kemasan ?? '-' }}
                                </td>
                                <td class="px-6 py-4 text-slate-700 text-right">
                                    Rp {{ number_format($detail->obat->harga ?? 0, 0, ',', '.') }}
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                    @else
                    <div class="px-6 py-6 text-sm text-slate-400 text-center">
                        <i class="fas fa-circle-info text-xl block mb-2 text-slate-300"></i>
                        Tidak ada obat yang diresepkan untuk pemeriksaan ini.
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Rincian Biaya --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 bg-slate-50">
                <h3 class="font-bold text-slate-800">Rincian Pembayaran</h3>
            </div>
            <div class="card-body p-6 flex flex-col gap-3 text-sm">
                <div class="flex items-center justify-between text-slate-600">
                    <span>Jasa Pemeriksaan Dokter</span>
                    <span class="font-medium text-slate-800">Rp 150.000</span>
                </div>

                @php
                    $totalObat = 0;
                    if($daftarPoli->periksa->detailPeriksas) {
                        foreach($daftarPoli->periksa->detailPeriksas as $d) {
                            $totalObat += ($d->obat->harga ?? 0);
                        }
                    }
                @endphp

                <div class="flex items-center justify-between text-slate-600">
                    <span>Subtotal Obat ({{ $daftarPoli->periksa->detailPeriksas ? $daftarPoli->periksa->detailPeriksas->count() : 0 }} item)</span>
                    <span class="font-medium text-slate-800">Rp {{ number_format($totalObat, 0, ',', '.') }}</span>
                </div>

                <div class="border-t border-slate-200 pt-3 flex items-center justify-between">
                    <span class="font-bold text-base text-slate-800">Total Biaya Periksa</span>
                    <span class="text-2xl font-bold text-[#2d4499]">
                        Rp {{ number_format($daftarPoli->periksa->biaya_periksa, 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>

    </div>

</x-layouts.app>
