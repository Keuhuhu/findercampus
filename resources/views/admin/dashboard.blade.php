<x-app-layout>
    <div class="mb-8">
        <h1 class="text-3xl font-extrabold text-gray-900">Admin Dashboard</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola laporan, klaim, dan pengguna FinderCampus.</p>
    </div>

    <!-- Stats Cards -->
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-10">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-primary">{{ $stats['total_laporan'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Total Laporan</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-blue-600">{{ $stats['laporan_aktif'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Laporan Aktif</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-amber-600">{{ $stats['klaim_pending'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Klaim Pending</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-gray-700">{{ $stats['total_klaim'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Total Klaim</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-emerald-600">{{ $stats['barang_dikembalikan'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Dikembalikan</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
            <p class="text-3xl font-extrabold text-gray-700">{{ $stats['total_user'] }}</p>
            <p class="text-xs text-gray-500 font-semibold mt-1 uppercase tracking-wider">Pengguna</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <!-- Klaim Pending -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">🔔 Klaim Menunggu Verifikasi</h2>
                <span class="bg-amber-100 text-amber-800 px-2 py-0.5 rounded-full text-xs font-bold">{{ $stats['klaim_pending'] }}</span>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($klaimPending as $klaim)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                @if($klaim->laporan->fotoUtama())
                                    <img src="{{ Storage::url($klaim->laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $klaim->laporan->nama_barang }}</p>
                                <p class="text-xs text-gray-500">Oleh: {{ $klaim->user->name }} &bull; {{ $klaim->created_at->diffForHumans() }}</p>
                            </div>
                        </div>
                        <a href="/admin/verifikasi/{{ $klaim->id }}" class="px-3 py-1.5 bg-primary text-white text-xs font-bold rounded-lg hover:bg-primary-light transition">
                            Review →
                        </a>
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 text-sm">Tidak ada klaim yang menunggu.</div>
                @endforelse
            </div>
        </div>

        <!-- Laporan Terbaru -->
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
            <div class="p-5 border-b border-gray-100 flex justify-between items-center">
                <h2 class="text-lg font-bold text-gray-900">📋 Laporan Terbaru</h2>
                <a href="/admin/laporans" class="text-xs font-bold text-accent hover:underline">Lihat Semua →</a>
            </div>
            <div class="divide-y divide-gray-100">
                @forelse($laporanTerbaru as $laporan)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                @if($laporan->fotoUtama())
                                    <img src="{{ Storage::url($laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                                @endif
                            </div>
                            <div>
                                <p class="text-sm font-bold text-gray-900">{{ $laporan->nama_barang }}</p>
                                <div class="flex items-center gap-2 mt-0.5">
                                    @if($laporan->tipe === 'hilang')
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-red-50 text-red-700 font-bold">Hilang</span>
                                    @else
                                        <span class="text-[10px] px-1.5 py-0.5 rounded bg-emerald-50 text-emerald-700 font-bold">Ditemukan</span>
                                    @endif
                                    <span class="text-xs text-gray-400">{{ $laporan->user->name }}</span>
                                </div>
                            </div>
                        </div>
                        <x-status-badge :status="$laporan->status" />
                    </div>
                @empty
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada laporan.</div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
