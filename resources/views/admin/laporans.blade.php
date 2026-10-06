<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Admin' => '/admin', 'Semua Laporan' => '#']" />
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Semua Laporan</h1>
        <p class="text-sm text-gray-500 mt-1">Daftar seluruh laporan barang hilang & ditemukan di FinderCampus.</p>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                        <th class="p-4 font-semibold">Barang</th>
                        <th class="p-4 font-semibold">Pelapor</th>
                        <th class="p-4 font-semibold">Tipe</th>
                        <th class="p-4 font-semibold">Lokasi</th>
                        <th class="p-4 font-semibold">Status</th>
                        <th class="p-4 font-semibold text-right">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @foreach($laporans as $laporan)
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
                                        <p class="text-xs text-gray-500">{{ $laporan->kode_laporan }} &bull; {{ $laporan->kategori->nama }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="p-4 text-sm text-gray-700">{{ $laporan->user->name }}</td>
                            <td class="p-4">
                                @if($laporan->tipe === 'hilang')
                                    <span class="px-2 py-0.5 bg-red-50 text-red-700 rounded text-xs font-semibold">Hilang</span>
                                @else
                                    <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 rounded text-xs font-semibold">Ditemukan</span>
                                @endif
                            </td>
                            <td class="p-4 text-sm text-gray-600">{{ $laporan->lokasi->nama }}</td>
                            <td class="p-4"><x-status-badge :status="$laporan->status" /></td>
                            <td class="p-4 text-right">
                                <a href="/laporan/{{ $laporan->id }}" class="text-sm text-accent hover:underline font-medium">Detail →</a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @if($laporans->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $laporans->links() }}</div>
        @endif
    </div>
</x-app-layout>
