<header class="bg-base-100 border-b border-base-300 h-16 flex items-center px-6 gap-4 sticky top-0 z-30 shadow-sm">

    {{-- Mobile Hamburger --}}
    <button onclick="toggleSidebar()" class="btn btn-square btn-ghost lg:hidden">
        <i class="fas fa-bars w-5 h-5"></i>
    </button>

    {{-- Breadcrumb --}}
    <div class="flex-1">
        <div class="flex items-center gap-2 text-sm">
            <span class="text-base-content/50">Poliklinik</span>
            <i class="fas fa-chevron-right text-xs text-base-content/30"></i>
            <span class="font-semibold text-base-content">
                {{ $title ?? 'Dashboard' }}
            </span>
        </div>
    </div>

    {{-- Fullscreen --}}
    <button onclick="toggleFullscreen()" class="btn btn-square btn-ghost" title="Fullscreen">
        <i id="fsIcon" class="fas fa-expand w-5 h-5"></i>
    </button>

    {{-- Notification Dropdown --}}
    @php
        $unreadCount = auth()->user()->unreadNotifications->count();
        $recentNotifications = auth()->user()->notifications()->take(6)->get();
    @endphp
    <div class="dropdown dropdown-end">
        <button tabindex="0" class="btn btn-circle btn-ghost" title="Notifikasi">
            <div class="relative flex items-center justify-center">
                <i class="fas fa-bell text-lg text-slate-600"></i>
                @if($unreadCount > 0)
                <span class="absolute -top-1.5 -right-2.5 flex h-4 min-w-[18px] px-1 items-center justify-center rounded-full bg-rose-500 text-[10px] font-bold text-white shadow-sm ring-2 ring-white animate-pulse">
                    {{ $unreadCount > 99 ? '99+' : $unreadCount }}
                </span>
                @endif
            </div>
        </button>

        <div tabindex="0" class="dropdown-content z-50 card card-compact w-80 sm:w-96 p-0 shadow-xl bg-base-100 rounded-2xl border border-slate-200 mt-2">
            {{-- Header --}}
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between bg-slate-50 rounded-t-2xl">
                <div class="flex items-center gap-2">
                    <span class="font-bold text-slate-800 text-sm">Notifikasi</span>
                    @if($unreadCount > 0)
                    <span class="badge badge-primary badge-sm text-white font-semibold">{{ $unreadCount }} Baru</span>
                    @endif
                </div>

                @if($unreadCount > 0)
                <form action="{{ route('notifications.markAllAsRead') }}" method="POST">
                    @csrf
                    <button type="submit" class="text-xs text-primary hover:underline font-semibold flex items-center gap-1">
                        <i class="fas fa-check-double text-[10px]"></i> Tandai Semua Dibaca
                    </button>
                </form>
                @endif
            </div>

            {{-- List --}}
            <div class="max-h-80 overflow-y-auto divide-y divide-slate-100">
                @forelse($recentNotifications as $notif)
                <form action="{{ route('notifications.markAsRead', $notif->id) }}" method="POST" class="w-full">
                    @csrf
                    <button type="submit" class="w-full text-left p-3.5 transition flex items-start gap-3 hover:bg-slate-50
                        {{ $notif->read_at === null ? 'bg-indigo-50/50 border-l-4 border-primary' : 'opacity-75' }}">
                        <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5
                            {{ $notif->read_at === null ? 'bg-primary/10 text-primary' : 'bg-slate-100 text-slate-500' }}">
                            <i class="{{ $notif->data['icon'] ?? 'fas fa-bell' }} text-xs"></i>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="text-xs font-bold text-slate-800 truncate">
                                {{ $notif->data['title'] ?? 'Pemberitahuan' }}
                            </p>
                            <p class="text-xs text-slate-600 line-clamp-2 mt-0.5 leading-relaxed">
                                {{ $notif->data['message'] ?? '-' }}
                            </p>
                            <span class="text-[10px] text-slate-400 mt-1 block font-medium">
                                <i class="fas fa-clock text-[9px] mr-0.5"></i>
                                {{ $notif->created_at->diffForHumans() }}
                            </span>
                        </div>
                        @if($notif->read_at === null)
                        <span class="w-2 h-2 rounded-full bg-primary shrink-0 mt-1.5" title="Belum dibaca"></span>
                        @endif
                    </button>
                </form>
                @empty
                <div class="py-10 text-center text-slate-400">
                    <i class="fas fa-bell-slash text-3xl mb-2 text-slate-300 block"></i>
                    <p class="text-xs font-medium text-slate-600">Belum ada notifikasi</p>
                    <p class="text-[11px] text-slate-400">Pemberitahuan aktivitas akan muncul di sini.</p>
                </div>
                @endforelse
            </div>

            {{-- Footer --}}
            @if($recentNotifications->count() > 0)
            <div class="p-2.5 border-t border-slate-100 bg-slate-50 text-center rounded-b-2xl">
                <a href="{{ route('notifications.index') }}" class="text-xs font-semibold text-primary hover:underline">
                    Lihat Semua Notifikasi
                </a>
            </div>
            @endif
        </div>
    </div>

    {{-- User Info / Profile Link --}}
    <a href="{{ route('profile.password') }}" class="flex items-center gap-3 hover:opacity-85 transition group" title="Buka Profil / Ganti Password">
        <div class="text-right hidden sm:block">
            <div class="text-sm font-semibold leading-tight group-hover:text-primary transition">
                {{ auth()->user()->nama ?? auth()->user()->name ?? 'Pengguna' }}
            </div>
            <div class="text-xs text-base-content/50 leading-tight uppercase font-medium">
                {{ auth()->user()->role ?? 'Admin Sistem' }}
            </div>
        </div>

        <div class="avatar">
            <div class="w-10 h-10 rounded-full bg-primary text-primary-content flex items-center justify-center font-bold shadow-sm">
                <span class="text-sm font-semibold leading-none">
                    {{ strtoupper(substr(auth()->user()->nama ?? auth()->user()->name ?? 'U', 0, 1)) }}
                </span>
            </div>
        </div>
    </a>

</header>

<script>
    function toggleFullscreen() {
        const icon = document.getElementById('fsIcon');

        if (!document.fullscreenElement) {
            document.documentElement.requestFullscreen();
            icon.classList.remove('fa-expand');
            icon.classList.add('fa-compress');
        } else {
            document.exitFullscreen();
            icon.classList.remove('fa-compress');
            icon.classList.add('fa-expand');
        }
    }
</script>
