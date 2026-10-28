<x-layouts.app title="Ganti Password">

    {{-- Header --}}
    <div class="flex items-center gap-3 mb-6">
        @php
            $dashboardRoute = match(auth()->user()->role) {
                'admin' => route('admin.dashboard'),
                'dokter' => route('dokter.dashboard'),
                default => route('pasien.dashboard'),
            };
        @endphp
        <a href="{{ $dashboardRoute }}"
            class="inline-flex items-center justify-center w-9 h-9 rounded-lg bg-slate-100 text-slate-500 hover:bg-slate-200 transition">
            <i class="fas fa-arrow-left text-sm"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Ganti Password
            </h2>
            <p class="text-xs text-slate-500">
                Perbarui kata sandi akun Anda untuk menjaga keamanan akses sistem.
            </p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- Card Info Akun --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 h-fit">
            <div class="card-body p-6 text-center">
                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center mx-auto mb-3 text-2xl font-bold">
                    {{ strtoupper(substr($user->nama ?? 'U', 0, 1)) }}
                </div>
                <h3 class="font-bold text-slate-800 text-lg">
                    {{ $user->nama }}
                </h3>
                <p class="text-xs text-slate-500 mt-0.5">
                    {{ $user->email }}
                </p>

                <div class="mt-4 pt-4 border-t border-slate-100 flex justify-center">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold uppercase tracking-wider
                        {{ $user->role === 'admin' ? 'bg-indigo-50 text-indigo-700 border border-indigo-200' : '' }}
                        {{ $user->role === 'dokter' ? 'bg-purple-50 text-purple-700 border border-purple-200' : '' }}
                        {{ $user->role === 'pasien' ? 'bg-amber-50 text-amber-700 border border-amber-200' : '' }}">
                        <i class="fas fa-shield-halved text-[10px]"></i>
                        Role: {{ ucfirst($user->role) }}
                    </span>
                </div>
            </div>
        </div>

        {{-- Form Ganti Password --}}
        <div class="card bg-base-100 shadow-sm rounded-2xl border border-slate-200 lg:col-span-2">
            <div class="card-body p-6 sm:p-8">

                <form action="{{ route('profile.password.update') }}" method="POST">
                    @csrf
                    @method('PUT')

                    {{-- Password Lama --}}
                    <div class="mb-5">
                        <label for="current_password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Password Lama <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="current_password" id="current_password"
                                placeholder="Masukkan password saat ini..."
                                class="w-full px-4 py-2.5 rounded-lg border-2 text-sm transition focus:outline-none focus:border-primary
                                {{ $errors->has('current_password') ? 'border-red-500 bg-red-50/20' : 'border-slate-300' }}"
                                required>
                        </div>
                        @error('current_password')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation text-[11px]"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Password Baru --}}
                    <div class="mb-5">
                        <label for="password" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password" id="password"
                                placeholder="Minimal 8 karakter..."
                                class="w-full px-4 py-2.5 rounded-lg border-2 text-sm transition focus:outline-none focus:border-primary
                                {{ $errors->has('password') ? 'border-red-500 bg-red-50/20' : 'border-slate-300' }}"
                                required>
                        </div>
                        <p class="text-xs text-slate-400 mt-1">
                            Minimal 8 karakter dan tidak boleh sama dengan password lama.
                        </p>
                        @error('password')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation text-[11px]"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Konfirmasi Password Baru --}}
                    <div class="mb-8">
                        <label for="password_confirmation" class="block text-sm font-semibold text-slate-700 mb-1.5">
                            Konfirmasi Password Baru <span class="text-red-500">*</span>
                        </label>
                        <div class="relative">
                            <input type="password" name="password_confirmation" id="password_confirmation"
                                placeholder="Ulangi password baru..."
                                class="w-full px-4 py-2.5 rounded-lg border-2 text-sm transition focus:outline-none focus:border-primary
                                {{ $errors->has('password_confirmation') ? 'border-red-500 bg-red-50/20' : 'border-slate-300' }}"
                                required>
                        </div>
                        @error('password_confirmation')
                        <p class="text-xs text-red-500 mt-1.5 flex items-center gap-1">
                            <i class="fas fa-circle-exclamation text-[11px]"></i>
                            {{ $message }}
                        </p>
                        @enderror
                    </div>

                    {{-- Tombol Aksi --}}
                    <div class="flex items-center justify-end gap-3 pt-4 border-t border-slate-100">
                        <a href="{{ $dashboardRoute }}"
                            class="px-5 py-2.5 bg-slate-100 hover:bg-slate-200 text-slate-600 rounded-lg text-sm font-semibold transition">
                            Batal
                        </a>

                        <button type="submit"
                            class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary hover:bg-primary/90 text-white rounded-lg text-sm font-semibold transition shadow-sm">
                            <i class="fas fa-key text-xs"></i>
                            Perbarui Password
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>

</x-layouts.app>
