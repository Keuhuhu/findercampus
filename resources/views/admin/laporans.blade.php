<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Admin' => '/admin', 'Semua Laporan' => '#']" />
    </x-slot>

    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Semua Laporan</h1>
        <p class="text-sm text-gray-500 mt-1">Kelola laporan pengguna dan pulihkan laporan yang diarsipkan.</p>
    </div>

    @if(session('success'))
        <div class="mb-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm font-medium text-emerald-800">{{ session('success') }}</div>
    @endif

    <nav class="mb-5 flex gap-2" aria-label="Filter status laporan">
        <a href="{{ route('admin.laporans') }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ ! $archived ? 'bg-primary text-white' : 'border border-gray-200 bg-white text-gray-700' }}">Laporan aktif</a>
        <a href="{{ route('admin.laporans', ['archived' => true]) }}" class="rounded-lg px-4 py-2 text-sm font-semibold {{ $archived ? 'bg-primary text-white' : 'border border-gray-200 bg-white text-gray-700' }}">Arsip</a>
    </nav>

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
                    @forelse($laporans as $laporan)
                        <tr class="hover:bg-gray-50 transition">
                            <td class="p-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                        @if($laporan->fotoUtama())
                                            <img src="@mediaUrl($laporan->fotoUtama()->file_path)" class="w-full h-full object-cover">
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
                                <div class="flex flex-wrap justify-end gap-2">
                                    <a href="{{ route('laporan.show', $laporan) }}" class="text-sm text-accent hover:underline font-medium">Detail</a>
                                    @if($archived)
                                        <form action="{{ route('admin.laporans.restore', $laporan->id) }}" method="POST" onsubmit="return confirm('Pulihkan laporan ini?')">
                                            @csrf
                                            <button type="submit" class="text-sm font-semibold text-emerald-700 hover:underline">Pulihkan</button>
                                        </form>
                                    @else
                                        <a href="{{ route('laporan.edit', $laporan) }}" class="text-sm font-semibold text-gray-700 hover:underline">Edit</a>
                                        @if($laporan->tipe === 'ditemukan' && $laporan->qr_code_path)
                                            <a href="{{ route('laporan.qr', $laporan) }}" class="text-sm font-semibold text-gray-700 hover:underline">Unduh QR</a>
                                        @endif
                                        <form action="{{ route('laporan.destroy', $laporan) }}" method="POST" onsubmit="return confirm('Arsipkan laporan ini?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-sm font-semibold text-red-600 hover:underline">Arsipkan</button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="p-8 text-center text-sm text-gray-500">{{ $archived ? 'Belum ada laporan yang diarsipkan.' : 'Belum ada laporan.' }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($laporans->hasPages())
            <div class="p-4 border-t border-gray-100">{{ $laporans->links() }}</div>
        @endif
    </div>
</x-app-layout>
