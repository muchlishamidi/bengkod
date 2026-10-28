<x-layouts.app title="Verifikasi Pembayaran">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Verifikasi Pembayaran Pasien
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Kelola dan verifikasi bukti transfer pembayaran biaya pemeriksaan pasien poliklinik.
            </p>
        </div>

        {{-- Filter Status --}}
        <div class="flex items-center gap-2 bg-slate-100 p-1 rounded-xl text-xs font-semibold w-fit">
            <a href="{{ route('admin.pembayaran.index') }}"
                class="px-3 py-1.5 rounded-lg transition {{ !request('status') ? 'bg-white text-slate-800 shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Semua
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'menunggu_verifikasi']) }}"
                class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'menunggu_verifikasi' ? 'bg-blue-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Menunggu Verifikasi
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'menunggu_pembayaran']) }}"
                class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'menunggu_pembayaran' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Belum Bayar
            </a>
            <a href="{{ route('admin.pembayaran.index', ['status' => 'lunas']) }}"
                class="px-3 py-1.5 rounded-lg transition {{ request('status') === 'lunas' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-slate-800' }}">
                Lunas
            </a>
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
                            <th class="px-6 py-4">ID</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Poli & Dokter</th>
                            <th class="px-6 py-4">Tanggal Periksa</th>
                            <th class="px-6 py-4">Total Tagihan</th>
                            <th class="px-6 py-4 text-center">Bukti Transfer</th>
                            <th class="px-6 py-4 text-center">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-slate-700">
                        @forelse($tagihans as $tagihan)
                        <tr class="hover:bg-slate-50 transition">

                            {{-- ID --}}
                            <td class="px-6 py-4 font-mono font-bold text-slate-400">
                                #{{ $tagihan->id }}
                            </td>

                            {{-- Pasien --}}
                            <td class="px-6 py-4">
                                <div class="font-bold text-slate-800">
                                    {{ $tagihan->daftarPoli->pasien->nama ?? '-' }}
                                </div>
                                <div class="text-xs text-indigo-600 font-mono font-medium">
                                    {{ $tagihan->daftarPoli->pasien->no_rm ?? '-' }}
                                </div>
                            </td>

                            {{-- Poli & Dokter --}}
                            <td class="px-6 py-4">
                                <div class="font-semibold text-slate-800">
                                    {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                                </div>
                                <div class="text-xs text-slate-500">
                                    Dr. {{ $tagihan->daftarPoli->jadwalPeriksa->dokter->nama ?? '-' }}
                                </div>
                            </td>

                            {{-- Tanggal --}}
                            <td class="px-6 py-4 text-slate-600 text-xs">
                                <div>{{ \Carbon\Carbon::parse($tagihan->tgl_periksa)->format('d/m/Y') }}</div>
                                <div class="text-slate-400">{{ \Carbon\Carbon::parse($tagihan->tgl_periksa)->format('H:i') }} WIB</div>
                            </td>

                            {{-- Total Biaya --}}
                            <td class="px-6 py-4 font-bold text-[#2d4499]">
                                Rp {{ number_format($tagihan->biaya_periksa, 0, ',', '.') }}
                            </td>

                            {{-- Bukti Transfer --}}
                            <td class="px-6 py-4 text-center">
                                @if($tagihan->bukti_pembayaran)
                                <a href="{{ asset('storage/' . $tagihan->bukti_pembayaran) }}" target="_blank"
                                    class="inline-flex items-center gap-1.5 px-3 py-1 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-lg text-xs font-semibold border border-indigo-200 transition">
                                    <i class="fas fa-image text-xs"></i>
                                    Lihat Bukti
                                </a>
                                @else
                                <span class="text-xs text-slate-400 italic">
                                    Belum diunggah
                                </span>
                                @endif
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4 text-center">
                                @if($tagihan->status_pembayaran === 'lunas')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-check-circle text-xs"></i> Lunas
                                </span>
                                @elseif($tagihan->status_pembayaran === 'menunggu_verifikasi')
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-blue-100 text-blue-700 animate-pulse">
                                    <i class="fas fa-clock text-xs"></i> Menunggu Verifikasi
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <i class="fas fa-hourglass-start text-xs"></i> Belum Bayar
                                </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2">
                                    <a href="{{ route('admin.pembayaran.show', $tagihan->id) }}"
                                        class="inline-flex items-center gap-1 px-3 py-1.5 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs font-semibold transition">
                                        <i class="fas fa-eye text-xs"></i> Detail
                                    </a>

                                    @if($tagihan->status_pembayaran === 'menunggu_verifikasi')
                                    <form action="{{ route('admin.pembayaran.konfirmasi', $tagihan->id) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            onclick="return confirm('Konfirmasi pembayaran pasien ini sebagai LUNAS?')"
                                            class="inline-flex items-center gap-1 px-3 py-1.5 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-xs font-semibold transition shadow-sm">
                                            <i class="fas fa-check text-xs"></i> Konfirmasi Lunas
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center py-16 text-slate-400">
                                <i class="fas fa-money-check text-3xl mb-3 block text-slate-300"></i>
                                Tidak ada data tagihan pembayaran sesuai filter.
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
