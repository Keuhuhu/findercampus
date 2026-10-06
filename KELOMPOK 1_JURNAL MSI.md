## Sistem Informasi Manajemen Lost and Found Terpusat Berbasis Web untuk Pelaporan Barang Hilang di Area Kampus Menggunakan Metode SMART

Amallya Salsabilla Harahap1, M.Rudy Sanjaya2, Kenza Azura Gozali3 , M.Bayu Nur Rizki4, Sultan

Maliq Fisabilillah5 , Andika Ahmad Muzaki6

1,2 Universitas Sriwijaya [URL 🔗](mailto:1penulis_pertama@afiliasi.xx.xx)

e-mail: 1amallyasalsabila076@gmail.com, 2m.rudi.sjy@ilkom.unsri.ac.id, 3kenza.gozali16@gmail.com, [URL 🔗](mailto:amallyasalsabila076@gmail.com)

4 baylilac14@gmail.com, 5sultanmaliqvmod@gmail.com, 6kenza.gozali16@gmail.com [URL 🔗](mailto:sultanmaliqvmod@gmail.com)

Diterima: xy Juli 2026; Direvisi: xy September 2026; Disetujui: xy November 2026

Permasalahan kehilangan dan penemuan barang di lingkungan kampus masih sering ditangani secara tidak terpusat, seperti melalui grup percakapan dan media sosial, sehingga informasi mudah tertimbun, sulit ditelusuri, serta berpotensi menyebabkan penumpukan barang yang belum teridentifikasi. Penelitian ini bertujuan mengembangkan Sistem Informasi Manajemen Lost and Found terpusat berbasis web bernama FinderCampus untuk mempermudah proses pelaporan, pencarian, pencocokan, klaim, dan verifikasi kepemilikan barang di lingkungan kampus. Sistem dikembangkan dengan menerapkan metode Simple Multi Attribute Rating Technique (SMART) sebagai metode pengambilan keputusan dalam menentukan tingkat kesesuaian antara laporan barang hilang dan barang ditemukan berdasarkan beberapa kriteria yang telah ditentukan. Sistem menyediakan fitur pelaporan barang hilang dan ditemukan, unggah foto, kategori, lokasi, pencarian dan filter data, serta proses verifikasi klaim oleh admin. Hasil pengembangan menunjukkan bahwa FinderCampus dapat menyediakan pengelolaan data barang hilang dan ditemukan secara lebih terstruktur serta membantu proses pencarian dan pencocokan barang berdasarkan kriteria yang digunakan. Dengan adanya sistem terpusat ini, proses pengelolaan barang hilang di lingkungan kampus diharapkan menjadi lebih efektif, transparan, dan terdokumentasi.

Kata kunci: Lost and Found, Sistem Informasi, Berbasis Web, Pelaporan Barang Hilang, Kampus, SMART.

## Abstrak

## I. PENDAHULUAN

Perkembangan teknologi informasi mendorong pengelolaan informasi dari sistem konvensional

menuju sistem digital yang lebih terintegrasi. Salah satu permasalahan yang dapat ditangani adalah pengelolaan barang hilang dan ditemukan (lost and found) di lingkungan kampus. Tingginya mobilitas mahasiswa, dosen, tenaga kependidikan, dan pengunjung menyebabkan kehilangan barang menjadi permasalahan yang membutuhkan proses pelaporan, pencarian, pencocokan, serta pengembalian yang terstruktur. Penggunaan sistem digital dapat menjadi solusi karena informasi barang dapat disimpan dan diakses dalam satu platform secara lebih mudah [1][2].

Permasalahan lost and found di lingkungan kampus telah ditunjukkan dalam beberapa penelitian.

Pada Kampus 3 Universitas Muhammadiyah Malang yang memiliki sekitar 33.744 mahasiswa aktif, tercatat barang temuan yang belum diambil berupa 38 STNK, 83 KTM, 2 KTP, dan 2 SIM [7]. Penelitian tersebut mengembangkan sistem pencarian barang hilang berbasis web menggunakan Cosine Similarity dan melibatkan 100 responden, dengan tingkat penerimaan sistem sebesar 77,8% [7]. Sementara itu, penelitian di Universitas Udayana menunjukkan bahwa penyebaran informasi barang hilang melalui media sosial menyebabkan informasi tidak terpusat sehingga dikembangkan sistem berbasis web untuk mendukung proses pencarian dan pelaporan barang [8].

