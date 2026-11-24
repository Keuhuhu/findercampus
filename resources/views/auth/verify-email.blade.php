<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Verifikasi Email' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Verifikasi Email</h1>
    </x-slot>

    <div class="mx-auto max-w-2xl rounded-2xl border border-blue-100 bg-white p-6 shadow-sm sm:p-8">
        <div class="mb-5 flex h-12 w-12 items-center justify-center rounded-xl bg-blue-50 text-blue-700">
            <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M3 8l9 6 9-6M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
            </svg>
        </div>

        <h2 class="text-xl font-bold text-gray-900">Satu langkah lagi untuk mengaktifkan akun</h2>
        <p class="mt-3 text-sm leading-6 text-gray-600">
            Kami mengirim tautan verifikasi ke <strong class="text-gray-800">{{ Auth::user()->email }}</strong>.
            Buka email tersebut lalu klik tombol verifikasi. Tautan berlaku selama {{ config('auth.verification.expire', 60) }} menit.
        </p>
        <p class="mt-2 text-sm font-medium text-amber-700">Jangan lupa periksa folder Spam atau Junk jika email belum terlihat di kotak masuk.</p>

        @if($verificationLinkExpired ?? false)
            <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4 text-sm text-amber-800" role="alert">
                Tautan verifikasi sudah kedaluwarsa atau tidak valid. Kirim tautan baru di bawah ini.
            </div>
        @endif

        @if(session('status') === 'verification-link-sent')
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
                Tautan verifikasi baru sudah dikirim ke email Anda.
            </div>
        @endif

        @if(session('status') === 'registration-verification-sent')
            <div class="mt-5 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800" role="status">
                Email verifikasi untuk mengaktifkan akun sudah dikirim ke alamat email Anda.
            </div>
        @endif

        @if(session('verification-mail-error'))
            <div class="mt-5 rounded-xl border border-red-200 bg-red-50 p-4 text-sm text-red-800" role="alert">
                {{ session('verification-mail-error') }}
            </div>
        @endif

        <div class="mt-6 flex flex-col gap-3 sm:flex-row sm:items-center">
            <form method="POST" action="{{ route('verification.send') }}">
                @csrf
                <button type="submit" class="inline-flex w-full items-center justify-center rounded-lg bg-blue-700 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:-translate-y-0.5 hover:bg-blue-800 hover:shadow-md focus:outline-none focus:ring-4 focus:ring-blue-200 sm:w-auto">
                    Kirim Ulang Email Verifikasi
                </button>
            </form>
            <a href="{{ route('profil.edit') }}" class="inline-flex items-center justify-center rounded-lg border border-gray-200 px-5 py-3 text-sm font-semibold text-gray-700 transition hover:border-gray-300 hover:bg-gray-50">
                Periksa Alamat Email
            </a>
        </div>
    </div>
</x-app-layout>
