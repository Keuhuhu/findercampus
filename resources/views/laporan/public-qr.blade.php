<x-app-layout>
    <div class="mx-auto max-w-3xl overflow-hidden rounded-2xl border border-gray-200 bg-white shadow-sm">
        <div class="border-b border-emerald-100 bg-emerald-50 px-6 py-5 sm:px-8">
            <p class="text-sm font-semibold text-emerald-800">Informasi barang temuan</p>
            <p class="mt-1 text-sm text-emerald-700">Halaman ini menampilkan informasi barang yang bisa dibagikan secara umum.</p>
        </div>

        @if($laporan->fotos->isNotEmpty())
            <div class="grid grid-cols-2 gap-2 bg-gray-100 p-3 sm:grid-cols-3 sm:gap-3 sm:p-4">
                @foreach($laporan->fotos as $foto)
                    <img
                        src="@mediaUrl($foto->file_path)"
                        alt="Foto barang temuan {{ $loop->iteration }}"
                        class="aspect-square w-full rounded-xl bg-white object-cover"
                        @if($loop->iteration > 3) loading="lazy" @endif
                    >
                @endforeach
            </div>
        @endif

        <div class="p-6 sm:p-8">
            <div class="flex flex-wrap items-center gap-2">
                <span class="rounded-full bg-emerald-100 px-3 py-1 text-xs font-semibold text-emerald-800">Barang ditemukan</span>
                <span class="rounded-full bg-gray-100 px-3 py-1 text-xs font-semibold text-gray-600">{{ $laporan->kode_laporan }}</span>
            </div>

            <h1 class="mt-4 text-2xl font-bold text-gray-900">{{ $laporan->nama_barang }}</h1>
            <p class="mt-1 text-sm text-gray-600">{{ $laporan->kategori->nama }}</p>

            <dl class="mt-6 grid gap-5 border-t border-gray-100 pt-6 sm:grid-cols-2">
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Status</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">Aktif</dd>
                </div>
                <div>
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Tanggal ditemukan</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $laporan->tanggal_kejadian->translatedFormat('d F Y') }}</dd>
                </div>
                <div class="sm:col-span-2">
                    <dt class="text-xs font-semibold uppercase tracking-wide text-gray-500">Lokasi kampus</dt>
                    <dd class="mt-1 text-sm font-medium text-gray-900">{{ $laporan->lokasi->nama }}</dd>
                </div>
            </dl>

            <div class="mt-7 rounded-xl bg-blue-50 p-4 text-sm leading-6 text-blue-900">
                Merasa barang ini milik Anda? Masuk ke akun FinderCampus untuk mengajukan klaim kepemilikan.
            </div>
            <a href="{{ route('klaim.create', $laporan) }}" class="mt-4 inline-flex w-full items-center justify-center rounded-xl bg-primary px-5 py-3 text-sm font-semibold text-white transition hover:bg-primary-light focus:outline-none focus:ring-4 focus:ring-blue-200">
                Masuk untuk mengajukan klaim
            </a>
        </div>
    </div>
</x-app-layout>
