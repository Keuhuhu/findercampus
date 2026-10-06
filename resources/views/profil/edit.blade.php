<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Profil Saya' => '/profil', 'Edit Profil' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Edit Profil</h1>
    </x-slot>

    <div class="max-w-2xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-8">
            <form method="POST" action="/profil">
                @csrf
                @method('PUT')

                <!-- Nama -->
                <div class="mb-5">
                    <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                    <input type="text" id="name" name="name" value="{{ old('name', $user->name) }}" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" required>
                    @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <!-- NIM (Read-only) -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">NIM / NIP</label>
                    <input type="text" value="{{ $user->nim_nip }}" class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" disabled>
                    <p class="text-xs text-gray-500 mt-1">NIM/NIP tidak dapat diubah. Hubungi admin untuk perubahan.</p>
                </div>

                <!-- Email (Read-only) -->
                <div class="mb-5">
                    <label class="block text-sm font-semibold text-gray-700 mb-1.5">Email Kampus</label>
                    <input type="text" value="{{ $user->email }}" class="w-full px-4 py-2.5 bg-gray-100 border border-gray-200 rounded-lg text-sm text-gray-500 cursor-not-allowed" disabled>
                </div>

                <!-- WhatsApp -->
                <div class="mb-5">
                    <label for="whatsapp" class="block text-sm font-semibold text-gray-700 mb-1.5">No. WhatsApp</label>
                    <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp', $user->whatsapp) }}" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('whatsapp') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" placeholder="08xxxxxxxxxx" required>
                    @error('whatsapp')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>

                <hr class="my-6 border-gray-200">

                <!-- Ganti Password (Opsional) -->
                <div class="mb-1">
                    <h3 class="text-base font-bold text-gray-900 mb-1">Ganti Kata Sandi</h3>
                    <p class="text-xs text-gray-500 mb-4">Kosongkan jika tidak ingin mengubah kata sandi.</p>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div>
                        <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi Baru</label>
                        <input type="password" id="password" name="password" class="w-full px-4 py-2.5 bg-gray-50 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200 focus:border-accent' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" placeholder="Minimal 8 karakter">
                        @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                        <input type="password" id="password_confirmation" name="password_confirmation" class="w-full px-4 py-2.5 bg-gray-50 border border-gray-200 focus:border-accent rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-accent/20 transition" placeholder="Ulangi kata sandi">
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                    <a href="/profil" class="text-sm text-gray-500 hover:text-gray-700 transition">← Kembali ke Profil</a>
                    <button type="submit" class="inline-flex items-center gap-2 px-6 py-2.5 bg-primary text-white font-semibold text-sm rounded-lg hover:bg-primary-light transition shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
