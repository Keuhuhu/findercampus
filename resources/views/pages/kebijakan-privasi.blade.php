<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Kebijakan Privasi' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Kebijakan Privasi</h1>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-10 prose prose-sm prose-gray max-w-none">
            <p class="text-gray-500 text-sm mb-6">Terakhir diperbarui: {{ date('d F Y') }}</p>

            <h2>1. Data yang Kami Kumpulkan</h2>
            <p>FinderCampus mengumpulkan data berikut saat Anda mendaftar dan menggunakan layanan:</p>
            <ul>
                <li><strong>Identitas:</strong> Nama lengkap, NIM/NIP, Fakultas/Unit</li>
                <li><strong>Kontak:</strong> Email kampus, nomor WhatsApp</li>
                <li><strong>Laporan:</strong> Detail barang (nama, kategori, warna, ciri, lokasi, foto)</li>
                <li><strong>Aktivitas:</strong> Log aktivitas pelaporan, klaim, dan pencarian</li>
            </ul>

            <h2>2. Penggunaan Data</h2>
            <p>Data Anda digunakan untuk:</p>
            <ul>
                <li>Memproses dan mencocokkan laporan barang hilang/ditemukan</li>
                <li>Memfasilitasi komunikasi antara pelapor dan pengklaim</li>
                <li>Verifikasi identitas dan kepemilikan barang</li>
                <li>Menghasilkan statistik anonim untuk peningkatan layanan</li>
            </ul>

            <h2>3. Visibilitas WhatsApp</h2>
            <p>Secara default, nomor WhatsApp Anda <strong>tidak ditampilkan</strong> kepada pengguna lain. Anda dapat mengaktifkan visibilitas WhatsApp melalui halaman <a href="/profil" class="text-accent hover:underline">Profil</a> dengan toggle "Tampilkan WhatsApp". Pengaturan ini dapat diubah kapan saja.</p>

            <h2>4. Penyimpanan & Keamanan</h2>
            <ul>
                <li>Data disimpan di server kampus yang diamankan dengan enkripsi standar industri.</li>
                <li>Kata sandi disimpan dalam bentuk hash (tidak dapat dibaca).</li>
                <li>Akses ke data administratif dibatasi hanya untuk petugas yang berwenang.</li>
            </ul>

            <h2>5. Hak Pengguna</h2>
            <ul>
                <li>Anda berhak mengakses, memperbarui, dan menghapus data pribadi Anda.</li>
                <li>Anda berhak menonaktifkan akun dengan menghubungi admin.</li>
                <li>Laporan yang dihapus akan disimpan dalam arsip selama 90 hari sebelum dihapus permanen.</li>
            </ul>

            <h2>6. Kontak</h2>
            <p>Untuk pertanyaan terkait privasi data, hubungi: <strong>lostfound@campus.ac.id</strong></p>
        </div>
    </div>
</x-app-layout>
