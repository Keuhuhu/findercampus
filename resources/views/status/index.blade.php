<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Status & Riwayat' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Pusat Status</h1>
        <p class="text-sm text-gray-500 mt-1">Pantau perkembangan laporan dan pengajuan klaim Anda di sini.</p>
    </x-slot>

    <!-- Tab Navigation -->
    <div class="border-b border-gray-200 mb-6 flex overflow-x-auto">
        <a href="/status?tab=laporan" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $tab === 'laporan' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} whitespace-nowrap transition">
            Laporan Saya
        </a>
        <a href="/status?tab=klaim" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $tab === 'klaim' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} whitespace-nowrap transition">
            Klaim Diajukan
        </a>
        <a href="/status?tab=klaim_masuk" class="px-6 py-3 font-semibold text-sm border-b-2 {{ $tab === 'klaim_masuk' ? 'border-primary text-primary' : 'border-transparent text-gray-500 hover:text-gray-700' }} whitespace-nowrap transition flex items-center gap-2">
            Klaim Masuk 
            @php $countMasuk = App\Models\Klaim::whereHas('laporan', fn($q) => $q->where('user_id', Auth::id()))->where('status', 'pending')->count(); @endphp
            @if($countMasuk > 0)
                <span class="bg-red-500 text-white text-[10px] px-1.5 py-0.5 rounded-full">{{ $countMasuk }}</span>
            @endif
        </a>
    </div>

    <!-- Content Area -->
    <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
        
        @if($tab === 'laporan')
            <div class="p-4 border-b border-gray-100 bg-gray-50/50 flex justify-end">
                <a href="/laporan/buat" class="px-4 py-2 bg-primary text-white text-sm font-semibold rounded-lg hover:bg-primary-light transition">
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
                                <td class="p-4 text-sm text-gray-900">{{ $laporan->tanggal_kejadian->format('d/m/Y') }}</td>
                                <td class="p-4"><x-status-badge :status="$laporan->status" /></td>
                                <td class="p-4 text-right">
                                    <a href="/laporan/{{ $laporan->id }}" class="text-sm text-accent hover:underline font-medium">Detail →</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="5" class="p-8 text-center text-gray-500 text-sm">Belum ada laporan.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($laporans->hasPages()) <div class="p-4 border-t border-gray-100 bg-white">{{ $laporans->links() }}</div> @endif
        
        @elseif($tab === 'klaim')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold">Barang yang Diklaim</th>
                            <th class="p-4 font-semibold">Tanggal Pengajuan</th>
                            <th class="p-4 font-semibold">Status Klaim</th>
                            <th class="p-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($klaims as $klaim)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-lg bg-gray-100 overflow-hidden shrink-0">
                                            @if($klaim->laporan->fotoUtama())
                                                <img src="{{ Storage::url($klaim->laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-900">{{ $klaim->laporan->nama_barang }}</p>
                                            <p class="text-xs text-gray-500">ID Klaim: CLM{{ $klaim->id }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="p-4 text-sm text-gray-900">{{ $klaim->created_at->format('d/m/Y H:i') }}</td>
                                <td class="p-4">
                                    @if($klaim->status === 'pending')
                                        <span class="inline-flex px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs font-bold uppercase">Pending</span>
                                    @elseif($klaim->status === 'disetujui')
                                        <span class="inline-flex px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs font-bold uppercase">Disetujui</span>
                                    @elseif($klaim->status === 'ditolak')
                                        <span class="inline-flex px-2 py-0.5 bg-red-100 text-red-800 rounded text-xs font-bold uppercase">Ditolak</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <a href="/klaim/{{ $klaim->id }}" class="text-sm text-accent hover:underline font-medium">Lihat Bukti →</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-500 text-sm">Anda belum mengajukan klaim apapun.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($klaims->hasPages()) <div class="p-4 border-t border-gray-100 bg-white">{{ $klaims->links() }}</div> @endif
            
        @elseif($tab === 'klaim_masuk')
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-white border-b border-gray-100 text-xs text-gray-500 uppercase tracking-wider">
                            <th class="p-4 font-semibold">Laporan Anda</th>
                            <th class="p-4 font-semibold">Diajukan Oleh</th>
                            <th class="p-4 font-semibold">Status Klaim</th>
                            <th class="p-4 font-semibold text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($klaimMasuk as $klaim)
                            <tr class="hover:bg-gray-50 transition">
                                <td class="p-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded bg-gray-100 overflow-hidden shrink-0">
                                            @if($klaim->laporan->fotoUtama())
                                                <img src="{{ Storage::url($klaim->laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                                            @endif
                                        </div>
                                        <p class="text-sm font-bold text-gray-900">{{ $klaim->laporan->nama_barang }}</p>
                                    </div>
                                </td>
                                <td class="p-4">
                                    <p class="text-sm text-gray-900 font-semibold">{{ $klaim->user->name }}</p>
                                    <p class="text-xs text-gray-500">{{ $klaim->created_at->diffForHumans() }}</p>
                                </td>
                                <td class="p-4">
                                    @if($klaim->status === 'pending')
                                        <span class="inline-flex px-2 py-0.5 bg-amber-100 text-amber-800 rounded text-xs font-bold uppercase">Perlu Direview</span>
                                    @elseif($klaim->status === 'disetujui')
                                        <span class="inline-flex px-2 py-0.5 bg-emerald-100 text-emerald-800 rounded text-xs font-bold uppercase">Disetujui</span>
                                    @elseif($klaim->status === 'ditolak')
                                        <span class="inline-flex px-2 py-0.5 bg-red-100 text-red-800 rounded text-xs font-bold uppercase">Ditolak</span>
                                    @endif
                                </td>
                                <td class="p-4 text-right">
                                    <a href="/klaim/{{ $klaim->id }}" class="text-sm text-accent hover:underline font-medium">Review Bukti →</a>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="4" class="p-8 text-center text-gray-500 text-sm">Tidak ada klaim yang masuk untuk laporan Anda.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($klaimMasuk->hasPages()) <div class="p-4 border-t border-gray-100 bg-white">{{ $klaimMasuk->links() }}</div> @endif
        @endif
        
    </div>
</x-app-layout>

