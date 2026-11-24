<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Laporan Saya' => '/dashboard', 'Edit Laporan' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Edit Laporan</h1>
        <p class="mt-1 text-sm text-gray-500">Perbarui detail laporan. Status laporan dikelola oleh proses klaim dan sistem.</p>
    </x-slot>

    <div class="mx-auto max-w-4xl space-y-6">
        @if($laporan->fotos->isNotEmpty())
            <section class="rounded-xl border border-gray-200 bg-white p-6 shadow-sm">
                <h2 class="mb-4 text-sm font-bold text-gray-800">Foto saat ini</h2>
                <div class="flex flex-wrap gap-3">
                    @foreach($laporan->fotos as $foto)
                        <img src="@mediaUrl($foto->file_path)" alt="Foto {{ $laporan->nama_barang }}" class="h-20 w-20 rounded-lg border border-gray-200 object-cover">
                    @endforeach
                </div>
            </section>
        @endif

        <form action="{{ route('laporan.update', $laporan) }}" method="POST" class="space-y-6 rounded-xl border border-gray-200 bg-white p-6 shadow-sm sm:p-8">
            @csrf
            @method('PUT')

            <div>
                <label for="nama_barang" class="mb-1.5 block text-sm font-semibold text-gray-700">Nama barang</label>
                <input id="nama_barang" name="nama_barang" value="{{ old('nama_barang', $laporan->nama_barang) }}" required maxlength="255" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">
                @error('nama_barang')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Kategori</label>
                    @php($kategoriOptions = $kategoris->pluck('nama', 'id')->all())
                    <x-custom-select name="kategori_id" :options="$kategoriOptions" placeholder="-- Pilih Kategori --" :selected="old('kategori_id', (string) $laporan->kategori_id)" :required="true" />
                    @error('kategori_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Warna dominan</label>
                    <input name="warna" value="{{ old('warna', $laporan->warna) }}" required maxlength="50" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">
                    @error('warna')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label class="mb-1.5 block text-sm font-semibold text-gray-700">Lokasi kampus</label>
                    @php($lokasiOptions = $lokasis->pluck('nama', 'id')->all())
                    <x-custom-select name="lokasi_id" :options="$lokasiOptions" placeholder="-- Pilih Lokasi --" :selected="old('lokasi_id', (string) $laporan->lokasi_id)" :required="true" />
                    @error('lokasi_id')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="detail_lokasi" class="mb-1.5 block text-sm font-semibold text-gray-700">Detail lokasi</label>
                    <input id="detail_lokasi" name="detail_lokasi" value="{{ old('detail_lokasi', $laporan->detail_lokasi) }}" maxlength="255" placeholder="Contoh: Kantin, meja dekat pintu" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">
                    @error('detail_lokasi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 gap-6 md:grid-cols-2">
                <div>
                    <label for="tanggal_kejadian" class="mb-1.5 block text-sm font-semibold text-gray-700">Tanggal kejadian</label>
                    <input id="tanggal_kejadian" type="date" name="tanggal_kejadian" value="{{ old('tanggal_kejadian', $laporan->tanggal_kejadian?->format('Y-m-d')) }}" max="{{ now()->toDateString() }}" required class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">
                    @error('tanggal_kejadian')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="waktu_kejadian" class="mb-1.5 block text-sm font-semibold text-gray-700">Perkiraan waktu</label>
                    <input id="waktu_kejadian" type="time" name="waktu_kejadian" value="{{ old('waktu_kejadian', $laporan->waktu_kejadian) }}" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">
                    @error('waktu_kejadian')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div>
                <label for="ciri_khusus" class="mb-1.5 block text-sm font-semibold text-gray-700">Ciri khusus</label>
                <textarea id="ciri_khusus" name="ciri_khusus" rows="3" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">{{ old('ciri_khusus', $laporan->ciri_khusus) }}</textarea>
                @error('ciri_khusus')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div>
                <label for="deskripsi" class="mb-1.5 block text-sm font-semibold text-gray-700">Deskripsi</label>
                <textarea id="deskripsi" name="deskripsi" rows="4" class="w-full rounded-lg border border-gray-200 bg-gray-50 px-4 py-2.5 text-sm focus:border-accent focus:outline-none focus:ring-4 focus:ring-accent/20">{{ old('deskripsi', $laporan->deskripsi) }}</textarea>
                @error('deskripsi')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="flex justify-end gap-3 border-t border-gray-100 pt-5">
                <a href="{{ route('laporan.show', $laporan) }}" class="rounded-lg border border-gray-300 px-5 py-2.5 text-sm font-semibold text-gray-700 hover:bg-gray-50">Batal</a>
                <button type="submit" class="rounded-lg bg-primary px-5 py-2.5 text-sm font-bold text-white hover:bg-primary-light">Simpan perubahan</button>
            </div>
        </form>
    </div>
</x-app-layout>
