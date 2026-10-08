<x-guest-layout>
    <div class="w-full sm:max-w-md">
        <div class="bg-white rounded-2xl shadow-xl border border-gray-100 p-8 w-full">
            <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-50 rounded-xl mb-4">
                <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                </svg>
            </div>
            <h2 class="text-2xl font-bold text-gray-900">Masuk ke FinderCampus</h2>
            <p class="text-sm text-gray-500 mt-2">Gunakan akun kampus Anda untuk mengakses portal layanan kehilangan.</p>
        </div>



        <form method="POST" action="/login">
            @csrf
            
            <div class="mb-4">
                <label for="login" class="block text-sm font-semibold text-gray-700 mb-1.5">Email Kampus atau NIM</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path>
                        </svg>
                    </div>
                    <input type="text" id="login" name="login" value="{{ old('login') }}" class="w-full pl-10 pr-4 py-2.5 bg-gray-50 border {{ $errors->has('login') ? 'border-red-300 ring-red-100' : 'border-gray-200 focus:border-amber-500 focus:ring-amber-500/20' }} rounded-lg text-sm focus:outline-none focus:ring-4 transition" placeholder="misal: nama@mhs.ac.id atau NIM" required autofocus>
                </div>
                @error('login')
                    <p class="mt-1 text-xs text-red-500">{{ $message }}</p>
                @enderror
            </div>

            <div class="mb-5">
                <div class="flex justify-between items-center mb-1.5">
                    <label for="password" class="block text-sm font-semibold text-gray-700">Kata Sandi</label>
                    <a href="/lupa-sandi" class="text-xs text-amber-500 hover:underline font-medium">Lupa kata sandi?</a>
                </div>
                <div class="relative" x-data="{ show: false }">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                        </svg>
                    </div>
                    <input :type="show ? 'text' : 'password'" id="password" name="password" class="w-full pl-10 pr-10 py-2.5 bg-gray-50 border {{ $errors->has('password') ? 'border-red-300 ring-red-100' : 'border-gray-200 focus:border-amber-500 focus:ring-amber-500/20' }} rounded-lg text-sm focus:outline-none focus:ring-4 transition" placeholder="Masukkan kata sandi" required>
                    <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 pr-3 flex items-center text-gray-400 hover:text-gray-600 focus:outline-none">
                        <svg x-show="!show" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                        <svg x-show="show" style="display: none;" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l18 18"></path></svg>
                    </button>
                </div>
            </div>

            <div class="mb-6 flex items-center">
                <input type="checkbox" id="remember" name="remember" class="w-4 h-4 text-amber-500 border-gray-300 rounded focus:ring-amber-500">
                <label for="remember" class="ml-2 text-sm text-gray-600">Ingat saya di perangkat ini</label>
            </div>

            <button type="submit" class="w-full py-3 px-4 bg-blue-600 hover:bg-blue-500 text-white font-semibold rounded-lg shadow-md transform hover:-translate-y-0.5 hover:shadow-lg active:scale-95 transition-all duration-200 flex items-center justify-center gap-2">
                Masuk
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </button>
        </form>

        <div class="mt-8 text-center text-sm text-gray-500">
            Belum memiliki akun? <a href="/register" class="text-blue-600 font-bold hover:underline">Daftar sekarang</a>
        </div>
    </div>
</x-guest-layout>