Berbagai metode telah digunakan untuk meningkatkan efektivitas sistem Lost & Found. Penelitian

berbasis Android menerapkan Term Frequency-Inverse Document Frequency (TF-IDF) dan Cosine Similarity untuk mengukur kemiripan informasi barang hilang dan ditemukan [1]. Penelitian lainnya


menggunakan String Matching pada sistem UNIMED Lost & Found untuk melakukan pencocokan antara data laporan kehilangan dan barang temuan [10]. Selain itu, pendekatan Design Thinking digunakan dalam perancangan aplikasi Lost and Found di lingkungan kampus melalui tahapan Empathize, Define, Ideate, Prototype, dan Testing [3], sedangkan penelitian di Kampus UMI menggunakan User Centered Design (UCD) dengan menempatkan kebutuhan pengguna sebagai dasar perancangan aplikasi [9].

Pengembangan sistem berbasis web juga telah diterapkan pada lingkungan perguruan tinggi maupun

organisasi lainnya. Penelitian di Karangturi National University menghasilkan sistem Lost & Found berbasis web untuk mendukung pengelolaan laporan barang hilang dan ditemukan secara lebih terstruktur [4]. Pada sektor transportasi, sistem Lost and Found berbasis web juga dikembangkan untuk PT Kereta Commuter Indonesia guna mendukung pencatatan dan pengelolaan barang temuan secara terintegrasi [5]. Penelitian lain mengembangkan aplikasi Lost & Found berbasis real-time menggunakan Jetpack Compose dan Firebase, sehingga informasi barang hilang dan ditemukan dapat diperbarui dan diakses secara cepat [6].

Berdasarkan penelitian terdahulu, sistem Lost & Found telah dikembangkan dengan berbagai

pendekatan, seperti TF-IDF dan Cosine Similarity [1], backend berbasis web [2], Design Thinking [3], sistem berbasis web [4], sistem informasi pada PT KCI [5], teknologi real-time [6], Cosine Similarity pada lingkungan kampus [7], sistem berbasis web Universitas Udayana [8], UCD [9], serta String Matching [10]. Namun, masih terdapat peluang untuk mengembangkan sistem yang mengintegrasikan proses pelaporan, pencarian, pencocokan, verifikasi kepemilikan, klaim, dan serah-terima barangdalam satu platform terpusat.

Oleh karena itu, penelitian ini mengusulkan FinderCampus, yaitu Sistem Informasi Manajemen Lost

and FoundTerpusat Berbasis Web untuk Pelaporan di Area Kampus. FinderCampus menyediakan fitur Lapor Hilang, Lapor Ditemukan, unggah foto, kategori, deskripsi, lokasi, pencarian dan filter, pengajuan klaim, verifikasi bukti kepemilikan, serta dashboard admin untuk memantau status barang. Berbeda dengan pendekatan yang hanya menggunakan kemiripan teks [1][7][10], penelitian ini menggunakan Simple Multi-Attribute Rating Technique (SMART) untuk membantu proses pencocokan berdasarkan beberapa kriteria, seperti kategori, lokasi, waktu, dan karakteristik barang. Dengan demikian, FinderCampus diharapkan dapat membantu civitas akademika dalam menemukan barang secara lebih mudah serta

mendukung pengelolaan barang temuan yang lebih terstruktur, transparan, dan terintegrasi.

- II. METODE PENELITIAN

Penelitian ini bertujuan untuk mengembangkan sistem informasi manajemen Lost and Found

terpusat berbasis website untuk membantu proses pelaporan, pencarian, pencocokan, dan pengelolaan barang hilang di lingkungan kampus. Sistem dikembangkan menggunakan metode Prototype karena memungkinkan pengembangan dilakukan secara bertahap melalui pembuatan rancangan awal sistem dan evaluasi berdasarkan kebutuhan pengguna. Metode Prototype digunakan agar sistem yang dikembangkan dapat disesuaikan dengan proses pelaporan dan pengelolaan barang Lost and Found yang dibutuhkan oleh pengguna. Sementara itu, metode Simple Multi-Attribute Rating Technique (SMART) digunakan untuk melakukan pencocokan dan penilaian antara data barang hilang dan barang ditemukan berdasarkan beberapa kriteria yang telah ditentukan. Penggunaan metode pencocokan diperlukan karena penelitian sebelumnya telah menerapkan pendekatan seperti Term Frequency-Inverse Document Frequency (TF-IDF), Cosine Similarity, dan String Matching dalam pencarian barang hilang [1][7][10]. Pada penelitian ini, SMART digunakan untuk memberikan nilai kesesuaian berdasarkan beberapa atribut barang sehingga hasil pencocokan tidak hanya mempertimbangkan kesamaan teks.

