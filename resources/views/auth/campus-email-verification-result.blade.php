<x-guest-layout>
    <div class="w-full max-w-lg rounded-2xl border border-gray-200 bg-white p-7 text-center shadow-sm sm:p-9">
        @if($verified)
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-emerald-50 text-emerald-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m5 12 4 4L19 6" />
                </svg>
            </div>
            <h1 class="mt-5 text-xl font-bold text-gray-900">Email Unsri berhasil diverifikasi</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Lencana Email Unsri Terverifikasi sudah aktif. Anda boleh menutup tab ini dan kembali ke FinderCampus.</p>
        @else
            <div class="mx-auto flex h-14 w-14 items-center justify-center rounded-full bg-amber-50 text-amber-600">
                <svg class="h-7 w-7" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 4h.01M10.3 3.86 1.82 18a2 2 0 0 0 1.72 3h16.92a2 2 0 0 0 1.72-3L13.7 3.86a2 2 0 0 0-3.4 0Z" />
                </svg>
            </div>
            <h1 class="mt-5 text-xl font-bold text-gray-900">Tautan verifikasi kampus tidak valid atau kedaluwarsa</h1>
            <p class="mt-3 text-sm leading-6 text-gray-600">Masuk ke FinderCampus untuk memeriksa status profil dan mengirim tautan baru.</p>
            <a href="{{ route('login') }}" class="mt-6 inline-flex items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white transition hover:bg-blue-800 focus:outline-none focus:ring-4 focus:ring-blue-200">Masuk ke FinderCampus</a>
        @endif
    </div>
</x-guest-layout>
