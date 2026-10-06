<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Riwayat Laporan' => '/status', 'Detail Barang' => '#']" />
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-800 rounded-xl text-sm font-medium">
            {{ session('error') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left Column: Details -->
        <div class="lg:col-span-2 space-y-6">
            
            <!-- Main Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Gallery (Simple for now, just shows first/primary image large, and others small) -->
                @if($laporan->fotos->count() > 0)
                    <div class="w-full h-72 sm:h-96 bg-gray-100 relative group">
                        @php $primary = $laporan->fotoUtama() ?? $laporan->fotos->first(); @endphp
                        <img src="{{ Storage::url($primary->file_path) }}" class="w-full h-full object-cover">
                        
                        <div class="absolute top-4 left-4">
                            <x-status-badge :status="$laporan->status" />
                        </div>
                        <div class="absolute top-4 right-4 bg-gray-900/80 backdrop-blur text-white px-3 py-1 rounded-lg text-sm font-semibold border border-gray-700">
                            {{ $laporan->kode_laporan }}
                        </div>
                    </div>
                    @if($laporan->fotos->count() > 1)
                        <div class="flex gap-2 p-4 bg-gray-50 border-b border-gray-200 overflow-x-auto">
                            @foreach($laporan->fotos as $foto)
                                <div class="w-20 h-20 rounded-lg overflow-hidden border-2 {{ $foto->is_primary ? 'border-primary' : 'border-transparent' }} shrink-0">
                                    <img src="{{ Storage::url($foto->file_path) }}" class="w-full h-full object-cover">
                                </div>
                            @endforeach
                        </div>
                    @endif
                @else
                    <div class="w-full h-48 bg-gray-100 flex flex-col items-center justify-center border-b border-gray-200">
                        <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        <span class="text-sm text-gray-400">Tidak ada foto</span>
                    </div>
                @endif

                <div class="p-6 sm:p-8">
                    <div class="flex flex-col sm:flex-row sm:items-start justify-between gap-4 mb-6">
                        <div>
                            <div class="flex items-center gap-2 mb-2">
                                <span class="px-2 py-1 rounded bg-gray-100 text-gray-600 text-xs font-bold">{{ $laporan->kategori->nama }}</span>
                                <span class="px-2 py-1 rounded {{ $laporan->tipe === 'hilang' ? 'bg-red-50 text-red-700' : 'bg-emerald-50 text-emerald-700' }} text-xs font-bold uppercase tracking-wider">
                                    {{ $laporan->tipe === 'hilang' ? '🔴 Hilang' : '🟢 Ditemukan' }}
                                </span>
                            </div>
                            <h1 class="text-2xl font-bold text-gray-900">{{ $laporan->nama_barang }}</h1>
                        </div>
                        
                        @if(Auth::id() !== $laporan->user_id && $laporan->status === 'aktif')
                            <a href="/klaim/buat/{{ $laporan->id }}" class="inline-flex items-center justify-center px-6 py-3 bg-gray-900 text-white font-bold rounded-xl hover:bg-black transition shadow-md">
                                {{ $laporan->tipe === 'ditemukan' ? 'Ini Milik Saya 🖐️' : 'Saya Menemukan Ini 💡' }}
                            </a>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-y-6 gap-x-8">
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Warna</h4>
                            <p class="text-sm font-semibold text-gray-900">{{ $laporan->warna }}</p>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Lokasi</h4>
                            <p class="text-sm font-semibold text-gray-900">{{ $laporan->lokasi->nama }}</p>
                            @if($laporan->detail_lokasi)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $laporan->detail_lokasi }}</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Tanggal Kejadian</h4>
                            <p class="text-sm font-semibold text-gray-900">{{ $laporan->tanggal_kejadian->translatedFormat('d F Y') }}</p>
                            @if($laporan->waktu_kejadian)
                                <p class="text-xs text-gray-500 mt-0.5">{{ date('H:i', strtotime($laporan->waktu_kejadian)) }} WIB</p>
                            @endif
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-1">Dilaporkan Oleh</h4>
                            <div class="flex items-center gap-2 mt-1">
                                <div class="w-6 h-6 rounded-full bg-primary text-white flex items-center justify-center text-[10px] font-bold">
                                    {{ substr($laporan->user->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-semibold text-gray-900">{{ $laporan->user->name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-8 pt-6 border-t border-gray-100">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Ciri-ciri Khusus</h4>
                        <p class="text-sm text-gray-600 bg-gray-50 p-4 rounded-lg border border-gray-100">{{ $laporan->ciri_khusus ?: 'Tidak ada ciri khusus yang dicantumkan.' }}</p>
                    </div>

                    @if($laporan->deskripsi)
                    <div class="mt-6">
                        <h4 class="text-sm font-bold text-gray-900 mb-2">Deskripsi / Kronologi</h4>
                        <p class="text-sm text-gray-600 leading-relaxed">{{ $laporan->deskripsi }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Action Buttons for Owner -->
            @if(Auth::id() === $laporan->user_id && $laporan->status === 'aktif')
                <div class="flex flex-col sm:flex-row gap-3">
                    <a href="/laporan/{{ $laporan->id }}/edit" class="flex-1 text-center px-4 py-3 bg-white border border-gray-200 text-gray-700 text-sm font-bold rounded-xl hover:bg-gray-50 transition shadow-sm">
                        ✏️ Edit Laporan
                    </a>
                    
                    <form action="/laporan/{{ $laporan->id }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin membatalkan laporan ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full px-4 py-3 bg-white border border-red-200 text-red-600 text-sm font-bold rounded-xl hover:bg-red-50 transition shadow-sm">
                            🗑️ Batalkan Laporan
                        </button>
                    </form>
                </div>
            @endif
        </div>

        <!-- Right Column: Timeline & QR -->
        <div class="lg:col-span-1 space-y-6">
            
            <!-- QR Code (If Ditemukan) -->
            @if($laporan->tipe === 'ditemukan' && $laporan->qr_code_path)
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <h3 class="text-base font-bold text-gray-900 mb-1">Label Barang Temuan</h3>
                <p class="text-xs text-gray-500 mb-4">Tempelkan QR Code ini pada barang saat diserahkan ke posko.</p>
                
                <div class="inline-block p-2 border-2 border-gray-100 rounded-xl mb-4">
                    <img src="{{ Storage::url($laporan->qr_code_path) }}" alt="QR Code" class="w-40 h-40">
                </div>
                
                <a href="/laporan/{{ $laporan->id }}/qr" class="w-full inline-flex items-center justify-center gap-2 px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-light transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                    Download QR Code
                </a>
            </div>
            @endif

            <!-- Contact Reporter -->
            @if(Auth::id() !== $laporan->user_id && $laporan->user->whatsapp_visible && $laporan->user->whatsapp)
            <div class="bg-emerald-50 rounded-xl shadow-sm border border-emerald-200 p-6 text-center">
                <div class="w-12 h-12 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-6 h-6 text-emerald-600" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51a12.8 12.8 0 00-.57-.01c-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                </div>
                <h3 class="text-sm font-bold text-gray-900 mb-1">Hubungi Pelapor</h3>
                <p class="text-xs text-gray-600 mb-4">Pengguna ini mengizinkan kontak via WhatsApp.</p>
                <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $laporan->user->whatsapp) }}" target="_blank" class="w-full inline-flex items-center justify-center px-4 py-2 bg-emerald-500 text-white text-sm font-bold rounded-lg hover:bg-emerald-600 transition shadow-sm">
                    Chat WhatsApp
                </a>
            </div>
            @endif

            <!-- Activity Logs -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-base font-bold text-gray-900 mb-4">Log Aktivitas</h3>
                
                <div class="relative pl-4 space-y-6 border-l-2 border-gray-100">
                    @forelse($laporan->activityLogs()->orderBy('created_at', 'desc')->get() as $log)
                        <div class="relative">
                            <div class="absolute -left-[21px] top-1 w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-white"></div>
                            <p class="text-xs font-bold text-gray-900">{{ ucfirst($log->aksi) }}</p>
                            <p class="text-xs text-gray-500 mt-0.5">{{ $log->deskripsi }}</p>
                            <p class="text-[10px] text-gray-400 mt-1 font-medium">{{ $log->created_at->diffForHumans() }}</p>
                        </div>
                    @empty
                        <p class="text-xs text-gray-500">Belum ada aktivitas.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