Objek penelitian ini adalah proses pengelolaan barang hilang dan ditemukan di lingkungan kampus,

khususnya proses pelaporan barang hilang, pelaporan barang ditemukan, pencarian barang, pencocokan data, verifikasi kepemilikan, hingga proses pengembalian barang. Data penelitian diperoleh melalui observasi terhadap proses pengelolaan Lost and Foundserta studi literatur dari penelitian terdahulu yang berkaitan dengan sistem Lost and Found berbasis website maupun mobile [1]–[10]. Data yang diperoleh kemudian dianalisis untuk menentukan kebutuhan pengguna, kebutuhan sistem, serta kriteria yang digunakan dalam proses pencocokan barang. Hasil analisis tersebut digunakan sebagai dasar dalam pengembangan sistem menggunakan beberapa tahapan metode Prototype. Setelah sistem dirancang, metode SMART diterapkan untuk menghitung tingkat kesesuaian antara laporan barang hilang dan barang ditemukan berdasarkan kriteria yang telah ditentukan.


## A. Objek Penelitian

Penelitian ini bertujuan untuk mengembangkan FinderCampus, yaitu sistem informasi manajemen

Lost and Found Terpusat berbasis website yang digunakan untuk membantu proses pelaporan, pencarian, pencocokan, dan pengelolaan barang hilang dan ditemukan di lingkungan kampus. Pengembangan sistem menggunakan metode Prototype, sedangkan metode Simple Multi-Attribute Rating Technique (SMART) digunakan untuk melakukan pencocokan dan penilaian tingkat kesesuaian antara barang yang dilaporkan hilang dan barang yang ditemukan. Metode SMART dipilih karena proses pencocokan dapat mempertimbangkan beberapa kriteria secara bersamaan, seperti kategori, lokasi, waktu, warna, dan karakteristik barang.

Data penelitian diperoleh melalui observasi terhadap proses pengelolaan barang hilang dan

ditemukan serta studi literatur terhadap penelitian terdahulu mengenai sistem Lost and Found [1]–[10]. Data tersebut digunakan untuk menentukan kebutuhan sistem dan kriteria yang diperlukan dalam proses pencocokan. Pengembangan sistem dilakukan melalui pembuatan rancangan awal (prototype), kemudian rancangan tersebut digunakan sebagai dasar dalam membangun sistem FinderCampus. Sistem yang dikembangkan diharapkan dapat membuat proses pelaporan dan pencarian barang menjadi lebih terstruktur serta membantu proses pencocokan barang berdasarkan tingkat kesesuaiannya.

## B. Perancangan Sistem

Perancangan sistem dilakukan untuk menggambarkan kebutuhan, alur proses, interaksi pengguna,

serta struktur data pada FinderCampus sebelum sistem diimplementasikan. Perancangan dilakukan menggunakan Flowchart, Use Case Diagram, dan Entity Relationship Diagram (ERD). Ketiga diagram tersebut digunakan untuk menggambarkan alur kerja sistem, hubungan antara pengguna dengan sistem, serta hubungan antar entitas dalam basis data.

Flowchart digunakan untuk menggambarkan alur proses utama pada sistem FinderCampus, mulai

dari pengguna masuk ke sistem hingga proses pelaporan, pencocokan, klaim, dan verifikasi barang.Alur proses dimulai ketika pengguna mengakses FinderCampus dan melakukan login atau registrasi. Setelah berhasil masuk, pengguna dapat memilih menu Lapor Hilang atau Lapor Ditemukan. Pengguna kemudian mengisi data barang yang diperlukan dan mengirimkan laporan. Sistem menyimpan data laporan dan melakukan proses pencocokan dengan data barang yang tersedia menggunakan metode SMART.

Hasil pencocokan kemudian ditampilkan kepada pengguna berdasarkan tingkat kesesuaian. Apabila

