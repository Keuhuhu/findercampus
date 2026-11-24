<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-blue-500">
                    <div class="text-sm font-medium text-gray-500 mb-1">Laporan Aktif Saya</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['laporan_aktif'] ?? 0 }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-amber-500">
                    <div class="text-sm font-medium text-gray-500 mb-1">Klaim Pending</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['klaim_pending'] ?? 0 }}</div>
                </div>
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 border-l-4 border-green-500">
                    <div class="text-sm font-medium text-gray-500 mb-1">Barang Berhasil Kembali</div>
                    <div class="text-3xl font-bold text-gray-900">{{ $stats['barang_kembali'] ?? 0 }}</div>
                </div>
            </div>

            <!-- Quick Actions -->
            <div class="flex flex-wrap gap-4 mb-8">
                <a href="{{ route('laporan.create') }}" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700 active:bg-blue-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                    Buat Laporan Baru
                </a>
                <a href="{{ route('pencarian.index') }}" class="inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                    Cari Kecocokan SMART
                </a>
            </div>
            
            @if(session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative">
                    {{ session('success') }}
                </div>
            @endif
            @if(session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative">
                    {{ session('error') }}
                </div>
            @endif

            <section class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <div class="flex items-center justify-between gap-4 mb-4">
                        <div>
                            <h3 class="text-lg font-bold">Laporan Saya</h3>
                            <p class="mt-1 text-sm text-gray-500">Lihat status, detail, dan label QR barang temuan Anda.</p>
                        </div>
                        <a href="{{ route('status', ['tab' => 'laporan']) }}" class="text-sm font-semibold text-primary hover:underline">Riwayat laporan</a>
                    </div>
                    @if($laporanSaya->isNotEmpty())
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2 lg:grid-cols-3">
                            @foreach($laporanSaya as $laporan)
                                <article class="rounded-xl border border-gray-200 p-4">
                                    <div class="flex items-start justify-between gap-3">
                                        <div>
                                            <p class="font-bold text-gray-900">{{ $laporan->nama_barang }}</p>
                                            <p class="mt-1 text-xs text-gray-500">{{ $laporan->kode_laporan }} · {{ $laporan->tipe === 'hilang' ? 'Hilang' : 'Ditemukan' }}</p>
                                        </div>
                                        <x-status-badge :status="$laporan->status" />
                                    </div>
                                    <div class="mt-4 flex flex-wrap gap-2">
                                        <a href="{{ route('laporan.show', $laporan) }}" class="rounded-lg bg-primary px-3 py-2 text-xs font-semibold text-white hover:bg-primary-light">Lihat detail</a>
                                        @if($laporan->tipe === 'ditemukan' && $laporan->qr_code_path)
                                            <a href="{{ route('laporan.qr', $laporan) }}" class="rounded-lg border border-gray-300 px-3 py-2 text-xs font-semibold text-gray-700 hover:bg-gray-50">Unduh QR</a>
                                        @endif
                                    </div>
                                </article>
                            @endforeach
                        </div>
                    @else
                        <p class="rounded-lg bg-gray-50 px-4 py-6 text-center text-sm text-gray-500">Anda belum membuat laporan.</p>
                    @endif
                </div>
            </section>

            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-8">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-bold mb-4">Laporan Terbaru di Kampus</h3>
                    @if(isset($laporanTerbaru) && count($laporanTerbaru) > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                            @foreach($laporanTerbaru as $lap)
                                <x-item-card :laporan="$lap" />
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-8">Belum ada laporan terbaru.</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
