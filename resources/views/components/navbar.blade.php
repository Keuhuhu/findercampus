<nav class="bg-white border-b border-gray-100 shadow-sm" x-data="{ open: false, notifOpen: false, profileOpen: false }" @click.outside="notifOpen = false; profileOpen = false; open = false">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center gap-2">
                    <a href="/dashboard" class="flex items-center gap-2">
                        <div class="w-8 h-8 bg-indigo-600 rounded-md flex items-center justify-center text-white font-bold text-lg">
                            F
                        </div>
                        <span class="text-xl font-bold text-indigo-600 hidden sm:block">FinderCampus</span>
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <a href="/dashboard" class="inline-flex items-center px-4 pt-1 border-b-2 {{ request()->is('dashboard') ? 'border-accent text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm transition duration-150 ease-in-out">
                        Dashboard
                    </a>
                    <a href="/laporan/buat" class="inline-flex items-center px-4 pt-1 border-b-2 {{ request()->is('laporan/buat') ? 'border-accent text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm transition duration-150 ease-in-out">
                        Lapor Barang
                    </a>
                    <a href="/pencarian" class="inline-flex items-center px-4 pt-1 border-b-2 {{ request()->is('pencarian') ? 'border-accent text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm transition duration-150 ease-in-out">
                        Pencarian
                    </a>
                    <a href="/status" class="inline-flex items-center px-4 pt-1 border-b-2 {{ request()->is('status') ? 'border-accent text-gray-900 font-semibold' : 'border-transparent text-gray-500 hover:text-gray-700 hover:border-gray-300' }} text-sm transition duration-150 ease-in-out">
                        Status & Klaim
                    </a>
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notifications Dropdown -->
                <div class="relative" @click.outside="notifOpen = false">
                    <button type="button" @click="notifOpen = !notifOpen" class="bell-btn p-2 text-gray-400 hover:text-gray-600 focus:outline-none transition relative">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        <!-- Unread Badge -->
                        <span class="badge-pulse absolute top-1 right-1 inline-flex items-center justify-center w-4 h-4 text-xs font-bold leading-none text-white bg-red-600 rounded-full"></span>
                    </button>

                    <div
                        x-show="notifOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                        style="display: none;"
                        class="absolute right-0 mt-2 w-80 bg-white rounded-xl shadow-xl py-1 z-50 ring-1 ring-black/5 origin-top-right"
                    >
                        <div class="px-4 py-2 border-b border-gray-100 flex justify-between items-center">
                            <span class="font-semibold text-sm text-gray-700">Notifikasi</span>
                            <a href="/notifikasi" class="text-xs text-accent hover:underline">Tandai semua dibaca</a>
                        </div>
                        <div class="max-h-60 overflow-y-auto divide-y divide-gray-50">
                            <a href="#" class="block px-4 py-3 hover:bg-gray-50 bg-blue-50/40 transition">
                                <p class="text-sm text-gray-800 font-medium">Barang yang mirip ditemukan!</p>
                                <p class="text-xs text-gray-500 mt-1">Sistem SMART mendeteksi kecocokan 94% dengan laporan kehilangan Anda.</p>
                                <p class="text-xs text-gray-400 mt-1 text-right">2 jam yang lalu</p>
                            </a>
                        </div>
                        <a href="/notifikasi" class="block px-4 py-2 text-sm text-center text-accent hover:bg-gray-50 font-medium transition">
                            Lihat semua notifikasi
                        </a>
                    </div>
                </div>

                <!-- Settings Dropdown -->
                <div class="relative" @click.outside="profileOpen = false">
                    <button type="button" @click="profileOpen = !profileOpen" class="avatar-btn flex items-center gap-2 focus:outline-none rounded-lg px-2 py-1 hover:bg-gray-50 transition">
                        @if(Auth::check() && Auth::user()->avatar)
                            <img class="h-8 w-8 rounded-full object-cover border-2 border-indigo-100 shadow-sm" src="{{ Storage::url(Auth::user()->avatar) }}" alt="Avatar" />
                        @else
                            <div class="h-8 w-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-bold border-2 border-indigo-200 shadow-sm">
                                {{ Auth::check() ? substr(Auth::user()->name, 0, 1) : 'U' }}
                            </div>
                        @endif
                        <div class="hidden md:flex flex-col items-start">
                            <span class="text-sm font-semibold text-gray-700 leading-tight">{{ Auth::check() ? explode(' ', Auth::user()->name)[0] : 'User' }}</span>
                        </div>
                        <svg class="h-4 w-4 text-gray-400 transition-transform duration-200" :class="profileOpen ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>

                    <div
                        x-show="profileOpen"
                        x-transition:enter="transition ease-out duration-200"
                        x-transition:enter-start="opacity-0 -translate-y-2 scale-95"
                        x-transition:enter-end="opacity-100 translate-y-0 scale-100"
                        x-transition:leave="transition ease-in duration-150"
                        x-transition:leave-start="opacity-100 translate-y-0 scale-100"
                        x-transition:leave-end="opacity-0 -translate-y-2 scale-95"
                        style="display: none;"
                        class="absolute right-0 mt-2 w-52 bg-white rounded-xl shadow-xl py-1 z-50 ring-1 ring-black/5 origin-top-right"
                    >
                        <div class="px-4 py-3 border-b border-gray-100 mb-1">
                            <p class="text-sm font-semibold text-gray-900 truncate">{{ Auth::check() ? Auth::user()->name : 'User' }}</p>
                            <p class="text-xs text-gray-500 truncate">{{ Auth::check() ? Auth::user()->email : '' }}</p>
                        </div>
                        <a href="/profil" class="flex items-center gap-2 px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 transition">
                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                            Profil Saya
                        </a>
                        @if(Auth::check() && Auth::user()->role === 'admin')
                            <a href="/admin/dashboard" class="flex items-center gap-2 px-4 py-2 text-sm text-accent font-medium hover:bg-amber-50 transition">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Admin Panel
                            </a>
                        @endif
                        <div class="border-t border-gray-100 mt-1">
                            <form method="POST" action="/logout">
                                @csrf
                                <button type="submit" class="flex items-center gap-2 w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                                    Keluar
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = !open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden bg-white border-b border-gray-200">
        <div class="pt-2 pb-3 space-y-1">
            <a href="/dashboard" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->is('dashboard') ? 'border-accent text-accent bg-blue-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                Dashboard
            </a>
            <a href="/laporan/buat" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->is('laporan/buat') ? 'border-accent text-accent bg-blue-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                Lapor Barang
            </a>
            <a href="/pencarian" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->is('pencarian') ? 'border-accent text-accent bg-blue-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                Pencarian
            </a>
            <a href="/status" class="block ps-3 pe-4 py-2 border-l-4 {{ request()->is('status') ? 'border-accent text-accent bg-blue-50' : 'border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300' }} text-base font-medium transition duration-150 ease-in-out">
                Status & Klaim
            </a>
            <a href="/notifikasi" class="block ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium transition duration-150 ease-in-out">
                Notifikasi
            </a>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::check() ? Auth::user()->name : 'User' }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::check() ? Auth::user()->email : '' }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <a href="/profil" class="block ps-3 pe-4 py-2 border-l-4 border-transparent text-gray-600 hover:text-gray-800 hover:bg-gray-50 hover:border-gray-300 text-base font-medium">
                    Profil Saya
                </a>
                @if(Auth::check() && Auth::user()->role === 'admin')
                    <a href="/admin/dashboard" class="block ps-3 pe-4 py-2 border-l-4 border-transparent text-accent hover:text-accent-hover hover:bg-gray-50 text-base font-medium">
                        Admin Panel
                    </a>
                @endif
                <form method="POST" action="/logout">
                    @csrf
                    <button type="submit" class="block w-full text-left ps-3 pe-4 py-2 border-l-4 border-transparent text-red-600 hover:bg-red-50 hover:border-red-300 text-base font-medium">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </div>
</nav>

