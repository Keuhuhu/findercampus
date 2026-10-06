<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Jelajahi Temuan' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Jelajahi Semua Temuan</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar barang temuan terbaru yang diserahkan ke posko kampus.</p>
    </x-slot>

    <!-- Filters -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-4 mb-8">
        <form action="/laporan" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="md:col-span-1">
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari barang..." class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent">
            </div>
            <div class="md:col-span-1">
                <select name="kategori" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                    <option value="">Semua Kategori</option>
                    @foreach($kategoris as $kat)
                        <option value="{{ $kat->id }}" {{ request('kategori') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1">
                <select name="lokasi" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-accent">
                    <option value="">Semua Lokasi</option>
                    @foreach($lokasis as $lok)
                        <option value="{{ $lok->id }}" {{ request('lokasi') == $lok->id ? 'selected' : '' }}>{{ $lok->nama }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-1 flex gap-2">
                <button type="submit" class="flex-1 px-4 py-2 bg-gray-900 text-white font-semibold text-sm rounded-lg hover:bg-black transition">Filter</button>
                @if(request()->anyFilled(['q', 'kategori', 'lokasi']))
                    <a href="/laporan" class="px-4 py-2 bg-gray-100 text-gray-600 font-semibold text-sm rounded-lg hover:bg-gray-200 transition text-center">Reset</a>
                @endif
            </div>
        </form>
    </div>

    <!-- Grid Results -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
        @forelse($laporans as $laporan)
            <x-item-card :laporan="$laporan" />
        @empty
            <div class="col-span-1 sm:col-span-2 lg:col-span-4 text-center py-16 bg-white rounded-xl shadow-sm border border-gray-200">
                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <p class="text-gray-500 font-medium">Tidak ada temuan yang sesuai filter.</p>
            </div>
        @endforelse
    </div>

    <!-- Pagination -->
    <div class="mt-8">
        {{ $laporans->withQueryString()->links() }}
    </div>

</x-app-layout>
