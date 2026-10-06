    <x-app-layout>
        <x-slot name="breadcrumb">
            <x-breadcrumb :breadcrumbs="['Pencarian SMART' => '/pencarian', 'Detail' => '/laporan/'.$laporan->id, 'Ajukan Klaim' => '#']" />
        </x-slot>

        <div class="max-w-3xl mx-auto">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <div class="bg-gray-50 border-b border-gray-200 p-6">
                    <h1 class="text-xl font-bold text-gray-900 mb-2">Ajukan Klaim Kepemilikan</h1>
                    <p class="text-sm text-gray-500">Anda akan mengajukan klaim untuk barang berikut. Mohon siapkan bukti kuat.</p>
                    
                    <div class="mt-4 flex gap-4 items-center bg-white p-3 rounded-lg border border-gray-100">
                        <div class="w-16 h-16 rounded overflow-hidden bg-gray-100 shrink-0">
                            @if($laporan->fotoUtama())
                                <img src="{{ Storage::url($laporan->fotoUtama()->file_path) }}" class="w-full h-full object-cover">
                            @endif
                        </div>
                        <div>
                            <h3 class="font-bold text-gray-900">{{ $laporan->nama_barang }}</h3>
                            <p class="text-xs text-gray-500">{{ $laporan->kategori->nama }} &bull; Ditemukan di {{ $laporan->lokasi->nama }}</p>
                        </div>
                    </div>
                </div>

                <form action="/klaim" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                    @csrf
                    <input type="hidden" name="laporan_id" value="{{ $laporan->id }}">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Teks Bukti Kepemilikan <span class="text-red-500">*</span></label>
                        <textarea name="bukti_kepemilikan" rows="4" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm transition" placeholder="Sebutkan detail yang tidak disebutkan di laporan publik (misal: isi dompet, password hp, ciri cacat spesifik, dll)." required>{{ old('bukti_kepemilikan') }}</textarea>
                        @error('bukti_kepemilikan')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Bukti (Opsional)</label>
                        <input type="file" name="foto_bukti" accept="image/*" class="w-full px-4 py-2 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm transition text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-md file:border-0 file:text-sm file:font-semibold file:bg-primary file:text-white hover:file:bg-primary-light">
                        <p class="text-xs text-gray-500 mt-2">Contoh: Foto kardus, nota pembelian, atau foto lama Anda bersama barang tersebut.</p>
                        @error('foto_bukti')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>

                    <div class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-start gap-3">
                        <svg class="w-5 h-5 text-blue-600 mt-0.5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        <div class="text-xs text-blue-800 leading-relaxed">
                            <strong class="block mb-1">Informasi Proses Verifikasi</strong>
                            Klaim Anda akan ditinjau oleh Admin dan/atau penemu barang. Jika disetujui, Anda akan mendapatkan akses ke titik pengambilan. Menyalahgunakan sistem klaim dapat mengakibatkan akun Anda diblokir.
                        </div>
                    </div>

                    <div class="pt-4 flex gap-3 justify-end">
                        <a href="/laporan/{{ $laporan->id }}" class="px-5 py-2.5 border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 transition">Batal</a>
                        <button type="submit" class="px-5 py-2.5 bg-primary text-white font-bold text-sm rounded-lg hover:bg-primary-light transition shadow-sm">
                            Kirim Klaim Kepemilikan
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </x-app-layout>

