<x-layouts.app title="Periksa Pasien">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Periksa Pasien
            </h2>
            <p class="text-xs text-slate-500 mt-1">
                Kelola antrian pasien, panggil nomor antrian, dan lakukan pencatatan pemeriksaan serta resep obat.
            </p>
        </div>

        {{-- Live WebSocket Status Badge --}}
        <div>
            <span id="wsStatusBadgeDokter" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm">
                <i class="fas fa-circle text-[8px] animate-pulse text-emerald-500"></i>
                <span>WebSocket Terhubung</span>
            </span>
        </div>
    </div>

    {{-- Alert Flash Message --}}
    @if (session('message'))
    <div class="alert alert-{{ session('type') ?? 'success' }} mb-4 rounded-xl shadow-sm" role="alert">
        <i class="fas fa-{{ session('type') == 'danger' ? 'circle-xmark' : 'circle-check' }}"></i>
        <span>{{ session('message') }}</span>
    </div>
    @endif

    {{-- Notification Toast for Real-Time & AJAX --}}
    <div id="panggilToast" class="fixed bottom-6 right-6 z-50 hidden transition-all duration-300">
        <div class="bg-indigo-900 text-white px-5 py-3.5 rounded-2xl shadow-2xl flex items-center gap-3 border border-indigo-700 backdrop-blur-md">
            <div class="w-10 h-10 rounded-xl bg-amber-400/20 text-amber-300 flex items-center justify-center shrink-0">
                <i class="fas fa-bullhorn text-base animate-bounce" id="toastIcon"></i>
            </div>
            <div>
                <span class="font-bold text-sm block" id="toastTitle">Memanggil Antrian</span>
                <span class="text-xs text-indigo-200" id="toastMessage">Sedang menyiarkan panggilan...</span>
            </div>
        </div>
    </div>

    {{-- Card --}}
    <div class="card bg-base-100 shadow-md rounded-2 border">
        <div class="card-body p-0">

            <div class="overflow-x-auto">
                <table class="table table-zebra w-full text-sm">

                    {{-- Head --}}
                    <thead class="bg-slate-100 text-slate-500 text-xs uppercase tracking-wider">
                        <tr>
                            <th class="px-6 py-4">No</th>
                            <th class="px-6 py-4">Pasien</th>
                            <th class="px-6 py-4">Keluhan</th>
                            <th class="px-6 py-4 text-center">No Antrian</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>

                    {{-- Body --}}
                    <tbody id="antrianTableBody" class="text-slate-700">
                        @forelse ($daftarPasien as $dp)
                        <tr class="hover:bg-slate-50 transition" id="row-antrian-{{ $dp->id }}">

                            <td class="px-6 py-4 text-slate-500 row-number">
                                {{ $loop->iteration }}
                            </td>

                            <td class="px-6 py-4 font-semibold text-slate-800">
                                {{ $dp->pasien->nama }}
                            </td>

                            <td class="px-6 py-4 text-slate-500">
                                {{ $dp->keluhan ?: '-' }}
                            </td>

                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                <span class="px-3 py-1 bg-slate-100 rounded-full text-xs">
                                    #{{ $dp->no_antrian }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-right" id="action-container-{{ $dp->id }}">
                                @if ($dp->periksas->isNotEmpty())
                                    <span class="badge bg-green-100 text-green-700 border border-green-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                        <i class="fas fa-circle-check mr-1"></i>Sudah Diperiksa
                                    </span>
                                @else
                                    <div class="inline-flex items-center justify-end gap-2">
                                        <button type="button"
                                            onclick="panggilAntrian('{{ route('periksa-pasien.panggil', $dp->id) }}', '{{ $dp->no_antrian }}', '{{ $dp->pasien->nama }}')"
                                            class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-none rounded-lg px-3"
                                            title="Panggil Antrian #{{ $dp->no_antrian }} ke Ruang Periksa">
                                            <i class="fas fa-bullhorn text-xs"></i>
                                            Panggil
                                        </button>

                                        <a href="{{ route('periksa-pasien.create', $dp->id) }}"
                                            class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4"
                                            title="Catat Hasil Pemeriksaan">
                                            <i class="fas fa-stethoscope text-xs"></i>
                                            Periksa
                                        </a>
                                    </div>
                                @endif
                            </td>

                        </tr>
                        @empty
                        <tr id="emptyStateRow">
                            <td colspan="5" class="text-center py-14 text-slate-400">
                                <div class="flex flex-col items-center justify-center gap-2">
                                    <i class="fas fa-inbox text-3xl text-slate-300"></i>
                                    <span>Tidak ada data pasien periksa</span>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>

                </table>
            </div>

        </div>
    </div>

    <script>
        const currentDokterId = {{ Auth::id() }};

        function panggilAntrian(url, noAntrian, namaPasien) {
            const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') 
                || '{{ csrf_token() }}';

            fetch(url, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                }
            })
            .then(res => res.json())
            .then(data => {
                showToast(`Panggilan Terkirim: Antrian #${noAntrian}`, `${namaPasien} dipanggil ke ruang periksa.`);
            })
            .catch(err => {
                console.error('Error saat memanggil:', err);
                showToast(`Memanggil Antrian #${noAntrian}`, `${namaPasien}`);
            });
        }

        function showToast(title, message, iconClass = 'fa-bullhorn text-amber-300') {
            const toast = document.getElementById('panggilToast');
            const toastTitle = document.getElementById('toastTitle');
            const toastMessage = document.getElementById('toastMessage');
            const toastIcon = document.getElementById('toastIcon');

            if (!toast) return;

            toastTitle.innerText = title;
            toastMessage.innerText = message;
            toastIcon.className = `fas ${iconClass} text-base animate-bounce`;

            toast.classList.remove('hidden');
            setTimeout(() => {
                toast.classList.add('hidden');
            }, 4500);
        }

        // ================= ECHO REAL-TIME LISTENER FOR DOKTER =================
        document.addEventListener('DOMContentLoaded', function () {
            function initEchoDokter() {
                if (typeof window.Echo === 'undefined') {
                    setTimeout(initEchoDokter, 500);
                    return;
                }

                // Update badge state from pusher connection
                if (window.Echo.connector && window.Echo.connector.pusher) {
                    const pusherConn = window.Echo.connector.pusher.connection;

                    function updateBadgeState(state) {
                        const wsBadge = document.getElementById('wsStatusBadgeDokter');
                        if (!wsBadge) return;

                        if (state === 'connected') {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] animate-pulse text-emerald-500"></i><span>WebSocket Terhubung</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-50 text-emerald-700 border border-emerald-200 shadow-sm cursor-pointer';
                        } else if (state === 'connecting') {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] animate-pulse text-amber-500"></i><span>Menghubungkan...</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-50 text-amber-700 border border-amber-200 shadow-sm';
                        } else {
                            wsBadge.innerHTML = '<i class="fas fa-circle text-[8px] text-slate-400"></i><span>WebSocket Standby</span>';
                            wsBadge.className = 'inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-50 text-slate-600 border border-slate-200 shadow-sm';
                        }
                    }

                    updateBadgeState(pusherConn.state);
                    pusherConn.bind('state_change', function (states) {
                        updateBadgeState(states.current);
                    });
                }

                // Listen to antrian-poli channel
                window.Echo.channel('antrian-poli')
                    .listen('.AntrianUpdated', handleDokterEvent)
                    .listen('AntrianUpdatedEvent', handleDokterEvent);
            }

            function handleDokterEvent(e) {
                console.log('Dokter real-time event received:', e);

                // Pastikan event ini milik jadwal dokter yang sedang login
                if (Number(e.id_dokter) !== currentDokterId) {
                    return;
                }

                const tbody = document.getElementById('antrianTableBody');
                const emptyRow = document.getElementById('emptyStateRow');

                // Case 1: Pasien baru mendaftar
                if (e.status === 'daftar') {
                    showToast(
                        `Pasien Baru Mendaftar (#${e.no_antrian})`,
                        `${e.nama_pasien || 'Pasien'} mendaftar ke jadwal Anda.`,
                        'fa-user-plus text-emerald-300'
                    );

                    if (emptyRow) {
                        emptyRow.remove();
                    }

                    const existingRow = document.getElementById(`row-antrian-${e.id_daftar_poli}`);
                    if (!existingRow && tbody && e.id_daftar_poli) {
                        const rowCount = tbody.querySelectorAll('tr').length + 1;
                        const tr = document.createElement('tr');
                        tr.id = `row-antrian-${e.id_daftar_poli}`;
                        tr.className = 'hover:bg-slate-50 transition bg-emerald-50/70';

                        const panggilUrl = `/dokter/periksa-pasien/${e.id_daftar_poli}/panggil`;
                        const periksaUrl = `/dokter/periksa-pasien/${e.id_daftar_poli}`;

                        tr.innerHTML = `
                            <td class="px-6 py-4 text-slate-500 row-number">${rowCount}</td>
                            <td class="px-6 py-4 font-semibold text-slate-800">${e.nama_pasien || '-'}</td>
                            <td class="px-6 py-4 text-slate-500">${e.keluhan || '-'}</td>
                            <td class="px-6 py-4 text-center font-bold text-slate-700">
                                <span class="px-3 py-1 bg-slate-100 rounded-full text-xs">#${e.no_antrian}</span>
                            </td>
                            <td class="px-6 py-4 text-right" id="action-container-${e.id_daftar_poli}">
                                <div class="inline-flex items-center justify-end gap-2">
                                    <button type="button"
                                        onclick="panggilAntrian('${panggilUrl}', '${e.no_antrian}', '${e.nama_pasien}')"
                                        class="btn btn-sm bg-indigo-600 hover:bg-indigo-700 text-white border-none rounded-lg px-3"
                                        title="Panggil Antrian #${e.no_antrian}">
                                        <i class="fas fa-bullhorn text-xs"></i> Panggil
                                    </button>
                                    <a href="${periksaUrl}"
                                        class="btn btn-sm bg-amber-500 hover:bg-amber-600 text-white border-none rounded-lg px-4"
                                        title="Catat Hasil Pemeriksaan">
                                        <i class="fas fa-stethoscope text-xs"></i> Periksa
                                    </a>
                                </div>
                            </td>
                        `;

                        tbody.appendChild(tr);

                        setTimeout(() => {
                            tr.classList.remove('bg-emerald-50/70');
                        }, 4000);
                    }
                }

                // Case 2: Antrian dipanggil
                if (e.status === 'dipanggil' && e.id_daftar_poli) {
                    const row = document.getElementById(`row-antrian-${e.id_daftar_poli}`);
                    if (row) {
                        row.classList.add('bg-indigo-50/80');
                        setTimeout(() => {
                            row.classList.remove('bg-indigo-50/80');
                        }, 3000);
                    }
                }

                // Case 3: Antrian selesai diperiksa
                if (e.status === 'selesai' && e.id_daftar_poli) {
                    const actionContainer = document.getElementById(`action-container-${e.id_daftar_poli}`);
                    if (actionContainer) {
                        actionContainer.innerHTML = `
                            <span class="badge bg-green-100 text-green-700 border border-green-200 rounded-lg px-3 py-1 text-xs font-semibold">
                                <i class="fas fa-circle-check mr-1"></i>Sudah Diperiksa
                            </span>
                        `;
                    }
                }
            }

            initEchoDokter();
        });

        setTimeout(() => {
            const alert = document.querySelector('.alert');
            if (alert) {
                alert.classList.remove('show');
                alert.classList.add('fade');
                setTimeout(() => alert.remove(), 200);
            }
        }, 3000);
    </script>

</x-layouts.app>
