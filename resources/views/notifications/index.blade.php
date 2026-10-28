<x-layouts.app title="Semua Notifikasi">

    {{-- Header --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-slate-800">
                Pemberitahuan & Notifikasi
            </h2>
            <p class="text-sm text-slate-500 mt-1">
                Riwayat seluruh aktivitas dan notifikasi sistem yang dikirimkan ke akun Anda.
            </p>
        </div>

        @if(auth()->user()->unreadNotifications->count() > 0)
        <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
            @csrf
            <button type="submit" class="btn btn-sm bg-primary hover:bg-primary/90 text-white rounded-xl shadow-sm">
                <i class="fas fa-check-double text-xs mr-1"></i> Tandai Semua Sudah Dibaca
            </button>
        </form>
        @endif
    </div>

    {{-- Card List --}}
    <div class="card bg-base-100 shadow-md rounded-2xl border border-slate-200 overflow-hidden">
        <div class="card-body p-0">

            <div class="divide-y divide-slate-100">
                @forelse($notifications as $notif)
                <div class="p-5 flex flex-col sm:flex-row sm:items-center justify-between gap-4 transition hover:bg-slate-50/70
                    {{ $notif->read_at === null ? 'bg-indigo-50/30 border-l-4 border-primary' : 'opacity-75' }}">

                    <div class="flex items-start gap-4">
                        <div class="w-10 h-10 rounded-xl flex items-center justify-center shrink-0 mt-0.5
                            {{ $notif->read_at === null ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500' }}">
                            <i class="{{ $notif->data['icon'] ?? 'fas fa-bell' }} text-base"></i>
                        </div>

                        <div>
                            <div class="flex items-center gap-2">
                                <h4 class="font-bold text-slate-800 text-sm">
                                    {{ $notif->data['title'] ?? 'Pemberitahuan' }}
                                </h4>
                                @if($notif->read_at === null)
                                <span class="badge badge-primary badge-xs text-white text-[10px] font-bold">Baru</span>
                                @endif
                            </div>

                            <p class="text-xs text-slate-600 mt-1 leading-relaxed max-w-2xl">
                                {{ $notif->data['message'] ?? '-' }}
                            </p>

                            <span class="text-[11px] text-slate-400 mt-2 block flex items-center gap-1 font-medium">
                                <i class="fas fa-clock text-[10px]"></i>
                                {{ $notif->created_at->diffForHumans() }} ({{ $notif->created_at->format('d/m/Y H:i') }})
                            </span>
                        </div>
                    </div>

                    <div class="flex items-center gap-2 self-end sm:self-center shrink-0">
                        @if($notif->read_at === null)
                        <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST">
                            @csrf
                            <button type="submit" class="btn btn-sm btn-ghost bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-lg text-xs">
                                <i class="fas fa-check text-xs"></i> Tandai Dibaca
                            </button>
                        </form>
                        @endif

                        @if(isset($notif->data['url']))
                        <a href="{{ $notif->data['url'] }}" class="btn btn-sm bg-primary hover:bg-primary/90 text-white rounded-lg text-xs shadow-sm">
                            <i class="fas fa-arrow-up-right-from-square text-xs"></i> Buka
                        </a>
                        @endif
                    </div>

                </div>
                @empty
                <div class="py-16 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-4xl mb-3 text-slate-300 block"></i>
                    <p class="font-semibold text-slate-600 text-base">Tidak Ada Notifikasi</p>
                    <p class="text-xs text-slate-400 mt-0.5">Semua riwayat notifikasi Anda akan muncul di halaman ini.</p>
                </div>
                @endforelse
            </div>

            {{-- Pagination Links --}}
            @if($notifications->hasPages())
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $notifications->links() }}
            </div>
            @endif

        </div>
    </div>

</x-layouts.app>
