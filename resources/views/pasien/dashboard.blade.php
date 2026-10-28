<x-layouts.app title="Dashboard Pasien">

    {{-- Welcome Header --}}
    <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div>
            <h1 class="text-2xl font-bold text-slate-800">
                Selamat Datang, {{ $user->nama }}!
            </h1>
            <p class="text-sm text-slate-500 mt-1">
                Pantau status antrian pemeriksaan Anda dan jadwal praktik dokter secara langsung (real-time).
            </p>
        </div>

        {{-- Live Indicator Badge --}}
        <div>
            <span id="wsStatusBadge" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                <i class="fas fa-circle text-[8px] animate-pulse text-emerald-500"></i>
                <span>WebSocket Terhubung</span>
            </span>
        </div>
    </div>

    {{-- Live Queue Toast Notification --}}
    <div id="liveQueueToast" class="fixed bottom-6 right-6 z-50 hidden transition-all duration-300">
        <div class="bg-indigo-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-indigo-700 backdrop-blur-md">
            <div class="w-10 h-10 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                <i class="fas fa-bullhorn text-base animate-bounce"></i>
            </div>
            <div>
                <span class="font-bold text-sm block" id="liveToastTitle">Antrian Diperbarui</span>
                <span class="text-xs text-indigo-200" id="liveToastDesc">Sedang memproses antrian...</span>
            </div>
        </div>
    </div>

    {{-- ================= BANNER ANTRIAN AKTIF ================= --}}
    @if($antrianAktif)
    <div class="card bg-gradient-to-r from-[#1e2d6b] to-[#2d4499] text-white shadow-lg rounded-2xl mb-8 overflow-hidden border border-indigo-900/30">
        <div class="card-body p-6 sm:p-8">
            <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6">

                {{-- Kolom Kiri: Detail Antrian --}}
                <div class="space-y-3">
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-white/15 text-white text-xs font-semibold backdrop-blur-sm">
                        <i class="fas fa-circle-dot text-emerald-400 animate-pulse text-[10px]"></i>
                        Antrian Aktif Anda
                    </div>

                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold tracking-tight">
                            {{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}
                        </h2>
                        <p class="text-slate-200 text-sm mt-1 flex items-center gap-2">
                            <i class="fas fa-user-doctor text-indigo-200 text-xs"></i>
                            Dr. {{ $antrianAktif->jadwalPeriksa->dokter->nama ?? '-' }}
                        </p>
                    </div>

                    <div class="flex flex-wrap items-center gap-4 text-xs text-slate-200 pt-1">
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-calendar-day text-indigo-300"></i>
                            <span>{{ $antrianAktif->jadwalPeriksa->hari ?? '-' }}</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-clock text-indigo-300"></i>
                            <span>{{ substr($antrianAktif->jadwalPeriksa->jam_mulai, 0, 5) }} - {{ substr($antrianAktif->jadwalPeriksa->jam_selesai, 0, 5) }} WIB</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            <i class="fas fa-clipboard-user text-indigo-300"></i>
                            <span>No. RM: {{ $user->no_rm }}</span>
                        </div>
                    </div>
                </div>

                {{-- Kolom Kanan: Papan Nomor Antrian --}}
                <div class="flex flex-wrap sm:flex-nowrap items-center gap-4 bg-white/10 p-4 rounded-xl border border-white/10 backdrop-blur-sm">

                    {{-- Nomor Pasien --}}
                    <div class="text-center px-4 py-2 border-r border-white/15 min-w-[110px]">
                        <span class="block text-xs uppercase tracking-wider text-slate-300 font-medium">
                            Nomor Anda
                        </span>
                        <span class="text-4xl sm:text-5xl font-black text-white">
                            #{{ $antrianAktif->no_antrian }}
                        </span>
                    </div>

                    {{-- Nomor Sedang Dilayani --}}
                    <div class="text-center px-4 py-2 min-w-[130px]">
                        <span class="block text-xs uppercase tracking-wider text-indigo-200 font-medium flex items-center justify-center gap-1">
                            <i class="fas fa-bullhorn text-xs"></i> Sedang Dilayani
                        </span>
                        <span id="currentServingNumber" class="text-4xl sm:text-5xl font-black text-amber-300 transition-all duration-300">
                            #{{ $antrianAktif->jadwalPeriksa->nomor_dilayani ?: '-' }}
                        </span>
                    </div>

                </div>

            </div>

            {{-- Status Banner Footer --}}
            <div class="mt-4 pt-4 border-t border-white/15 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 text-xs text-slate-200">
                <div id="bannerStatusContainer" class="flex items-center gap-2">
                    @if($antrianAktif->jadwalPeriksa->nomor_dilayani == $antrianAktif->no_antrian)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-500/30 text-emerald-200 font-bold border border-emerald-400/50 animate-pulse">
                        <i class="fas fa-bell text-xs"></i> Giliran Anda Sekarang! Silakan menuju ruang periksa.
                    </span>
                    @elseif($antrianAktif->jadwalPeriksa->nomor_dilayani < $antrianAktif->no_antrian && $antrianAktif->jadwalPeriksa->nomor_dilayani > 0)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-amber-500/20 text-amber-200 font-medium border border-amber-400/30">
                        <i class="fas fa-hourglass-half text-xs"></i>
                        Menunggu antrian (Tersisa {{ $antrianAktif->no_antrian - $antrianAktif->jadwalPeriksa->nomor_dilayani }} antrian lagi sebelum giliran Anda)
                    </span>
                    @elseif($antrianAktif->jadwalPeriksa->nomor_dilayani > $antrianAktif->no_antrian)
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-slate-500/30 text-slate-200 font-medium border border-slate-400/30">
                        <i class="fas fa-check-double text-xs"></i>
                        Pemeriksaan Anda telah selesai.
                    </span>
                    @else
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white/10 text-slate-200 font-medium">
                        <i class="fas fa-clock text-xs"></i>
                        Menunggu dokter memulai pemeriksaan
                    </span>
                    @endif
                </div>

                <a href="{{ route('pasien.riwayat.index') }}" class="inline-flex items-center gap-1 text-white hover:text-indigo-200 transition font-medium underline underline-offset-4">
                    <span>Lihat di Riwayat Pendaftaran</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>

        </div>
    </div>
    @else
    {{-- Banner Kosong (Jika Pasien Tidak Memiliki Antrian Aktif) --}}
    <div class="card bg-base-100 border border-slate-200 shadow-sm rounded-2xl mb-8">
        <div class="card-body p-6 sm:p-7 flex flex-col sm:flex-row items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center shrink-0">
                    <i class="fas fa-clipboard-check text-xl"></i>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-base">
                        Tidak Ada Antrian Aktif
                    </h3>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Anda belum mendaftar pemeriksaan poli hari ini atau seluruh pemeriksaan Anda telah selesai.
                    </p>
                </div>
            </div>

            <a href="{{ route('pasien.daftar') }}"
                class="inline-flex items-center gap-2 px-5 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-xl text-sm font-semibold transition shrink-0 shadow-sm">
                <i class="fas fa-plus text-xs"></i>
                Daftar Poli Sekarang
            </a>
        </div>
    </div>
    @endif

    {{-- ================= TABEL JADWAL POLIKLINIK & DOKTER ================= --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
        <div class="card-body p-0">

            {{-- Card Header --}}
            <div class="px-6 py-5 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                <div>
                    <h2 class="text-lg font-bold text-slate-800">
                        Jadwal Poliklinik & Antrian Berjalan
                    </h2>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Daftar jadwal dokter spesialis dan nomor antrian yang sedang dilayani pada masing-masing jadwal (live update).
                    </p>
                </div>

                <div class="text-xs text-slate-400 flex items-center gap-1.5 self-start sm:self-auto">
                    <i class="fas fa-bolt text-amber-500"></i>
                    <span>Pembaruan Otomatis Tanpa Reload (Reverb)</span>
                </div>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Poli</th>
                            <th class="px-6 py-4">Dokter</th>
                            <th class="px-6 py-4">Hari</th>
                            <th class="px-6 py-4">Jam Periksa</th>
                            <th class="px-6 py-4 text-center">Sedang Dilayani</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody class="text-slate-700">
                        @forelse($jadwals as $index => $jadwal)
                        <tr class="hover:bg-slate-50 transition" id="jadwal-row-{{ $jadwal->id }}">

                            {{-- No --}}
                            <td class="px-6 py-4 text-slate-400 font-medium">
                                {{ $index + 1 }}
                            </td>

                            {{-- Poli --}}
                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $jadwal->dokter->poli->nama_poli ?? 'Poli Umum' }}
                            </td>

                            {{-- Dokter --}}
                            <td class="px-6 py-4 text-slate-700 font-medium">
                                Dr. {{ $jadwal->dokter->nama ?? '-' }}
                            </td>

                            {{-- Hari --}}
                            <td class="px-6 py-4 text-slate-600">
                                <span class="px-2.5 py-1 bg-slate-100 text-slate-700 rounded-md text-xs font-semibold">
                                    {{ $jadwal->hari }}
                                </span>
                            </td>

                            {{-- Jam Periksa --}}
                            <td class="px-6 py-4 text-slate-600">
                                {{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }}
                            </td>

                            {{-- Nomor Sedang Dilayani --}}
                            <td class="px-6 py-4 text-center" id="serving-badge-{{ $jadwal->id }}">
                                @if($jadwal->nomor_dilayani > 0)
                                <span class="inline-flex items-center justify-center px-3 py-1 bg-amber-50 text-amber-700 border border-amber-200 rounded-full font-bold text-xs">
                                    <i class="fas fa-bullhorn text-[10px] mr-1.5"></i>
                                    Antrian #{{ $jadwal->nomor_dilayani }}
                                </span>
                                @else
                                <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-xs font-normal">
                                    Belum ada antrian
                                </span>
                                @endif
                            </td>

                            {{-- Aksi --}}
                            <td class="px-6 py-4 text-right">
                                @if($antrianAktif)
                                <span class="text-xs text-slate-400 italic" title="Selesaikan antrian aktif terlebih dahulu">
                                    Ada antrian aktif
                                </span>
                                @else
                                <a href="{{ route('pasien.daftar') }}"
                                    class="inline-flex items-center gap-1 px-3 py-1.5 bg-primary/10 hover:bg-primary hover:text-white text-primary rounded-lg text-xs font-semibold transition">
                                    <i class="fas fa-plus text-[10px]"></i>
                                    Daftar
                                </a>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr>
                            <td colspan="7" class="text-center py-12 text-slate-400">
                                <i class="fas fa-calendar-xmark text-3xl mb-2 block text-slate-300"></i>
                                Belum ada jadwal dokter yang terdaftar saat ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    {{-- Script Real-Time Echo --}}
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const userAntrianNo = {{ $antrianAktif ? $antrianAktif->no_antrian : 'null' }};
            const userJadwalId = {{ $antrianAktif ? $antrianAktif->id_jadwal : 'null' }};

            // Setup WebSocket Status Listener
            function initEchoListener() {
                if (typeof window.Echo === 'undefined') {
                    console.warn('Laravel Echo belum siap, mencoba kembali...');
                    setTimeout(initEchoListener, 500);
                    return;
                }

                // Update status badge from Pusher connection
                if (window.Echo.connector && window.Echo.connector.pusher) {
                    const pusherConn = window.Echo.connector.pusher.connection;

                    function updateBadgeState(state) {
                        const wsBadge = document.getElementById('wsStatusBadge');
                        if (!wsBadge) return;

                        if (state === 'connected') {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] animate-pulse text-emerald-500"></i><span>WebSocket Terhubung</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm cursor-pointer';
                            wsBadge.title = 'Terhubung ke server Reverb WebSocket.';
                        } else if (state === 'connecting') {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] animate-pulse text-amber-500"></i><span>Menghubungkan...</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm';
                        } else {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] text-slate-400"></i><span>WebSocket Standby (Klik Hubungkan)</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 shadow-sm cursor-pointer hover:bg-slate-100 transition';
                            wsBadge.title = 'Klik untuk menghubungkan ulang ke server WebSocket.';
                            wsBadge.onclick = function() {
                                window.Echo.connector.pusher.connect();
                            };
                        }
                    }

                    // Periksa status saat ini
                    updateBadgeState(pusherConn.state);

                    pusherConn.bind('state_change', function (states) {
                        console.log('WS State changed:', states.current);
                        updateBadgeState(states.current);
                    });
                }

                // Subscribe to public channel 'antrian-poli'
                console.log('Subscribing to channel antrian-poli...');
                window.Echo.channel('antrian-poli')
                    .listen('.AntrianUpdated', function (e) {
                        console.log('Pesan broadcast .AntrianUpdated diterima:', e);
                        handleAntrianUpdate(e);
                    })
                    .listen('AntrianUpdatedEvent', function (e) {
                        console.log('Pesan broadcast AntrianUpdatedEvent diterima:', e);
                        handleAntrianUpdate(e);
                    });
            }

            function handleAntrianUpdate(e) {
                // 1. Tampilkan Toast Popup Real-time
                showLiveToast(e);

                const noDilayani = Number(e.nomor_dilayani) || 0;
                const idJadwal = Number(e.id_jadwal);

                // 2. Update Badge di Tabel Jadwal Poliklinik
                const badgeCell = document.getElementById(`serving-badge-${idJadwal}`);
                const tableRow = document.getElementById(`jadwal-row-${idJadwal}`);

                if (badgeCell) {
                    if (noDilayani > 0) {
                        badgeCell.innerHTML = `
                            <span class="inline-flex items-center justify-center px-3 py-1 bg-amber-100 text-amber-800 border border-amber-300 rounded-full font-bold text-xs animate-pulse">
                                <i class="fas fa-bullhorn text-[10px] mr-1.5"></i>
                                Antrian #${noDilayani}
                            </span>
                        `;
                    } else {
                        badgeCell.innerHTML = `
                            <span class="inline-flex items-center px-3 py-1 bg-slate-100 text-slate-400 rounded-full text-xs font-normal">
                                Belum ada antrian
                            </span>
                        `;
                    }

                    if (tableRow) {
                        tableRow.classList.add('bg-amber-100/60');
                        setTimeout(() => {
                            tableRow.classList.remove('bg-amber-100/60');
                        }, 3000);
                    }
                }

                // 3. Update Banner Antrian Aktif Pasien (Jika jadwal ini adalah jadwal pasien)
                if (userJadwalId && userJadwalId === idJadwal) {
                    const servingNumberEl = document.getElementById('currentServingNumber');
                    const statusContainer = document.getElementById('bannerStatusContainer');

                    if (servingNumberEl) {
                        servingNumberEl.innerText = `#${noDilayani > 0 ? noDilayani : '-'}`;
                        servingNumberEl.classList.add('scale-125', 'text-emerald-300');
                        setTimeout(() => {
                            servingNumberEl.classList.remove('scale-125', 'text-emerald-300');
                        }, 1200);
                    }

                    if (statusContainer && userAntrianNo !== null) {
                        if (noDilayani === userAntrianNo) {
                            statusContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-emerald-500/40 text-emerald-100 font-bold border border-emerald-400 animate-pulse text-xs">
                                    <i class="fas fa-bell text-xs"></i> Giliran Anda Sekarang! Silakan menuju ruang periksa.
                                </span>
                            `;
                        } else if (noDilayani < userAntrianNo && noDilayani > 0) {
                            const selisih = userAntrianNo - noDilayani;
                            statusContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-amber-500/20 text-amber-200 font-medium border border-amber-400/30 text-xs">
                                    <i class="fas fa-hourglass-half text-xs"></i>
                                    Menunggu antrian (Tersisa ${selisih} antrian lagi sebelum giliran Anda)
                                </span>
                            `;
                        } else if (noDilayani > userAntrianNo) {
                            statusContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white/10 text-slate-200 font-medium text-xs">
                                    <i class="fas fa-check-double text-xs"></i>
                                    Pemeriksaan Anda telah selesai.
                                </span>
                            `;
                        } else {
                            statusContainer.innerHTML = `
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-md bg-white/10 text-slate-200 font-medium text-xs">
                                    <i class="fas fa-clock text-xs"></i>
                                    Menunggu dokter memulai pemeriksaan
                                </span>
                            `;
                        }
                    }
                }
            }

            function showLiveToast(e) {
                const toast = document.getElementById('liveQueueToast');
                const toastTitle = document.getElementById('liveToastTitle');
                const toastDesc = document.getElementById('liveToastDesc');
                if (!toast) return;

                const no = e.nomor_dilayani > 0 ? `#${e.nomor_dilayani}` : '-';
                if (e.status === 'dipanggil') {
                    toastTitle.innerText = `Panggilan Antrian ${no}`;
                } else if (e.status === 'selesai') {
                    toastTitle.innerText = `Antrian Selesai - Selanjutnya ${no}`;
                } else {
                    toastTitle.innerText = `Antrian ${no} Terdaftar`;
                }

                toastDesc.innerText = `${e.nama_poli || 'Poli'} (Dr. ${e.nama_dokter || 'Dokter'})`;

                toast.classList.remove('hidden');
                setTimeout(() => {
                    toast.classList.add('hidden');
                }, 4500);
            }

            initEchoListener();
        });
    </script>

</x-layouts.app>