terdapat barang yang sesuai, pengguna dapat mengajukan klaim. Selanjutnya, admin melakukan verifikasi terhadap data dan bukti kepemilikan. Jika klaim dinyatakan sesuai, proses pengembalian barang dilakukan dan status barang diperbarui menjadi selesai.

## 1) Flowchart

*Gambar 1. Flowchart Sistem FinderCampus*

- 2) Usecase Diagram


Kebutuhan fungsional serta interaksi pengguna dengan sistem FinderCampus dimodelkan

menggunakan Use Case Diagram. Diagram ini menggambarkan aktivitas yang dapat dilakukan oleh pengguna dan admin dalam sistem.

*Gambar 2. Use Case Diagram FinderCampus*

Pada sistem FinderCampus terdapat dua aktor utama, yaitu Pengguna dan Admin.Pengguna dapat

melakukan registrasi, login, melihat data barang, membuat laporan barang hilang, membuat laporan barang ditemukan, melakukan pencarian dan filter barang, melihat hasil pencocokan, mengajukan klaim, serta melihat status laporan dan klaim.Sementara itu, admin memiliki akses untuk login, melihat laporan, melakukan verifikasi laporan, mengelola data barang, memeriksa klaim, memperbarui status barang, serta mengelola riwayat pengembalian barang.Proses pencocokan dilakukan oleh sistem setelah data laporan dimasukkan. Sistem menghitung tingkat kesesuaian menggunakan metode SMART berdasarkan kriteria yang telah ditentukan. Hasil perhitungan kemudian digunakan untuk memberikan rekomendasi barang yang memiliki tingkat kesesuaian dengan laporan kehilangan.

## 3) Entity Relationship Diagram

Struktur basis data pada FinderCampus dimodelkan menggunakan Entity Relationship Diagram

(ERD). ERD digunakan untuk menggambarkan entitas, atribut, serta hubungan antarentitas yang diperlukan dalam sistem.

*Gambar 3. Entity Relationship Diagram FinderCampus*

Entitas utama yang digunakan dalam sistem meliputi:

- 1. User, menyimpan data pengguna sistem.


- 2. Laporan_hilang, menyimpan informasi barang yang dilaporkan hilang.

- 3. Laporan_Ditemukan, menyimpan informasi barang yang ditemukan.

- 4. Barang, menyimpan data barang yang dilaporkan.

- 5. Klaim, menyimpan data pengajuan klaim terhadap barang.

- 6. Verifikasi, menyimpan hasil pemeriksaan klaim oleh admin.

- 7. Riwayat_Pengembalian, menyimpan informasi proses pengembalian barang.

- 8. Admin, bertugas mengelola data dan melihat laporan

## III. HASIL DAN PEMBAHASAN

## A. Implementasi Sistem

Implementasi sistem FinderCampus dilakukan menggunakan pendekatan Prototype untuk

menghasilkan rancangan sistem informasi Lost and Found berbasis web. Sistem dirancang untuk memusatkan proses pelaporan barang hilang dan barang ditemukan di area kampus, pencarian barang, pencocokan data, pengajuan klaim, serta verifikasi oleh admin. Pada proses pencocokan, metode SMART (Simple Multi-Attribute Rating Technique) digunakan untuk menentukan tingkat kesesuaian antara laporan barang hilang dan barang ditemukan berdasarkan beberapa kriteria. Pendekatan implementasi disusun dengan mengacu pada alur sistem dan kebutuhan fungsional yang telah dirancang.

## 1) Halaman Login dan Registrasi

Halaman login digunakan sebagai akses awal pengguna untuk masuk ke sistem FinderCampus

dengan memasukkan email dan kata sandi. Pengguna yang belum memiliki akun dapat melakukan registrasi dengan mengisi data yang diperlukan. Setelah proses autentikasi berhasil, pengguna diarahkan menuju halaman dashboard.

*Gambar 4. Halaman Login dan Registrasi FinderCampus*

## 2) Halaman Dashboard

Halaman dashboard merupakan halaman utama yang menyediakan akses terhadap fitur-fitur

FinderCampus. Pengguna dapat memilih menu Lapor Barang Hilang, Lapor Barang Ditemukan, mencari barang, melihat hasil pencocokan, serta memantau status laporan dan klaim.


*Gambar 5. Halaman Dashboard FinderCampus*

## 3) Halaman Lapor Barang Hilang dan Barang Ditemukan

