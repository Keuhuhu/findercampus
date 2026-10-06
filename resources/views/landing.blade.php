<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>FinderCampus — Sistem Layanan Kehilangan Terpadu</title>
    
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-white text-gray-800">

    <!-- Navbar -->
    <nav class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex justify-between items-center h-16">
            <div class="flex items-center gap-2">
                <div class="w-8 h-8 bg-primary rounded-md flex items-center justify-center text-white font-bold text-lg shadow-sm">F</div>
                <span class="text-xl font-bold text-primary">FinderCampus</span>
            </div>
            <div class="flex items-center gap-3">
                <a href="/login" class="text-sm font-semibold text-gray-600 hover:text-primary transition px-4 py-2">Masuk</a>
                <a href="/register" class="text-sm font-semibold text-white bg-primary hover:bg-primary-light px-5 py-2.5 rounded-lg transition shadow-sm">Daftar Akun</a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative overflow-hidden bg-gradient-to-br from-primary via-primary-light to-accent py-24 sm:py-32">
        <div class="absolute inset-0 opacity-10">
            <div class="absolute top-10 left-10 w-72 h-72 bg-white rounded-full blur-3xl"></div>
            <div class="absolute bottom-10 right-10 w-96 h-96 bg-accent rounded-full blur-3xl"></div>
        </div>
        <div class="relative max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <div class="inline-flex items-center px-4 py-1.5 bg-white/15 backdrop-blur-sm rounded-full text-white/90 text-sm font-medium mb-6 border border-white/20">
                <span class="w-2 h-2 rounded-full bg-emerald-400 mr-2 animate-pulse"></span>
                Sistem SMART Aktif &bull; Multi-Attribute Rating Technique
            </div>
            <h1 class="text-4xl sm:text-5xl md:text-6xl font-extrabold text-white leading-tight mb-6">
                Kehilangan Barang<br>di Kampus?
            </h1>
            <p class="text-lg sm:text-xl text-blue-100 max-w-2xl mx-auto mb-10 leading-relaxed">
                <strong>FinderCampus</strong> membantu Anda menemukan kembali barang hilang melalui pencocokan otomatis berbasis algoritma <strong>SMART</strong> — cepat, transparan, dan terverifikasi.
            </p>
            <div class="flex flex-col sm:flex-row gap-4 justify-center">
                <a href="/register" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white text-primary font-bold rounded-xl shadow-lg hover:shadow-xl hover:scale-[1.02] transition text-lg">
                    Daftar Sekarang
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                </a>
                <a href="/login" class="inline-flex items-center justify-center gap-2 px-8 py-4 bg-white/10 backdrop-blur text-white font-bold rounded-xl border border-white/30 hover:bg-white/20 transition text-lg">
                    Masuk ke Akun
                </a>
            </div>
        </div>
    </section>

    <!-- Stats Section -->
    <section class="py-16 bg-white border-b border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 text-center">
                @php
                    $safeStats = $stats ?? [
                        'total_laporan' => \App\Models\Laporan::count(),
                        'barang_dikembalikan' => \App\Models\Laporan::where('status', 'selesai')->count(),
                        'user_aktif' => \App\Models\User::where('role', 'user')->count(),
                        'temuan_aktif' => \App\Models\Laporan::where('tipe', 'ditemukan')->where('status', 'aktif')->count(),
                    ];
                @endphp
                <div>
                    <div class="text-4xl font-extrabold text-primary mb-1">{{ $safeStats['total_laporan'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Total Laporan</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-emerald-600 mb-1">{{ $safeStats['barang_dikembalikan'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Barang Dikembalikan</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-accent mb-1">{{ $safeStats['user_aktif'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Pengguna Terdaftar</div>
                </div>
                <div>
                    <div class="text-4xl font-extrabold text-amber-500 mb-1">{{ $safeStats['temuan_aktif'] }}</div>
                    <div class="text-sm text-gray-500 font-medium">Temuan Aktif</div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="py-20 bg-background">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl font-extrabold text-gray-900 mb-3">Bagaimana FinderCampus Bekerja?</h2>
                <p class="text-gray-500 max-w-xl mx-auto">Tiga langkah mudah untuk menemukan kembali barang Anda yang hilang di kampus.</p>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <!-- Step 1 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center hover:shadow-md transition group">
                    <div class="w-16 h-16 bg-blue-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-blue-100 transition">
                        <svg class="w-8 h-8 text-accent" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">1</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Buat Laporan</h3>
                    <p class="text-sm text-gray-500">Laporkan barang hilang atau barang yang Anda temukan di kampus dengan detail lengkap — kategori, lokasi, warna, dan foto.</p>
                </div>
                <!-- Step 2 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center hover:shadow-md transition group">
                    <div class="w-16 h-16 bg-emerald-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-emerald-100 transition">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">2</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Pencocokan SMART</h3>
                    <p class="text-sm text-gray-500">Algoritma SMART menganalisis 5 kriteria (Kategori, Lokasi, Waktu, Ciri, Warna) dan memberi skor kecocokan secara transparan.</p>
                </div>
                <!-- Step 3 -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-200 p-8 text-center hover:shadow-md transition group">
                    <div class="w-16 h-16 bg-amber-50 rounded-2xl flex items-center justify-center mx-auto mb-5 group-hover:bg-amber-100 transition">
                        <svg class="w-8 h-8 text-amber-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="inline-flex items-center justify-center w-8 h-8 rounded-full bg-primary text-white text-sm font-bold mb-3">3</div>
                    <h3 class="text-lg font-bold text-gray-900 mb-2">Klaim & Ambil</h3>
                    <p class="text-sm text-gray-500">Ajukan klaim dengan bukti kepemilikan. Setelah diverifikasi admin, barang Anda siap diambil di posko kampus.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- SMART Info Section -->
    <section class="py-20 bg-white">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-gradient-to-r from-primary to-primary-light rounded-2xl p-8 sm:p-12 text-white">
                <div class="grid md:grid-cols-2 gap-10 items-center">
                    <div>
                        <h2 class="text-2xl sm:text-3xl font-extrabold mb-4">Algoritma SMART</h2>
                        <p class="text-blue-100 leading-relaxed mb-6">
                            Simple Multi-Attribute Rating Technique — metode pengambilan keputusan multi-kriteria yang mengubah pencarian barang hilang dari manual menjadi otomatis dan terukur.
                        </p>
                        <div class="space-y-3">
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="text-sm">Pencocokan otomatis 5 kriteria dengan bobot ilmiah</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="text-sm">Skor kecocokan transparan untuk setiap hasil</span>
                            </div>
                            <div class="flex items-center gap-3">
                                <div class="w-2 h-2 rounded-full bg-emerald-400"></div>
                                <span class="text-sm">Notifikasi radar otomatis ketika barang cocok ditemukan</span>
                            </div>
                        </div>
                    </div>
                    <div class="bg-white/10 backdrop-blur-sm rounded-xl p-6 border border-white/20">
                        <h3 class="text-lg font-bold mb-4">Bobot Kriteria</h3>
                        <div class="space-y-3">
                            <div>
                                <div class="flex justify-between text-sm mb-1"><span>Kategori Barang</span><span class="font-bold">25%</span></div>
                                <div class="w-full bg-white/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: 25%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1"><span>Lokasi Penemuan</span><span class="font-bold">25%</span></div>
                                <div class="w-full bg-white/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: 25%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1"><span>Waktu Kejadian</span><span class="font-bold">20%</span></div>
                                <div class="w-full bg-white/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: 20%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1"><span>Ciri-Ciri Barang</span><span class="font-bold">20%</span></div>
                                <div class="w-full bg-white/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: 20%"></div></div>
                            </div>
                            <div>
                                <div class="flex justify-between text-sm mb-1"><span>Warna</span><span class="font-bold">10%</span></div>
                                <div class="w-full bg-white/20 rounded-full h-2.5"><div class="bg-white h-2.5 rounded-full" style="width: 10%"></div></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA -->
    <section class="py-16 bg-background">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <h2 class="text-2xl sm:text-3xl font-extrabold text-gray-900 mb-4">Siap Menemukan Barang Anda?</h2>
            <p class="text-gray-500 mb-8">Bergabung dengan civitas kampus lainnya yang sudah menggunakan FinderCampus.</p>
            <a href="/register" class="inline-flex items-center gap-2 px-8 py-4 bg-primary text-white font-bold rounded-xl shadow-lg hover:bg-primary-light hover:shadow-xl transition text-lg">
                Mulai Sekarang — Gratis
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-900 text-gray-400 py-10">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center gap-4">
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 bg-accent rounded flex items-center justify-center text-white font-bold text-xs">F</div>
                <span class="text-sm font-semibold text-gray-300">FinderCampus</span>
                <span class="text-xs">&copy; {{ date('Y') }} Biro Kemahasiswaan</span>
            </div>
            <div class="flex gap-6 text-sm">
                <a href="/syarat-ketentuan" class="hover:text-white transition">Syarat & Ketentuan</a>
                <a href="/kebijakan-privasi" class="hover:text-white transition">Kebijakan Privasi</a>
                <a href="/panduan-smart" class="hover:text-white transition">Panduan SMART</a>
            </div>
        </div>
    </footer>
</body>
</html>
