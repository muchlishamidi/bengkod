<x-layouts.app title="Riwayat Pendaftaran">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Riwayat Pendaftaran Poli
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Histori pendaftaran pemeriksaan dan hasil diagnosis dokter Anda.
            </p>
        </div>

        <a href="{{ route('pasien.daftar') }}"
            class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition shadow-sm w-fit">
            <i class="fas fa-plus text-xs"></i>
            Daftar Poli Baru
        </a>
    </div>

    {{-- Card Table --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No. Antrian</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Jadwal Periksa</th>
                            <th class="px-6 py-4">Tanggal Daftar</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-sm text-slate-700">
                        @forelse($riwayatPoli as $riwayat)
                        <tr class="hover:bg-slate-50 transition">

                            {{-- No Antrian --}}
                            <td class="px-6 py-4 font-bold text-slate-800">
                                <span class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-indigo-50 text-indigo-700 font-bold">
                                    {{ $riwayat->no_antrian }}
                                </span>
                            </td>

                            {{-- Poli --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $riwayat->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poli Umum' }}
                            </td>

                            {{-- Dokter --}}
                            <td class="px-6 py-4 text-slate-600">
                                Dr. {{ $riwayat->jadwalPeriksa->dokter->nama ?? '-' }}
                            </td>

                            {{-- Jadwal Periksa --}}
                            <td class="px-6 py-4 text-slate-600">
                                <div class="font-medium text-slate-800">{{ $riwayat->jadwalPeriksa->hari ?? '-' }}</div>
                                <div class="text-xs text-slate-400">
                                    {{ $riwayat->jadwalPeriksa ? substr($riwayat->jadwalPeriksa->jam_mulai, 0, 5) . ' - ' . substr($riwayat->jadwalPeriksa->jam_selesai, 0, 5) : '-' }}
                                </div>
                            </td>

                            {{-- Tanggal Daftar --}}
                            <td class="px-6 py-4 text-slate-500">
                                {{ $riwayat->created_at ? $riwayat->created_at->format('d/m/Y H:i') : '-' }}
                            </td>

                            {{-- Status Pemeriksaan --}}
                            <td class="px-6 py-4">
                                @if($riwayat->periksa)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-100 text-emerald-700">
                                    <i class="fas fa-check-circle text-xs"></i>
                                    Sudah Diperiksa
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-100 text-amber-700">
                                    <i class="fas fa-clock text-xs"></i>
                                    Menunggu Pemeriksaan
                                </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($riwayat->periksa)
                                <a href="{{ route('pasien.riwayat.show', $riwayat->id) }}"
                                    class="inline-flex items-center gap-1.5 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-eye text-xs"></i>
                                    Detail
                                </a>
                                @else
                                <span class="text-xs text-slate-400 italic">
                                    Menunggu Giliran
                                </span>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-16 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-inbox text-4xl text-slate-300"></i>
                                    <p class="font-medium text-slate-600">Belum Ada Riwayat Pendaftaran</p>
                                    <p class="text-xs text-slate-400">Anda belum pernah melakukan pendaftaran ke poliklinik.</p>
                                    <a href="{{ route('pasien.daftar') }}" class="mt-2 btn btn-sm bg-primary text-white border-none rounded-lg px-4">
                                        <i class="fas fa-plus text-xs"></i> Daftar Sekarang
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

            {{-- Pagination Links --}}
            @if($riwayatPoli->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $riwayatPoli->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