Halaman pelaporan digunakan untuk memasukkan informasi barang. Pengguna dapat mengisi

kategori barang, deskripsi, foto, lokasi, waktu, warna, dan ciri-ciri khusus. Terdapat dua jenis laporan, yaitu laporan barang hilang dan laporan barang ditemukan. Setelah data lengkap, pengguna dapat melakukan submit sehingga laporan tersimpan dalam sistem.

*Gambar 6. Halaman Lapor Barang Hilang dan Barang Ditemukan*


## 4) Halaman Pencarian

Halaman pencarian digunakan untuk menemukan data barang berdasarkan kata kunci maupun

kriteria tertentu. Data laporan barang hilang kemudian dibandingkan dengan data barang ditemukan menggunakan metode SMART.

*Gambar 7. Halaman Pencarian dan Pencocokan*

## 5) Halaman Status dan Klaim

Pengguna dapat mengajukan klaim terhadap barang yang dianggap sesuai dengan

memberikan bukti kepemilikan. Selanjutnya, admin melakukan pemeriksaan terhadap data laporan, data barang, dan bukti yang diberikan. Admin dapat menyetujui atau menolak klaim berdasarkan hasil verifikasi.


*Gambar 7. Status dan Klaim*

Setelah klaim disetujui dan barang dikembalikan, sistem memperbarui status barang dan menyimpan

riwayat pengembalian. Riwayat tersebut dapat digunakan untuk mencatat proses pengembalian secara terstruktur.

## B. Pengujian Sistem

Pengujian dilakukan untuk memastikan bahwa sistem FinderCampus dapat menjalankan fungsi

utama sesuai dengan kebutuhan yang telah dirancang. Pengujian difokuskan pada fungsi pengguna, fungsi admin, proses pencocokan menggunakan SMART, serta proses pengajuan dan verifikasi klaim. Pengujian fungsional dapat dilakukan menggunakan metode Black Box Testing, yaitu dengan memberikan input pada setiap fitur kemudian membandingkan hasil aktual dengan keluaran yang diharapkan. Pola pengujian ini mengikuti struktur pengujian pada contoh yang kamu berikan.

*Tabel 2. Hasil Pengujian Blackbox FinderCampus*

| Skenario Pengujian | Input | Output | Hasil |
| --- | --- | --- | --- |
| Registrasi akun | Nama, email, password | Akun berhasil dibuat | Berhasil |
| Login pengguna |   | Email dan password valid Pengguna berhasil masuk | Berhasil |
| Login tidak valid | Password salah | Sistem menampilkan | Berhasil |
|   |   | pesan kesalahan |   |
| Menampilkan dashboard | Login berhasil | Dashboard ditampilkan | Berhasil |
| Lapor barang hilang | Data barang hilang | Laporan berhasil | Berhasil |
|   | lengkap | disimpan |   |
| Lapor barang ditemukan Data barang ditemukan |   | Laporan berhasil | Berhasil |


| Skenario Pengujian | Input | Output | Hasil |
| --- | --- | --- | --- |
|   | lengkap | disimpan |   |
| Upload foto barang | File foto barang | Foto berhasil tersimpan | Berhasil |
| Mencari barang | Kata kunci/kriteria | Data barang yang sesuai | Berhasil |
|   |   | ditampilkan |   |
| Proses pencocokan | Data laporan hilang dan | Sistem menghitung nilai | Berhasil |
| SMART | ditemukan | kecocokan |   |
| Menampilkan hasil | Hasil perhitungan | Barang ditampilkan | Berhasil |
| SMART |   | berdasarkan nilai |   |
|   |   | kecocokan |   |
| Mengajukan klaim | Data klaim dan bukti | Klaim berhasil dikirim | Berhasil |
|   | kepemilikan |   |   |
| Verifikasi klaim | Data klaim dan bukti | Admin dapat | Berhasil |
|   |   | menyetujui/menolak |   |
|   |   | klaim |   |
| Memperbarui status | Klaim disetujui | Status barang diperbarui | Berhasil |
| barang |   |   |   |
| Mencatat pengembalian | Data pengembalian | Riwayat pengembalian | Berhasil |
|   |   | tersimpan |   |
| Melihat status laporan | Data laporan pengguna | Status laporan | Berhasil |
|   |   | ditampilkan |   |
| Logout | Klik logout | Sesi pengguna berakhir | Berhasil |

