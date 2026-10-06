<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Riwayat Laporan Saya' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Riwayat Laporan Saya</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar semua barang yang pernah Anda laporkan hilang atau ditemukan.</p>
    </x-slot>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden mb-6">
        <!-- Filter Bar -->
        <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex flex-col sm:flex-row gap-4 items-center justify-between">
            <form method="GET" action="/status" class="flex flex-wrap gap-3 items-center w-full sm:w-auto">
                <select name="tipe" onchange="this.form.submit()" class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:ring-accent focus:border-accent">
                    <option value="">Semua Tipe</option>
                    <option value="hilang" {{ request('tipe') == 'hilang' ? 'selected' : '' }}>Barang Hilang</option>
                    <option value="ditemukan" {{ request('tipe') == 'ditemukan' ? 'selected' : '' }}>Barang Ditemukan</option>
                </select>
                
                <select name="status" onchange="this.form.submit()" class="text-sm bg-white border border-gray-200 rounded-lg px-3 py-2 focus:ring-accent focus:border-accent">
                    <option value="">Semua Status</option>
                    <option value="aktif" {{ request('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                    <option value="diklaim" {{ request('status') == 'diklaim' ? 'selected' : '' }}>Diklaim</option>
                    <option value="selesai" {{ request('status') == 'selesai' ? 'selected' : '' }}>Selesai</option>
                    <option value="dibatalkan" {{ request('status') == 'dibatalkan' ? 'selected' : '' }}>Dibatalkan</option>
                </select>
            </form>
            
            <a href="/laporan/buat" class="w-full sm:w-auto inline-flex items-center justify-center px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-light transition">
                + Laporan Baru
            </a>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-white border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Barang & ID</th>
                        <th class="p-4 font-semibold">Tipe</th>
                        <th class="p-4 font-semibold">Tanggal Kejadian</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        @if($laporan->fotoUtama())
                                            <img src="{{ Storage::url($laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-gray-900">{{ $laporan->nama_barang }}</p>
                                        <p class="text-xs text-gray-500">{{ $laporan->kode_laporan }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4">
                                @if($laporan->tipe === 'hilang')
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-red-50 text-red-700">Hilang</span>
                                @else
                                    <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-emerald-50 text-emerald-700">Ditemukan</span>
                                @endif
                            </td>
                            <td class="p-4">
                                <p class="text-sm text-gray-900">{{ $laporan->tanggal_kejadian->format('d/m/Y') }}</p>
                            </td>
                            <td class="p-4">
                                <x-status-badge :status="$laporan->status" />
                            </td>
                            <td class="p-4 text-right">
                                <a href="/laporan/{{ $laporan->id }}" class="text-sm text-accent hover:underline font-medium">Detail →</a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="p-8 text-center text-gray-500 text-sm">
                                Belum ada laporan yang sesuai.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($laporans->hasPages())
            <div class="p-4 border-t border-gray-100 bg-white">
                {{ $laporans->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
