<x-layouts.app title="Daftar Poli">

    <div class="flex items-center justify-center px-4">
        <div class="w-full max-w-3xl">
            <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200">
                <div class="card-body p-8">

                    <h2 class="text-2xl font-bold text-center mb-6 text-slate-800 flex items-center justify-center gap-2">
                        <i class="fas fa-hospital text-primary"></i>
                        Pendaftaran Poli
                    </h2>

                    {{-- Toast Success --}}
                    @if (session('message'))
                    <div id="toastSuccess" class="toast toast-top toast-end z-50">
                        <div class="alert alert-success shadow-lg rounded-xl">
                            <i class="fas fa-check-circle"></i>
                            <span>{{ session('message') }}</span>
                        </div>
                    </div>
                    @endif

                    {{-- Error --}}
                    @if ($errors->any())
                    <div class="alert alert-error mb-4 rounded-xl">
                        <i class="fas fa-circle-exclamation"></i>
                        <ul class="list-disc pl-5 text-sm">
                            @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    {{-- Alert Jika Pasien Masih Memiliki Antrian Aktif --}}
                    @if (isset($antrianAktif) && $antrianAktif)
                    <div class="alert alert-warning mb-6 rounded-xl shadow-sm text-sm">
                        <i class="fas fa-triangle-exclamation text-lg"></i>
                        <div>
                            <div class="font-bold">Perhatian: Anda Masih Memiliki Antrian Aktif</div>
                            <div class="text-xs mt-0.5">
                                Anda sudah terdaftar di <strong>{{ $antrianAktif->jadwalPeriksa->dokter->poli->nama_poli ?? 'Poliklinik' }}</strong>
                                (Dr. {{ $antrianAktif->jadwalPeriksa->dokter->nama ?? '-' }}) dengan <strong>Nomor Antrian #{{ $antrianAktif->no_antrian }}</strong>.
                                Sesuai ketentuan, Anda baru dapat mendaftar kembali setelah pemeriksaan sebelumnya selesai.
                            </div>
                            <div class="mt-2">
                                <a href="{{ route('pasien.dashboard') }}" class="btn btn-xs bg-slate-800 text-white border-none rounded-lg">
                                    <i class="fas fa-arrow-left text-[10px]"></i> Lihat Antrian di Dashboard
                                </a>
                            </div>
                        </div>
                    </div>
                    @endif

                    <form action="{{ route('pasien.daftar.submit') }}" method="POST">
                        @csrf
                        <input type="hidden" name="id_pasien" value="{{ $user->id }}">

                        {{-- Nomor RM --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1 text-sm text-slate-700">
                                Nomor Rekam Medis
                            </label>

                            <input type="text" value="{{ $user->no_rm }}"
                                class="w-full border-2 rounded-lg p-2.5 bg-gray-100 text-slate-600 text-sm" disabled>
                        </div>

                        {{-- Pilih Poli --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1 text-sm text-slate-700">
                                Pilih Poli <span class="text-red-500">*</span>
                            </label>

                            <select name="id_poli" id="poliSelect" class="w-full border-2 rounded-lg p-2.5 text-sm focus:outline-none focus:border-primary"
                                {{ isset($antrianAktif) && $antrianAktif ? 'disabled' : '' }} required>
                                <option value="">-- Pilih Poli --</option>
                                @foreach ($polis as $poli)
                                <option value="{{ $poli->id }}">
                                    {{ $poli->nama_poli }}
                                </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- Pilih Jadwal --}}
                        <div class="mb-4">
                            <label class="font-semibold block mb-1 text-sm text-slate-700">
                                Pilih Jadwal Periksa <span class="text-red-500">*</span>
                            </label>

                            <select name="id_jadwal" id="jadwalSelect" class="w-full border-2 rounded-lg p-2.5 text-sm focus:outline-none focus:border-primary"
                                {{ isset($antrianAktif) && $antrianAktif ? 'disabled' : '' }} required>
                                <option value="">-- Pilih Jadwal --</option>

                                @foreach ($jadwals as $jadwal)
                                <option value="{{ $jadwal->id }}" data-poli="{{ $jadwal->dokter->id_poli }}">
                                    {{ $jadwal->hari }}
                                    ({{ substr($jadwal->jam_mulai, 0, 5) }} - {{ substr($jadwal->jam_selesai, 0, 5) }})
                                    — Dr. {{ $jadwal->dokter->nama ?? '--' }}
                                </option>
                                @endforeach

                            </select>
                        </div>

                        {{-- Keluhan --}}
                        <div class="mb-6">
                            <label class="font-semibold block mb-1 text-sm text-slate-700">
                                Keluhan <span class="text-red-500">*</span>
                            </label>
                            <textarea name="keluhan" rows="3" required
                                class="w-full border-2 rounded-lg p-2.5 text-sm focus:outline-none focus:border-primary @error('keluhan') border-red-500 @enderror"
                                placeholder="Tuliskan keluhan atau gejala yang Anda rasakan..."
                                {{ isset($antrianAktif) && $antrianAktif ? 'disabled' : '' }}>{{ old('keluhan') }}</textarea>
                            @error('keluhan')
                            <p class="text-red-500 text-xs mt-1 flex items-center gap-1">
                                <i class="fas fa-circle-exclamation text-[10px]"></i>
                                <span>{{ $message }}</span>
                            </p>
                            @enderror
                        </div>

                        <div class="flex justify-end gap-3">
                            <a href="{{ route('pasien.dashboard') }}" class="px-6 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 font-semibold rounded-lg text-sm transition">
                                Batal
                            </a>

                            <button type="submit"
                                @if(isset($antrianAktif) && $antrianAktif)
                                    disabled class="px-8 py-2.5 bg-slate-300 text-slate-500 font-semibold rounded-lg cursor-not-allowed text-sm"
                                @else
                                    class="px-8 py-2.5 bg-primary hover:bg-primary/90 text-white font-semibold rounded-lg transition text-sm shadow-sm"
                                @endif>
                                <i class="fas fa-paper-plane mr-1 text-xs"></i>
                                Daftar Poli
                            </button>
                        </div>
                    </form>

                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener("DOMContentLoaded", function(){
            const poliSelect = document.getElementById("poliSelect")
            const jadwalSelect = document.getElementById("jadwalSelect")
            const jadwalOptions = jadwalSelect.querySelectorAll("option")

            poliSelect.addEventListener("change", function(){
                let poliId = this.value
                jadwalOptions.forEach(option => {
                    if(option.value === ""){
                        option.style.display = "block"
                        return
                    }
                    if(option.dataset.poli === poliId){
                        option.style.display = "block"
                    }else{
                        option.style.display = "none"
                    }
                })
                jadwalSelect.value = ""
            })
        })
    </script>
    @endpush
</x-layouts.app>