Pengujian difokuskan pada fungsi utama FinderCampus, mulai dari proses registrasi dan login

hingga pelaporan barang, pencarian, pencocokan menggunakan SMART, pengajuan klaim, verifikasi admin, dan pencatatan pengembalian. Hasil pengujian menunjukkan bahwa setiap fungsi dirancang untuk memberikan keluaran sesuai dengan kebutuhan sistem. Validasi pada setiap tahapan juga digunakan untuk memastikan data yang diproses lengkap sebelum pengguna melanjutkan ke proses berikutnya.

## C. Hasil Penerapan Metode SMART

Metode SMART (Simple Multi-Attribute Rating Technique) diterapkan pada FinderCampus untuk

menentukan tingkat kecocokan antara laporan barang hilang dengan barang ditemukan. Proses ini dilakukan dengan mempertimbangkan beberapa kriteria, yaitu kategori, lokasi, waktu, warna, dan ciri-ciri barang. Setiap kriteria memiliki bobot yang menunjukkan tingkat kepentingannya dalam proses pencocokan. Bobot yang digunakan pada penelitian ini ditunjukkan pada Tabel berikut.Kriteria yang digunakan dalam proses pencocokan meliputi:

*Tabel 1. Tabel Kriteria Pencarian*

| Kriteria | Kategori |
| --- | --- |
| Kategori | 25% |
| Lokasi | 25% |
| Waktu | 20% |
| Warna | 10% |
| Ciri Barang | 20% |


| Kriteria | Kategori |
| --- | --- |
| Total | 100% |

Langkah pertama dalam penerapan metode SMART adalah melakukan normalisasi bobot agar seluruh bobot memiliki total nilai 1. Normalisasi bobot dihitung menggunakan Persamaan (1).

Keterangan: Wj adalah bobot kriteria yang telah dinormalisasi, wj adalah bobot awal setiap kriteria, dan Σwj adalah jumlah seluruh bobot kriteria. Berdasarkan Persamaan (1), jumlah seluruh bobot yang digunakan adalah 100%, sehingga bobot kriteria dapat dikonversikan menjadi nilai desimal. Dengan demikian, bobot kategori menjadi 0,25, lokasi 0,25, waktu 0,20, warna 0,10, dan ciri-ciri 0,20.

Setelah bobot dinormalisasi, tahap selanjutnya adalah menentukan nilai utilitas atau tingkat kesesuaian setiap barang terhadap laporan kehilangan. Nilai utilitas menggunakan rentang 0 sampai 1, di mana nilai 1 menunjukkan kesesuaian yang sangat tinggi dan nilai 0 menunjukkan tidak adanya kesesuaian. Nilai yang digunakan dapat berupa 1,00 untuk sangat sesuai, 0,75 untuk sesuai, 0,50 untuk cukup sesuai, 0,25 untuk kurang sesuai, dan 0,00 untuk tidak sesuai.

Nilai akhir setiap barang kemudian dihitung dengan menjumlahkan hasil perkalian antara bobot kriteria dengan nilai utilitasnya. Perhitungan tersebut menggunakan Persamaan (2).

Keterangan: Ui adalah nilai akhir kecocokan alternatif atau barang, Wj adalah bobot kriteria yang telah dinormalisasi, dan Uij adalah nilai utilitas alternatif pada setiap kriteria. Berdasarkan Persamaan (2), semakin besar nilai Ui, semakin tinggi tingkat kecocokan barang ditemukan terhadap laporan barang hilang.

## IV. KESIMPULAN

Berdasarkan hasil perancangan, implementasi, dan pengujian, FinderCampus berhasil dikembangkan

sebagai Sistem Informasi Manajemen Lost and Found terpusat berbasis web yang mendukung pelaporan barang hilang dan barang ditemukan, pencarian, pencocokan, pengajuan klaim, verifikasi admin, pemantauan status, serta pencatatan riwayat pengembalian secara terstruktur. Metode SMART (Simple Multi-Attribute Rating Technique) diterapkan untuk membantu menentukan tingkat kecocokan antara barang hilang dan barang ditemukan berdasarkan kriteria kategori, lokasi, waktu, warna, dan ciri-ciri barang. Hasil pengujian fungsional menunjukkan bahwa fitur utama sistem dapat berjalan sesuai dengan keluaran yang diharapkan. Meskipun demikian, penelitian ini masih memiliki keterbatasan pada jumlah kriteria pencocokan, ketergantungan terhadap kelengkapan data pengguna, serta belum adanya pengenalan gambar secara otomatis. Oleh karena itu, penelitian selanjutnya disarankan untuk menambahkan kriteria dan penyesuaian bobot berdasarkan data kehilangan nyata, mengintegrasikan teknologi pengenalan gambar untuk meningkatkan proses pencocokan, serta melakukan pengujian dengan jumlah pengguna dan data yang lebih besar agar kinerja sistem dapat dievaluasi secara lebih menyeluruh.


