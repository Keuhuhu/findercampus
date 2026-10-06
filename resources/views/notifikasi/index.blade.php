<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Pusat Notifikasi' => '#']" />
    </x-slot>

    <div class="max-w-4xl mx-auto">
        <div class="flex items-center justify-between mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Pusat Notifikasi</h1>
                <p class="text-sm text-gray-500 mt-1">Pemberitahuan aktivitas, laporan, dan klaim Anda.</p>
            </div>
            @if(Auth::user()->unreadNotifications->count() > 0)
                <form action="/notifikasi/read-all" method="POST">
                    @csrf
                    <button type="submit" class="px-4 py-2 bg-white border border-gray-200 text-sm font-semibold text-gray-700 rounded-lg hover:bg-gray-50 transition shadow-sm">
                        Tandai Semua Dibaca
                    </button>
                </form>
            @endif
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="divide-y divide-gray-100">
                @forelse($notifikasis as $notif)
                    <div class="p-5 {{ $notif->read_at ? 'bg-white' : 'bg-blue-50/50' }} hover:bg-gray-50 transition flex items-start gap-4">
                        <div class="w-10 h-10 rounded-full flex items-center justify-center shrink-0 {{ $notif->read_at ? 'bg-gray-100 text-gray-500' : 'bg-primary text-white shadow-sm ring-4 ring-blue-100' }}">
                            @if(isset($notif->data['type']) && $notif->data['type'] === 'klaim_diajukan')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                            @elseif(isset($notif->data['type']) && $notif->data['type'] === 'klaim_status')
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                            @endif
                        </div>

                        <div class="flex-1">
                            <h4 class="text-sm font-bold {{ $notif->read_at ? 'text-gray-900' : 'text-primary' }} mb-1">
                                {{ $notif->data['title'] ?? 'Pemberitahuan Baru' }}
                            </h4>
                            <p class="text-sm text-gray-600 mb-2">{{ $notif->data['message'] ?? '' }}</p>
                            <p class="text-xs font-medium text-gray-400">{{ $notif->created_at->diffForHumans() }}</p>
                        </div>

                        <div>
                            @if(isset($notif->data['url']))
                                <form action="/notifikasi/{{ $notif->id }}/read" method="POST">
                                    @csrf
                                    <button type="submit" class="text-xs font-bold text-accent hover:underline">
                                        Lihat Detail →
                                    </button>
                                </form>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="p-12 text-center text-gray-500">
                        <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                        <p class="font-medium text-sm">Tidak ada notifikasi saat ini.</p>
                    </div>
                @endforelse
            </div>
            
            @if($notifikasis->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $notifikasis->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
