<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Status Klaim' => '/status?tab=klaim', 'Detail Klaim' => '#']" />
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-4xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6">
        
        <!-- Main Content -->
        <div class="md:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 mb-1">Detail Klaim #CLM{{ $klaim->id }}</h1>
                        <p class="text-sm text-gray-500">Diajukan pada {{ $klaim->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    <div>
                        @if($klaim->status === 'pending')
                            <span class="inline-flex px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase">Menunggu Review</span>
                        @elseif($klaim->status === 'disetujui')
                            <span class="inline-flex px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase">Disetujui</span>
                        @elseif($klaim->status === 'ditolak')
                            <span class="inline-flex px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase">Ditolak</span>
                        @endif
                    </div>
                </div>

                @if($klaim->status === 'disetujui' && $klaim->riwayatPengembalian)
                <div class="px-6 py-4 bg-emerald-50 border-b border-emerald-100 flex items-center justify-between">
                    <div>
                        <h4 class="font-bold text-emerald-900 text-sm">Barang Telah Dikembalikan</h4>
                        <p class="text-xs text-emerald-700">Silakan unduh surat bukti pengembalian resmi.</p>
                    </div>
                    <a href="/export/bukti-pengembalian/{{ $klaim->riwayatPengembalian->id }}" class="px-4 py-2 bg-emerald-600 text-white text-xs font-bold rounded-lg hover:bg-emerald-700 transition shadow-sm">
                        Unduh Bukti (PDF)
                    </a>
                </div>
                @endif

                <div class="p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">Bukti Kepemilikan (Teks):</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-100 text-sm text-gray-700 whitespace-pre-wrap">{{ $klaim->bukti_kepemilikan }}</div>

                    @if($klaim->foto_bukti)
                        <h3 class="text-sm font-bold text-gray-900 mt-6 mb-3">Foto Bukti Lampiran:</h3>
                        <a href="{{ Storage::url($klaim->foto_bukti) }}" target="_blank" class="block w-48 rounded-lg overflow-hidden border border-gray-200 hover:ring-2 hover:ring-accent transition">
                            <img src="{{ Storage::url($klaim->foto_bukti) }}" class="w-full h-auto object-cover">
                        </a>
                    @endif
                </div>
            </div>

            @if($klaim->verifikasi && $klaim->verifikasi->catatan)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Catatan Verifikator</h3>
                    <p class="text-sm text-gray-600 bg-blue-50 p-3 rounded border border-blue-100">{{ $klaim->verifikasi->catatan }}</p>
                </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="md:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="w-full h-32 bg-gray-100">
                    @if($klaim->laporan->fotoUtama())
                        <img src="{{ Storage::url($klaim->laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                    @endif
                </div>
                <div class="p-5">
                    <h3 class="font-bold text-gray-900 text-lg mb-1">{{ $klaim->laporan->nama_barang }}</h3>
                    <p class="text-xs text-gray-500 mb-4">{{ $klaim->laporan->kode_laporan }}</p>
                    <a href="/laporan/{{ $klaim->laporan_id }}" class="w-full block text-center px-4 py-2 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition">
                        Lihat Laporan Asli
                    </a>
                </div>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-sm font-bold text-gray-900 mb-3">Informasi Pengklaim</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-primary text-white flex items-center justify-center font-bold text-sm">
                        {{ substr($klaim->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $klaim->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $klaim->user->fakultas }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

