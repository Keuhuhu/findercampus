<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Panduan SMART' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Panduan Algoritma SMART</h1>
    </x-slot>

    <div class="max-w-4xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-10 prose prose-sm prose-gray max-w-none">
            <div class="not-prose bg-gradient-to-r from-primary to-primary-light text-white rounded-xl p-6 mb-8">
                <h2 class="text-xl font-bold mb-2">Simple Multi-Attribute Rating Technique</h2>
                <p class="text-blue-100 text-sm">Metode pengambilan keputusan multi-kriteria yang digunakan FinderCampus untuk mencocokkan barang hilang dengan barang temuan secara otomatis dan transparan.</p>
            </div>

            <h2>Apa itu SMART?</h2>
            <p>SMART (Simple Multi-Attribute Rating Technique) adalah metode pengambilan keputusan yang mengubah pencarian barang hilang dari proses manual menjadi otomatis dan terukur. Sistem ini menganalisis beberapa kriteria sekaligus dengan bobot yang telah ditentukan secara ilmiah.</p>

            <h2>5 Kriteria Pencocokan</h2>

            <div class="not-prose grid grid-cols-1 sm:grid-cols-2 gap-4 my-6">
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-primary">25%</span>
                        <span class="font-bold text-gray-900">Kategori Barang</span>
                    </div>
                    <p class="text-xs text-gray-600">Apakah jenis barang sama? (Elektronik, Dokumen, Kunci, dll)</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-primary">25%</span>
                        <span class="font-bold text-gray-900">Lokasi Penemuan</span>
                    </div>
                    <p class="text-xs text-gray-600">Seberapa dekat lokasi hilang dengan lokasi ditemukan?</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-primary">20%</span>
                        <span class="font-bold text-gray-900">Waktu Kejadian</span>
                    </div>
                    <p class="text-xs text-gray-600">Seberapa dekat waktu kehilangan dengan waktu penemuan?</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-primary">20%</span>
                        <span class="font-bold text-gray-900">Ciri-Ciri Barang</span>
                    </div>
                    <p class="text-xs text-gray-600">Kesesuaian ciri khusus (stiker, goresan, merk, model)</p>
                </div>
                <div class="bg-blue-50 rounded-lg p-4 border border-blue-100 sm:col-span-2 sm:max-w-xs sm:mx-auto">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="text-2xl font-extrabold text-primary">10%</span>
                        <span class="font-bold text-gray-900">Warna</span>
                    </div>
                    <p class="text-xs text-gray-600">Apakah warna barang sama atau serumpun?</p>
                </div>
            </div>

            <h2>Bagaimana Skor Dihitung?</h2>

            <h3>Langkah 1: Normalisasi Bobot</h3>
            <p>Setiap kriteria memiliki bobot (<em>w<sub>j</sub></em>) yang sudah dinormalisasi sehingga total = 100%:</p>
            <div class="not-prose bg-gray-50 rounded-lg p-4 font-mono text-sm text-center border border-gray-200 my-4">
                W<sub>j</sub> = w<sub>j</sub> / Σw<sub>j</sub>
            </div>

            <h3>Langkah 2: Penilaian Utilitas</h3>
            <p>Setiap kriteria diberi nilai utilitas (<em>U<sub>ij</sub></em>) dari 0.00 hingga 1.00:</p>
            <div class="not-prose overflow-x-auto my-4">
                <table class="min-w-full text-sm border border-gray-200 rounded-lg overflow-hidden">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="px-4 py-2 text-left">Nilai</th>
                            <th class="px-4 py-2 text-left">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        <tr class="bg-emerald-50"><td class="px-4 py-2 font-bold">1.00</td><td class="px-4 py-2">Sangat Sesuai</td></tr>
                        <tr><td class="px-4 py-2 font-bold">0.75</td><td class="px-4 py-2">Sesuai</td></tr>
                        <tr class="bg-gray-50"><td class="px-4 py-2 font-bold">0.50</td><td class="px-4 py-2">Cukup Sesuai</td></tr>
                        <tr><td class="px-4 py-2 font-bold">0.25</td><td class="px-4 py-2">Kurang Sesuai</td></tr>
                        <tr class="bg-red-50"><td class="px-4 py-2 font-bold">0.00</td><td class="px-4 py-2">Tidak Sesuai</td></tr>
                    </tbody>
                </table>
            </div>

            <h3>Langkah 3: Hitung Skor Akhir</h3>
            <p>Skor akhir SMART dihitung dengan rumus:</p>
            <div class="not-prose bg-primary/5 rounded-lg p-4 font-mono text-center text-sm border border-primary/20 my-4">
                U<sub>i</sub> = Σ (W<sub>j</sub> × U<sub>ij</sub>)
            </div>
            <p>Skor akhir (U<sub>i</sub>) berkisar antara <strong>0 — 100</strong>. Semakin tinggi skor, semakin cocok barang temuan dengan laporan kehilangan.</p>

            <h2>Interpretasi Skor</h2>
            <div class="not-prose flex flex-col gap-2 my-4">
                <div class="flex items-center gap-3 bg-emerald-50 rounded-lg px-4 py-2 border border-emerald-200">
                    <span class="font-extrabold text-emerald-700">≥ 80%</span>
                    <span class="text-sm text-emerald-800 font-medium">🟢 Sangat Cocok — Kemungkinan besar barang yang sama</span>
                </div>
                <div class="flex items-center gap-3 bg-amber-50 rounded-lg px-4 py-2 border border-amber-200">
                    <span class="font-extrabold text-amber-700">60 – 79%</span>
                    <span class="text-sm text-amber-800 font-medium">🟡 Kecocokan Sedang — Perlu diperiksa lebih lanjut</span>
                </div>
                <div class="flex items-center gap-3 bg-red-50 rounded-lg px-4 py-2 border border-red-200">
                    <span class="font-extrabold text-red-700">< 60%</span>
                    <span class="text-sm text-red-800 font-medium">🔴 Kurang Cocok — Kemungkinan barang berbeda</span>
                </div>
            </div>

            <h2>Tips Agar Pencocokan Lebih Akurat</h2>
            <ul>
                <li>Isi <strong>semua field</strong> saat membuat laporan, terutama ciri khusus dan warna.</li>
                <li>Unggah <strong>foto yang jelas</strong> dari berbagai sudut.</li>
                <li>Tuliskan <strong>ciri unik</strong> seperti stiker, goresan, ukiran nama, nomor seri, dll.</li>
                <li>Pilih <strong>lokasi yang tepat</strong> — semakin spesifik, semakin akurat.</li>
                <li>Aktifkan <strong>Radar Notifikasi</strong> agar sistem memberitahu Anda saat ada barang baru yang cocok.</li>
            </ul>
        </div>
    </div>
</x-app-layout>
