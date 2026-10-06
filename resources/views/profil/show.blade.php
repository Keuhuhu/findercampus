<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Profil Saya' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Profil Saya</h1>
    </x-slot>

    @if(session('success'))
        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-xl text-sm font-medium flex items-center gap-2">
            <svg class="w-5 h-5 text-emerald-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- Left Column: Avatar & Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 text-center">
                <!-- Avatar -->
                <div class="relative inline-block mb-4">
                    @if($user->avatar)
                        <img src="{{ Storage::url($user->avatar) }}" alt="Avatar" class="w-24 h-24 rounded-full object-cover border-4 border-gray-100 shadow-sm">
                    @else
                        <div class="w-24 h-24 rounded-full bg-primary flex items-center justify-center text-white text-3xl font-bold border-4 border-gray-100 shadow-sm">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                    @endif
                    @if($user->is_verified)
                        <div class="absolute -bottom-1 -right-1 bg-accent rounded-full p-1">
                            <svg class="w-4 h-4 text-white" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                        </div>
                    @endif
                </div>

                <!-- Upload Avatar -->
                <form method="POST" action="/profil/avatar" enctype="multipart/form-data" class="mb-4">
                    @csrf
                    <label for="avatar-upload" class="cursor-pointer text-xs text-accent hover:underline font-medium">
                        📷 Ubah Foto Profil
                    </label>
                    <input type="file" id="avatar-upload" name="avatar" class="hidden" accept="image/*" onchange="this.form.submit()">
                </form>

                <h2 class="text-lg font-bold text-gray-900">{{ $user->name }}</h2>
                @if($user->is_verified)
                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-semibold bg-blue-50 text-accent border border-blue-100 mt-1">
                        ✅ Terverifikasi {{ $user->fakultas }}
                    </span>
                @endif

                <div class="mt-6 space-y-3 text-left text-sm">
                    <div class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207"></path></svg>
                        <span>{{ $user->email }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <span class="text-gray-400 font-bold w-5 text-center shrink-0">#</span>
                        <span>{{ $user->nim_nip ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path></svg>
                        <span>{{ $user->fakultas ?? '-' }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-gray-600">
                        <svg class="w-5 h-5 text-gray-400 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 18h.01M8 21h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                        <span>{{ $user->whatsapp ?? '-' }}</span>
                    </div>
                </div>

                <!-- WhatsApp Visibility Toggle -->
                <div class="mt-6 pt-6 border-t border-gray-100">
                    <form method="POST" action="/profil/toggle-whatsapp">
                        @csrf
                        <div class="flex items-center justify-between">
                            <div class="text-left">
                                <p class="text-sm font-semibold text-gray-700">Tampilkan WhatsApp</p>
                                <p class="text-xs text-gray-500">Izinkan pengklaim menghubungi Anda via WA</p>
                            </div>
                            <button type="submit" class="relative inline-flex h-6 w-11 items-center rounded-full transition-colors {{ $user->whatsapp_visible ? 'bg-accent' : 'bg-gray-200' }}">
                                <span class="inline-block h-4 w-4 transform rounded-full bg-white shadow-sm transition-transform {{ $user->whatsapp_visible ? 'translate-x-6' : 'translate-x-1' }}"></span>
                            </button>
                        </div>
                    </form>
                </div>

                <a href="/profil/edit" class="mt-6 w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 bg-primary text-white font-semibold text-sm rounded-lg hover:bg-primary-light transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    Edit Profil
                </a>
            </div>
        </div>

        <!-- Right Column: Stats & Activity -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Stats Cards -->
            <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-extrabold text-primary mb-1">{{ $stats['total_laporan'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Total Laporan</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-extrabold text-red-500 mb-1">{{ $stats['laporan_hilang'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Barang Hilang</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-extrabold text-accent mb-1">{{ $stats['laporan_ditemukan'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Barang Ditemukan</div>
                </div>
                <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-5 text-center">
                    <div class="text-3xl font-extrabold text-emerald-600 mb-1">{{ $stats['barang_dikembalikan'] }}</div>
                    <div class="text-xs text-gray-500 font-medium">Dikembalikan</div>
                </div>
            </div>

            <!-- Info Card -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Informasi Akun</h3>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-4">
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Nama Lengkap</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->name }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">NIM / NIP</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->nim_nip ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Email Kampus</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->email }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Fakultas / Unit</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->fakultas ?? '-' }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">No. WhatsApp</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold flex items-center gap-2">
                            {{ $user->whatsapp ?? '-' }}
                            @if($user->whatsapp_visible)
                                <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded-full">Publik</span>
                            @else
                                <span class="text-xs text-gray-500 bg-gray-100 px-2 py-0.5 rounded-full">Tersembunyi</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Status Verifikasi</dt>
                        <dd class="mt-1 text-sm font-semibold">
                            @if($user->is_verified)
                                <span class="text-emerald-600">✅ Terverifikasi</span>
                            @else
                                <span class="text-amber-600">⏳ Menunggu Verifikasi</span>
                            @endif
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Bergabung Sejak</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ $user->created_at->translatedFormat('d F Y') }}</dd>
                    </div>
                    <div>
                        <dt class="text-xs text-gray-500 font-medium uppercase tracking-wider">Role</dt>
                        <dd class="mt-1 text-sm text-gray-900 font-semibold">{{ ucfirst($user->role) }}</dd>
                    </div>
                </dl>
            </div>
        </div>
    </div>
</x-app-layout>
