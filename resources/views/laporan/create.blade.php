<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Buat Laporan' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Buat Laporan Baru</h1>
        <p class="text-sm text-gray-500 mt-1">Lengkapi informasi di bawah agar algoritma SMART dapat melakukan pencocokan dengan akurat.</p>
    </x-slot>

    <div class="max-w-4xl" x-data="{ 
        tipe: '{{ old('tipe', $tipe ?? 'hilang') }}',
        previewImages: [],
        handleFiles(event) {
            this.previewImages = [];
            const files = event.target.files;
            if (files.length > 5) {
                alert('Maksimal 5 foto yang diizinkan');
                event.target.value = '';
                return;
            }
            for (let i = 0; i < files.length; i++) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    this.previewImages.push(e.target.result);
                };
                reader.readAsDataURL(files[i]);
            }
        }
    }">
        <form action="/laporan" method="POST" enctype="multipart/form-data" class="space-y-8">
            @csrf

            <!-- Toggle Tipe Laporan -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-2 flex gap-2 w-full sm:w-auto sm:inline-flex">
                <label class="flex-1 sm:w-48">
                    <input type="radio" name="tipe" value="hilang" x-model="tipe" class="peer sr-only">
                    <div class="px-4 py-3 text-sm font-bold text-center rounded-lg cursor-pointer transition-all border-2 border-transparent peer-checked:bg-red-50 peer-checked:text-red-700 peer-checked:border-red-200 hover:bg-gray-50 text-gray-500">
                        🔴 Saya Kehilangan
                    </div>
                </label>
                <label class="flex-1 sm:w-48">
                    <input type="radio" name="tipe" value="ditemukan" x-model="tipe" class="peer sr-only">
                    <div class="px-4 py-3 text-sm font-bold text-center rounded-lg cursor-pointer transition-all border-2 border-transparent peer-checked:bg-emerald-50 peer-checked:text-emerald-700 peer-checked:border-emerald-200 hover:bg-gray-50 text-gray-500">
                        🟢 Saya Menemukan
                    </div>
                </label>
            </div>

            <div class="bg-white rounded-xl shadow-sm border border-gray-200 overflow-hidden">
                <!-- Section 1: Informasi Dasar -->
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">1</span>
                        Informasi Dasar Barang
                    </h2>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Barang / Merk <span class="text-red-500">*</span></label>
                            <input type="text" name="nama_barang" value="{{ old('nama_barang') }}" placeholder="Contoh: Dompet Hitam, Kunci Motor Honda" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('nama_barang') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                            @error('nama_barang')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Kategori Barang <span class="text-red-500">*</span></label>
                            <select name="kategori_id" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('kategori_id') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                                <option value="">-- Pilih Kategori --</option>
                                @foreach($kategoris as $kat)
                                    <option value="{{ $kat->id }}" {{ old('kategori_id') == $kat->id ? 'selected' : '' }}>{{ $kat->nama }}</option>
                                @endforeach
                            </select>
                            @error('kategori_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Warna Dominan <span class="text-red-500">*</span></label>
                            <select name="warna" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('warna') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                                <option value="">-- Pilih Warna --</option>
                                @php $warnas = ['Hitam', 'Putih', 'Abu-abu', 'Biru', 'Merah', 'Hijau', 'Kuning', 'Cokelat', 'Silver', 'Gold', 'Multi-warna', 'Lainnya']; @endphp
                                @foreach($warnas as $wrn)
                                    <option value="{{ $wrn }}" {{ old('warna') == $wrn ? 'selected' : '' }}>{{ $wrn }}</option>
                                @endforeach
                            </select>
                            @error('warna')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: Waktu & Lokasi -->
                <div class="p-6 sm:p-8 border-b border-gray-100 bg-gray-50/50">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">2</span>
                        Waktu & Lokasi Kejadian
                    </h2>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Lokasi Area Kampus <span class="text-red-500">*</span></label>
                            <select name="lokasi_id" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('lokasi_id') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                                <option value="">-- Pilih Area --</option>
                                @foreach($lokasis as $lok)
                                    <option value="{{ $lok->id }}" {{ old('lokasi_id') == $lok->id ? 'selected' : '' }}>{{ $lok->nama }}</option>
                                @endforeach
                            </select>
                            @error('lokasi_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Detail Lokasi Spesifik</label>
                            <input type="text" name="detail_lokasi" value="{{ old('detail_lokasi') }}" placeholder="Contoh: Toilet Lantai 2, Kantin Meja Pojok" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('detail_lokasi') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Tanggal Kejadian <span class="text-red-500">*</span></label>
                            <input type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', date('Y-m-d')) }}" max="{{ date('Y-m-d') }}" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('tanggal_kejadian') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                            @error('tanggal_kejadian')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-1.5">Perkiraan Waktu (Opsional)</label>
                            <input type="time" name="waktu_kejadian" value="{{ old('waktu_kejadian') }}" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition">
                        </div>
                    </div>
                </div>

                <!-- Section 3: Ciri Khusus -->
                <div class="p-6 sm:p-8 border-b border-gray-100">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">3</span>
                        Ciri Khusus & Kronologi
                    </h2>

                    <div class="mb-6">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Ciri-ciri Unik</label>
                        <textarea name="ciri_khusus" rows="2" placeholder="Goresan, stiker, nomor seri, bentuk gantungan..." class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition">{{ old('ciri_khusus') }}</textarea>
                        <p class="text-xs text-gray-500 mt-1">Sangat penting untuk membantu verifikasi kepemilikan.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5" x-text="tipe === 'hilang' ? 'Kronologi Kehilangan (Opsional)' : 'Deskripsi Penemuan (Opsional)'"></label>
                        <textarea name="deskripsi" rows="3" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition">{{ old('deskripsi') }}</textarea>
                    </div>
                </div>

                <!-- Section 4: Bukti Visual -->
                <div class="p-6 sm:p-8">
                    <h2 class="text-lg font-bold text-gray-900 mb-6 flex items-center gap-2">
                        <span class="flex items-center justify-center w-6 h-6 rounded-full bg-primary text-white text-xs">4</span>
                        Bukti Visual
                    </h2>

                    <div class="w-full">
                        <label class="block text-sm font-semibold text-gray-700 mb-1.5">Unggah Foto (Maksimal 5)</label>
                        
                        <!-- Custom File Upload UI -->
                        <div class="relative border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:bg-gray-50 transition cursor-pointer" onclick="document.getElementById('foto_input').click()">
                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                            </svg>
                            <div class="mt-4 flex text-sm text-gray-600 justify-center">
                                <span class="relative cursor-pointer bg-transparent rounded-md font-medium text-accent hover:text-accent focus-within:outline-none">
                                    <span>Klik untuk mengunggah</span>
                                </span>
                                <p class="pl-1">atau tarik & lepas file ke sini</p>
                            </div>
                            <p class="text-xs text-gray-500 mt-2">PNG, JPG, WEBP hingga 5MB</p>
                            
                            <!-- Actual input hidden -->
                            <input id="foto_input" type="file" name="foto[]" multiple accept="image/*" class="sr-only" @change="handleFiles">
                        </div>

                        <!-- Image Preview Grid -->
                        <template x-if="previewImages.length > 0">
                            <div class="mt-4 grid grid-cols-2 sm:grid-cols-5 gap-4">
                                <template x-for="(src, index) in previewImages" :key="index">
                                    <div class="relative rounded-lg overflow-hidden border border-gray-200 aspect-square">
                                        <img :src="src" class="w-full h-full object-cover">
                                        <div x-show="index === 0" class="absolute top-0 left-0 right-0 bg-primary/80 text-white text-[10px] font-bold py-1 text-center backdrop-blur-sm">
                                            FOTO UTAMA
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>

                        @error('foto')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
                        @error('foto.*')<p class="mt-2 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Footer Actions -->
                <div class="p-6 sm:p-8 bg-gray-50 border-t border-gray-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                    <div class="flex items-center gap-2 text-sm text-gray-600">
                        <svg class="w-5 h-5 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                        Algoritma SMART Match Otomatis Aktif
                    </div>
                    <div class="flex items-center gap-3 w-full sm:w-auto">
                        <a href="/dashboard" class="w-full sm:w-auto px-6 py-2.5 bg-white border border-gray-300 text-gray-700 font-semibold text-sm rounded-lg hover:bg-gray-50 transition text-center">Batal</a>
                        <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-primary text-white font-semibold text-sm rounded-lg hover:bg-primary-light transition flex items-center justify-center gap-2 shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                            Simpan & Kirim
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>
</x-app-layout>
