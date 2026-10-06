<x-guest-layout>
    <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full sm:w-[500px]">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900">Buat Akun Baru</h2>
            <p class="text-sm text-gray-500 mt-2">Daftarkan akun kampus Anda untuk akses layanan Lost & Found FinderCampus.</p>
        </div>

        <form method="POST" action="/register">
            @csrf
            
            <div class="mb-4">
                <label for="name" class="block text-sm font-semibold text-gray-700 mb-1.5">Nama Lengkap</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V5a2 2 0 114 0v1m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path></svg>
                    </div>
                    <input type="text" id="name" name="name" value="{{ old('name') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('name') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="Nama lengkap sesuai KTM / KTP" required>
                </div>
                @error('name')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="nim_nip" class="block text-sm font-semibold text-gray-700 mb-1.5">NIM / NIP</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <span class="text-gray-400 font-bold">#</span>
                        </div>
                        <input type="text" id="nim_nip" name="nim_nip" value="{{ old('nim_nip') }}" class="w-full pl-8 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('nim_nip') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="Nomor Induk" required>
                    </div>
                    @error('nim_nip')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="fakultas" class="block text-sm font-semibold text-gray-700 mb-1.5">Fakultas / Unit</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        </div>
                        <select id="fakultas" name="fakultas" class="w-full pl-10 pr-8 py-2.5 bg-gray-50 border {{ $errors->has('fakultas') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition appearance-none" required>
                            <option value="" disabled selected>Pilih Fakultas Anda</option>
                            <option value="Fasilkom">Fakultas Ilmu Komputer</option>
                            <option value="Fakultas Teknik">Fakultas Teknik</option>
                            <option value="Fakultas Kedokteran">Fakultas Kedokteran</option>
                            <option value="Fakultas Ekonomi">Fakultas Ekonomi</option>
                            <option value="Fakultas Hukum">Fakultas Hukum</option>
                            <option value="Lainnya">Lainnya / Unit Kerja</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <svg class="h-4 w-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                    @error('fakultas')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                <div>
                    <label for="email" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Kampus</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="email" id="email" name="email" value="{{ old('email') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('email') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="nama@campus.ac.id" required>
                    </div>
                    @error('email')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="whatsapp" class="block text-sm font-semibold text-gray-700 mb-1.5">No. WhatsApp</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        </div>
                        <input type="text" id="whatsapp" name="whatsapp" value="{{ old('whatsapp') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('whatsapp') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="08xxxxxxxxxx" required>
                    </div>
                    @error('whatsapp')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-6" x-data="{ show: false }">
                <div>
                    <label for="password" class="block text-sm font-semibold text-gray-700 mb-1.5">Kata Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" id="password" name="password" class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border {{ $errors->has('password') ? 'border-red-300' : 'border-gray-200 focus:border-amber-500' }} rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="Minimal 8 Karakter" required>
                    </div>
                    @error('password')<p class="mt-1 text-xs text-red-500">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700 mb-1.5">Konfirmasi Sandi</label>
                    <div class="relative">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                            <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </div>
                        <input :type="show ? 'text' : 'password'" id="password_confirmation" name="password_confirmation" class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border border-gray-200 focus:border-amber-500 rounded-lg text-sm focus:outline-none focus:ring-4 focus:ring-amber-500/20 transition" placeholder="Ulangi Kata Sandi" required>
                        <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                            <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                        </button>
                    </div>
                </div>
            </div>

            <div class="mb-6 flex items-start">
                <div class="flex items-center h-5">
                    <input type="checkbox" id="terms" name="terms" class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500" required>
                </div>
                <label for="terms" class="ml-2 text-sm text-gray-600">
                    Saya menyetujui <a href="/syarat-ketentuan" class="text-blue-600 font-bold hover:underline">Syarat & Ketentuan</a> serta <a href="/kebijakan-privasi" class="text-blue-600 font-bold hover:underline">Kebijakan Privasi</a> FinderCampus.
                </label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-md transform hover:-translate-y-0.5 hover:shadow-lg active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                Daftar Akun
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-6 text-center text-sm text-gray-500">
            Sudah memiliki akun? <a href="/login" class="text-amber-500 font-bold hover:underline">Masuk</a>
        </div>
    </div>
</x-guest-layout>

