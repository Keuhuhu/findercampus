<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Admin' => '/admin', 'Verifikasi Klaim' => '#']" />
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium">
            {{ session('success') }}
        </div>
    @endif

    <div class="max-w-5xl mx-auto grid grid-cols-1 lg:grid-cols-3 gap-8">
        
        <!-- Left: Klaim Details -->
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="p-6 border-b border-gray-100 flex justify-between items-start">
                    <div>
                        <h1 class="text-xl font-bold text-gray-900 mb-1">Verifikasi Klaim #CLM{{ $klaim->id }}</h1>
                        <p class="text-sm text-gray-500">Diajukan {{ $klaim->created_at->translatedFormat('d M Y, H:i') }}</p>
                    </div>
                    @if($klaim->status === 'pending')
                        <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-full text-xs font-bold uppercase">Pending</span>
                    @elseif($klaim->status === 'disetujui')
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-800 rounded-full text-xs font-bold uppercase">Disetujui</span>
                    @elseif($klaim->status === 'ditolak')
                        <span class="px-3 py-1 bg-red-100 text-red-800 rounded-full text-xs font-bold uppercase">Ditolak</span>
                    @endif
                </div>

                <!-- Pengklaim Info -->
                <div class="p-6 bg-blue-50/50 border-b border-gray-100">
                    <h3 class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-3">Informasi Pengklaim</h3>
                    <div class="flex items-center gap-3">
                        <div class="w-12 h-12 rounded-full bg-primary text-white flex items-center justify-center font-bold text-lg">
                            {{ substr($klaim->user->name, 0, 1) }}
                        </div>
                        <div>
                            <p class="font-bold text-gray-900">{{ $klaim->user->name }}</p>
                            <p class="text-xs text-gray-500">{{ $klaim->user->nim_nip }} &bull; {{ $klaim->user->fakultas }} &bull; {{ $klaim->user->email }}</p>
                        </div>
                    </div>
                </div>

                <!-- Bukti Kepemilikan -->
                <div class="p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-3">📝 Bukti Kepemilikan (Teks)</h3>
                    <div class="bg-gray-50 p-4 rounded-lg border border-gray-200 text-sm text-gray-700 whitespace-pre-wrap leading-relaxed">{{ $klaim->bukti_kepemilikan }}</div>

                    @if($klaim->foto_bukti)
                        <h3 class="text-sm font-bold text-gray-900 mt-6 mb-3">📸 Foto Bukti</h3>
                        <a href="{{ Storage::url($klaim->foto_bukti) }}" target="_blank" class="block w-64 rounded-lg overflow-hidden border-2 border-gray-200 hover:ring-4 hover:ring-accent/20 transition">
                            <img src="{{ Storage::url($klaim->foto_bukti) }}" class="w-full h-auto object-cover">
                        </a>
                    @endif
                </div>
            </div>

            <!-- Action Buttons (only if pending) -->
            @if($klaim->status === 'pending')
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6" x-data="{ showReject: false }">
                <!-- Approve -->
                <form action="/admin/verifikasi/{{ $klaim->id }}/approve" method="POST" class="bg-emerald-50 rounded-xl border border-emerald-200 p-5">
                    @csrf
                    <h3 class="text-sm font-bold text-emerald-900 mb-3 flex items-center gap-2">
                        ✅ Setujui Klaim
                    </h3>
                    <textarea name="catatan" rows="2" class="w-full text-sm border-emerald-200 rounded-lg bg-white mb-3" placeholder="Catatan opsional..."></textarea>
                    <button type="submit" onclick="return confirm('Yakin ingin menyetujui klaim ini? Barang akan ditandai telah dikembalikan.')" class="w-full px-4 py-2.5 bg-emerald-600 text-white font-bold text-sm rounded-lg hover:bg-emerald-700 transition shadow-sm">
                        Setujui & Kembalikan Barang
                    </button>
                </form>

                <!-- Reject -->
                <form action="/admin/verifikasi/{{ $klaim->id }}/reject" method="POST" class="bg-red-50 rounded-xl border border-red-200 p-5">
                    @csrf
                    <h3 class="text-sm font-bold text-red-900 mb-3 flex items-center gap-2">
                        ❌ Tolak Klaim
                    </h3>
                    <textarea name="catatan" rows="2" class="w-full text-sm border-red-200 rounded-lg bg-white mb-3" placeholder="Alasan penolakan (wajib diisi)..." required></textarea>
                    @error('catatan')<p class="text-xs text-red-500 mb-2">{{ $message }}</p>@enderror
                    <button type="submit" onclick="return confirm('Yakin ingin menolak klaim ini?')" class="w-full px-4 py-2.5 bg-red-600 text-white font-bold text-sm rounded-lg hover:bg-red-700 transition shadow-sm">
                        Tolak Klaim
                    </button>
                </form>
            </div>
            @endif

            <!-- Verifikasi Result -->
            @if($klaim->verifikasi)
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                    <h3 class="text-sm font-bold text-gray-900 mb-2">Hasil Verifikasi</h3>
                    <p class="text-sm text-gray-600 mb-1"><strong>Status:</strong> {{ ucfirst($klaim->verifikasi->status) }}</p>
                    <p class="text-sm text-gray-600 mb-1"><strong>Admin:</strong> {{ $klaim->verifikasi->admin->name ?? '-' }}</p>
                    <p class="text-sm text-gray-600"><strong>Catatan:</strong> {{ $klaim->verifikasi->catatan }}</p>
                </div>
            @endif
        </div>

        <!-- Right: Barang Info -->
        <div class="lg:col-span-1 space-y-6">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                @if($klaim->laporan->fotos->count() > 0)
                    <div class="w-full h-48 bg-gray-100">
                        <img src="{{ Storage::url($klaim->laporan->fotos->first()->file_path) }}" class="w-full h-full object-cover">
                    </div>
                @endif
                <div class="p-5">
                    <div class="flex items-center gap-2 mb-2">
                        @if($klaim->laporan->tipe === 'hilang')
                            <span class="px-2 py-0.5 bg-red-50 text-red-700 text-[10px] font-bold rounded">HILANG</span>
                        @else
                            <span class="px-2 py-0.5 bg-emerald-50 text-emerald-700 text-[10px] font-bold rounded">DITEMUKAN</span>
                        @endif
                        <span class="text-[10px] text-gray-400 font-medium">{{ $klaim->laporan->kode_laporan }}</span>
                    </div>
                    <h3 class="font-bold text-gray-900 text-lg">{{ $klaim->laporan->nama_barang }}</h3>
                    
                    <div class="mt-4 space-y-2 text-sm text-gray-600">
                        <div class="flex justify-between"><span class="text-gray-400">Kategori</span><span class="font-semibold text-gray-900">{{ $klaim->laporan->kategori->nama }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Warna</span><span class="font-semibold text-gray-900">{{ $klaim->laporan->warna }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Lokasi</span><span class="font-semibold text-gray-900">{{ $klaim->laporan->lokasi->nama }}</span></div>
                        <div class="flex justify-between"><span class="text-gray-400">Tanggal</span><span class="font-semibold text-gray-900">{{ $klaim->laporan->tanggal_kejadian->format('d/m/Y') }}</span></div>
                    </div>

                    @if($klaim->laporan->ciri_khusus)
                        <div class="mt-4 pt-4 border-t border-gray-100">
                            <h4 class="text-xs font-bold text-gray-400 uppercase mb-1">Ciri Khusus</h4>
                            <p class="text-sm text-gray-700">{{ $klaim->laporan->ciri_khusus }}</p>
                        </div>
                    @endif
                    
                    <a href="/laporan/{{ $klaim->laporan_id }}" target="_blank" class="mt-4 w-full block text-center px-4 py-2 border border-gray-200 text-gray-700 text-xs font-bold rounded-lg hover:bg-gray-50 transition">
                        Buka Laporan Asli ↗
                    </a>
                </div>
            </div>

            <!-- Pelapor Info -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5">
                <h3 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-3">Pelapor Asli</h3>
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 rounded-full bg-gray-200 text-gray-600 flex items-center justify-center font-bold text-sm">
                        {{ substr($klaim->laporan->user->name, 0, 1) }}
                    </div>
                    <div>
                        <p class="text-sm font-bold text-gray-900">{{ $klaim->laporan->user->name }}</p>
                        <p class="text-xs text-gray-500">{{ $klaim->laporan->user->nim_nip }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
