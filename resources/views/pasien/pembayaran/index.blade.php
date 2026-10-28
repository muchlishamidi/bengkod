<x-layouts.app title="Pembayaran Tagihan">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Tagihan & Pembayaran
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Daftar tagihan pemeriksaan medis dan status pembayaran Anda.
            </p>
        </div>
    </div>

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Poli & Dokter</th>
                            <th class="px-6 py-4">Obat Diresepkan</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4 text-center">Status Pembayaran</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-slate-700">
                        @forelse($tagihans as $index => $item)
                        <tr class="hover:bg-slate-50 transition">

                            {{-- No --}}
                            <td class="px-6 py-4 text-slate-400 font-medium">
                                {{ $index + 1 }}
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4 text-slate-600">
                                <div class="font-semibold text-slate-800">
                                    {{ \Carbon\Carbon::parse($item->tgl_periksa)->format('d/m/Y') }}
                                </div>
                                <div class="text-xs text-slate-400">
                                    {{ \Carbon\Carbon::parse($item->tgl_periksa)->format('H:i') }} WIB
                                </div>
                            </td>

                            {{-- Poli & Dokter --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $item->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    Dr. {{ $item->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- Obat --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="px-2.5 py-1 rounded-full text-xs font-semibold bg-slate-100 text-slate-700">
                                    {{ $item->detailPeriksas ? $item->detailPeriksas->count() : 0 }} obat
                                </span>
                            </td>

                            {{-- Total Tagihan --}}
                            <td class="px-6 py-4 font-bold text-[#2d4499] text-base">
                                Rp {{ number_format($item->biaya_periksa, 0, ',', '.') }}
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($item->status_pembayaran === 'lunas')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-circle-check text-xs"></i> Lunas
                                </span>
                                @elseif($item->status_pembayaran === 'menunggu_verifikasi')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700">
                                    <i class="fas fa-spinner animate-spin text-xs"></i> Menunggu Verifikasi
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <i class="fas fa-clock text-xs"></i> Belum Dibayar
                                </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($item->status_pembayaran === 'lunas')
                                <a href="{{ route('pasien.pembayaran.show', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-receipt text-xs"></i>
                                    Lihat Bukti
                                </a>
                                @elseif($item->status_pembayaran === 'menunggu_verifikasi')
                                <a href="{{ route('pasien.pembayaran.show', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 border border-blue-200 rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-eye text-xs"></i>
                                    Cek Status
                                </a>
                                @else
                                <a href="{{ route('pasien.pembayaran.show', $item->id) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-primary hover:bg-primary/90 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                    <i class="fas fa-upload text-xs"></i>
                                    Bayar Sekarang
                                </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <i class="fas fa-receipt text-3xl mb-3 block text-slate-300"></i>
                                <p class="font-medium text-slate-600">Belum Ada Tagihan Pembayaran</p>
                                <p class="text-xs text-slate-400">Tagihan akan otomatis muncul setelah Anda selesai diperiksa oleh dokter.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination Links --}}
            @if($tagihans->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $tagihans->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
