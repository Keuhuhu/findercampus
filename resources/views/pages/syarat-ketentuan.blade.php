<x-app-layout>
    <x-slot name="breadcrumb">
        <x-breadcrumb :breadcrumbs="['Syarat & Ketentuan' => '#']" />
    </x-slot>

    <x-slot name="header">
        <h1 class="text-2xl font-bold text-gray-900">Syarat & Ketentuan Layanan</h1>
    </x-slot>

    <div class="max-w-3xl">
        <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-6 sm:p-10 prose prose-sm prose-gray max-w-none">
            <p class="text-gray-500 text-sm mb-6">Terakhir diperbarui: {{ date('d F Y') }}</p>

            <h2>1. Ketentuan Umum</h2>
            <p>FinderCampus adalah sistem informasi manajemen Lost & Found terpusat yang dikelola oleh Biro Kemahasiswaan dan Sistem Informasi kampus. Dengan menggunakan layanan ini, Anda menyetujui untuk mematuhi seluruh syarat dan ketentuan berikut.</p>

            <h2>2. Pendaftaran Akun</h2>
            <ul>
                <li>Pengguna wajib mendaftar menggunakan identitas asli (Nama, NIM/NIP, Email Kampus).</li>
                <li>Setiap pengguna hanya diperkenankan memiliki satu akun.</li>
                <li>Akun yang terverifikasi memiliki akses penuh ke seluruh fitur layanan.</li>
                <li>Penyalahgunaan akun dapat berakibat pemblokiran permanen.</li>
            </ul>

            <h2>3. Pelaporan Barang</h2>
            <ul>
                <li>Pelapor bertanggung jawab atas keakuratan informasi yang diberikan.</li>
                <li>Foto yang diunggah harus merupakan foto asli dari barang yang dimaksud.</li>
                <li>Laporan palsu atau menyesatkan akan ditindak sesuai peraturan kampus.</li>
                <li>Laporan yang tidak aktif selama 30 hari akan otomatis kedaluwarsa.</li>
            </ul>

            <h2>4. Klaim & Pengembalian</h2>
            <ul>
                <li>Pengklaim wajib menyertakan bukti kepemilikan yang valid.</li>
                <li>Verifikasi klaim dilakukan oleh admin/petugas posko yang berwenang.</li>
                <li>Barang hanya akan diserahkan setelah proses verifikasi selesai.</li>
                <li>FinderCampus tidak bertanggung jawab atas klaim yang disetujui berdasarkan bukti palsu.</li>
            </ul>

            <h2>5. Algoritma SMART</h2>
            <p>Sistem pencocokan menggunakan algoritma Simple Multi-Attribute Rating Technique (SMART) sebagai alat bantu keputusan. Hasil pencocokan bersifat rekomendasi dan bukan keputusan final — verifikasi manusia tetap diperlukan.</p>

            <h2>6. Privasi Data</h2>
            <p>Data pribadi pengguna dilindungi sesuai <a href="/kebijakan-privasi" class="text-accent hover:underline">Kebijakan Privasi</a> FinderCampus. Nomor WhatsApp hanya ditampilkan jika pengguna mengaktifkan opsi tersebut di pengaturan profil.</p>

            <h2>7. Perubahan Ketentuan</h2>
            <p>FinderCampus berhak mengubah syarat dan ketentuan ini sewaktu-waktu. Pengguna akan diberitahu melalui notifikasi sistem apabila terjadi perubahan signifikan.</p>
        </div>
    </div>
</x-app-layout>