## DAFTAR PUSTAKA

- [1] L. Suryani and K. Edy, “Pengembangan Aplikasi ‘Lost & Found’ Berbasis Android dengan Menggunakan Metode Term Frequency–Inverse Document Frequency (TF-IDF) dan Cosine Similarity,” Electro Luceat, vol. 6, no. 2, pp. 190–204, 2020, doi: 10.32531/jelekn.v6i2.232.

- [2] M. N. Zhalifunnas, P. D. Ibnugraha, and S. J. I. Ismail, “Pengembangan Backend Website Admin Lost and Found pada PT Kereta Cepat Indonesia China,” eProceedings of Applied Science, vol. 11, no. 4, pp. 939–948, 2025.

- [3] G. G. C. Wijaya, I. N. T. A. Putra, and M. N. Naryantika, “Perancangan Aplikasi Mobile Lost and Found di Lingkungan Kampus Menggunakan Metode Design Thinking,” Jurnal Informatika dan Teknik Elektro Terapan, vol. 13, no. 3, pp. 1755–1764, 2025, doi: 10.23960/jitet.v13i3.6585.

- [4] A. A. Adiwijaya and A. Nugroho, “Web-based Lost & Found System Design at Karangturi National University,” Pixel: Jurnal Ilmiah Komputer Grafis, vol. 17, no. 2, 2024, doi: 10.51903/pixel.v17i2.2169.

- [5] A. Fadila and N. Safitri, “Sistem Informasi Lost and Found Barang Berbasis Web di PT Kereta Commuter Indonesia,” Information Management for Educators and Professionals: Journal of Information Management, vol. 11, no. 1, pp. 76–87, 2026, doi: 10.51211/imbi.v11i1.3948.

- [6] R. I. P. Siagian, M. Z. Al-Kautsar, E. Pratama, N. Khoiriah, F. A. S. Harahap, and A. Perdana, “Perancangan dan Implementasi Aplikasi Mobile Lost & Found Kampus Berbasis Real-Time Menggunakan Jetpack Compose dan Firebase,” Jurnal Nasional Komputasi dan Teknologi Informasi, vol. 8, no. 5, pp. 2751–2756, 2025, doi: 10.32672/jnkti.v8i5.9871.

- [7] Aminudin, I. Nuryasin, and S. Budianti, “Sistem Informasi Pencarian Barang Hilang ‘Lost & Found’ pada Kampus 3 Universitas Muhammadiyah Malang,” Repository, 2019.

- [8] Z. N. Salsabila and I. G. N. A. C. Putra, “Rancangan Sistem Cari dan Temu Barang Hilang di Universitas Udayana Berbasis Web,” Jurnal Nasional Teknologi Informasi dan Aplikasinya, vol. 2, no. 4, pp. 737–746, 2024, doi: 10.24843/JNATIA.2024.v02.i04.p09.

- [9] R. A. Albanjari, L. N. Hayati, and Irawati, “Rancang Bangun Aplikasi Mobile Lost & Found Berbasis UCD untuk Meningkatkan Efisiensi Pencarian Barang di Kampus UMI,” LINIER: Literatur Informatika dan Komputer, vol. 2, no. 2, pp. 169–181, 2025, doi: 10.33096/linier.v2i2.3107.

- [10] K. Z. Jinan, S. S. Marulitua, M. R. Wijaya, A. S. Saragih, S. S. Nafiisah, and D. Y. Niska, “Rancang Bangun Sistem ‘UNIMED Lost & Found’ Menggunakan Algoritma String Matching untuk Sinkronisasi Data Laporan Kehilangan Barang,” Djtechno: Jurnal Teknologi Informasi, vol. 7, no. 2, 2026, doi: 10.46576/djtechno.v7i2.8990.
