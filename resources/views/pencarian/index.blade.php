<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Pencarian SMART & Pencocokan
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-white overflow-visible shadow-sm sm:rounded-lg p-6 mb-6">
                <h3 class="text-lg font-bold mb-4">Mulai Pencocokan</h3>
                <form method="GET" action="{{ route('pencarian.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-4 items-end">
                    
                    <div class="md:col-span-2 relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Pilih Laporan Kehilangan Anda</label>
                        @php
                            $optLaporan = [];
                            foreach($laporanSaya as $lap) {
                                $optLaporan[$lap->id] = $lap->kode_laporan . ' - ' . $lap->nama_barang . ' (' . $lap->tanggal_kejadian->format('d M Y') . ')';
                            }
                        @endphp
                        <x-custom-select 
                            name="laporan_id" 
                            :options="$optLaporan" 
                            placeholder="-- Pilih Barang yang Hilang --" 
                            selected="{{ request('laporan_id') }}"
                            :required="true"
                        />
                    </div>

                    <div class="relative">
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Filter Kategori (Opsional)</label>
                        @php
                            $optKategori = [];
                            foreach($kategoris as $k) {
                                $optKategori[$k->id] = $k->nama;
                            }
                        @endphp
                        <x-custom-select 
                            name="kategori" 
                            :options="$optKategori" 
                            placeholder="Semua Kategori" 
                            selected="{{ request('kategori') }}"
                        />
                    </div>

                    <div>
                        <button type="submit" class="w-full inline-flex justify-center items-center px-4 py-3 bg-indigo-600 border border-transparent rounded-xl font-bold text-sm text-white tracking-wide hover:bg-indigo-700 hover:shadow-lg focus:outline-none focus:ring-4 focus:ring-indigo-500/30 transition-all duration-300 btn-ripple shadow-sm">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            Cari Kecocokan
                        </button>
                    </div>
                </form>
            </div>

            @if(request()->filled('laporan_id'))
                <h3 class="text-lg font-bold mb-4 text-gray-800">Hasil Pencocokan Algoritma SMART</h3>

                @if($hasilPencocokan && $hasilPencocokan->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        @foreach($hasilPencocokan as $match)
                            <div class="bg-white rounded-lg shadow border border-gray-200 overflow-hidden relative">
                                
                                <div class="absolute top-0 right-0 bg-indigo-600 text-white font-bold px-3 py-1 text-sm rounded-bl-lg shadow-sm z-10">
                                    Skor: {{ number_format($match->smart_score, 1) }}%
                                </div>

                                <div class="h-48 w-full bg-gray-100">
                                    @if($match->fotoUtama())
                                        <img src="{{ Storage::url($match->fotoUtama()->file_path) }}" alt="{{ $match->nama_barang }}" class="w-full h-full object-cover">
                                    @else
                                        <div class="flex items-center justify-center h-full text-gray-400">Tidak ada foto</div>
                                    @endif
                                </div>
                                <div class="p-4">
                                    <div class="flex items-center gap-2 mb-1 text-xs text-gray-500">
                                        <span class="font-medium bg-gray-100 px-2 py-0.5 rounded">{{ $match->kategori->nama }}</span>
                                        <span>•</span>
                                        <span>{{ $match->tanggal_kejadian->diffForHumans() }}</span>
                                    </div>
                                    <h4 class="font-bold text-lg text-gray-900 leading-tight mb-2">{{ $match->nama_barang }}</h4>
                                    
                                    <div class="space-y-1 mb-4 text-sm text-gray-600">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span class="truncate">{{ $match->lokasi->nama }}</span>
                                        </div>
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 text-gray-400 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path></svg>
                                            <span class="truncate">Warna: {{ $match->warna }}</span>
                                        </div>
                                    </div>

                                    <!-- Score Breakdown (Optional) -->
                                    <div class="mb-4">
                                        <p class="text-xs font-semibold text-gray-700 mb-1">Rincian Kecocokan:</p>
                                        <div class="w-full bg-gray-200 rounded-full h-1.5 flex overflow-hidden">
                                            @php
                                                $catScore = ($match->smart_detail['kategori'] ?? 0) * 0.25 * 100;
                                                $locScore = ($match->smart_detail['lokasi'] ?? 0) * 0.25 * 100;
                                                $timeScore = ($match->smart_detail['waktu'] ?? 0) * 0.20 * 100;
                                                $ciriScore = ($match->smart_detail['ciri'] ?? 0) * 0.20 * 100;
                                                $warnaScore = ($match->smart_detail['warna'] ?? 0) * 0.10 * 100;
                                            @endphp
                                            <div style="width: {{ $catScore }}%" class="bg-blue-500" title="Kategori"></div>
                                            <div style="width: {{ $locScore }}%" class="bg-green-500" title="Lokasi"></div>
                                            <div style="width: {{ $timeScore }}%" class="bg-yellow-400" title="Waktu"></div>
                                            <div style="width: {{ $ciriScore }}%" class="bg-purple-500" title="Ciri Khusus"></div>
                                            <div style="width: {{ $warnaScore }}%" class="bg-pink-500" title="Warna"></div>
                                        </div>
                                    </div>

                                    <div class="flex gap-2">
                                        <a href="{{ route('laporan.show', $match->id) }}" class="flex-1 text-center bg-gray-100 hover:bg-gray-200 text-gray-800 text-xs font-bold py-2 px-4 rounded transition">Detail</a>
                                        <a href="{{ route('klaim.create', $match->id) }}" class="flex-1 text-center bg-indigo-600 hover:bg-indigo-700 text-white text-xs font-bold py-2 px-4 rounded transition">Klaim Ini</a>
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white p-8 text-center rounded-lg shadow border border-gray-200">
                        <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" aria-hidden="true">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        <h3 class="mt-2 text-sm font-medium text-gray-900">Tidak ada kecocokan tinggi</h3>
                        <p class="mt-1 text-sm text-gray-500">
                            Sistem belum menemukan barang yang sangat cocok (>40%) dengan laporan kehilangan Anda. Coba kurangi filter atau periksa kembali nanti.
                        </p>
                    </div>
                @endif
            @endif

        </div>
    </div>
</x-app-layout>
