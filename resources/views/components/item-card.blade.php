@props(['laporan'])

<div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden card-lift group flex flex-col h-full">
    <!-- Image Header -->
    <div class="relative h-48 w-full bg-gray-100 overflow-hidden">
        @if($laporan->fotoUtama())
            <img src="{{ Storage::url($laporan->fotoUtama()->file_path) }}" alt="{{ $laporan->nama_barang }}" class="w-full h-full object-cover group-hover:scale-105 transition duration-500 ease-out">
        @else
            <div class="w-full h-full flex items-center justify-center text-gray-300 bg-gradient-to-br from-gray-50 to-gray-100">
                <svg class="w-14 h-14" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
            </div>
        @endif

        <!-- Badge Status -->
        <div class="absolute top-3 left-3">
            @if($laporan->tipe === 'ditemukan')
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-blue-600 text-white shadow-md tracking-wide">
                    DITEMUKAN
                </span>
            @else
                <span class="inline-flex items-center px-2.5 py-1 rounded-md text-xs font-bold bg-red-600 text-white shadow-md tracking-wide">
                    HILANG
                </span>
            @endif
        </div>
    </div>

    <!-- Content -->
    <div class="p-4 flex flex-col flex-grow">
        <h3 class="text-base font-bold text-gray-900 line-clamp-1 mb-1">{{ $laporan->nama_barang }}</h3>

        <div class="flex items-start gap-1.5 text-xs text-gray-500 mb-1">
            <svg class="w-4 h-4 text-gray-400 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="line-clamp-1">{{ $laporan->lokasi->nama ?? 'Lokasi tidak diketahui' }}</span>
        </div>

        <div class="flex items-center gap-1.5 text-xs text-gray-500 mb-4">
            <svg class="w-4 h-4 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span>{{ \Carbon\Carbon::parse($laporan->tanggal_kejadian)->translatedFormat('d M Y') }}, {{ \Carbon\Carbon::parse($laporan->waktu_kejadian)->format('H:i') }} WIB</span>
        </div>

        <div class="mt-auto pt-3 border-t border-gray-100 flex gap-2">
            @if($laporan->tipe === 'ditemukan')
                <a href="{{ route('klaim.create', $laporan->id) }}" class="btn-ripple w-full text-center py-2 px-4 bg-indigo-50 text-indigo-700 hover:bg-indigo-100 font-semibold text-sm rounded-lg transition-all duration-200">
                    Ini Milik Saya 🖐️
                </a>
            @else
                <a href="{{ route('klaim.create', $laporan->id) }}" class="btn-ripple w-full text-center py-2 px-4 bg-gray-50 text-gray-700 hover:bg-gray-100 border border-gray-200 font-semibold text-sm rounded-lg transition-all duration-200">
                    Lihat Detail
                </a>
            @endif
        </div>
    </div>
</div>

