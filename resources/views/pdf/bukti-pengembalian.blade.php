<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Bukti Pengembalian — {{ $riwayat->laporan->kode_laporan }}</title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Helvetica', 'Arial', sans-serif; font-size: 12px; color: #1a1a1a; padding: 40px; }
        
        .header { text-align: center; border-bottom: 3px solid #1e40af; padding-bottom: 20px; margin-bottom: 30px; }
        .header h1 { font-size: 20px; color: #1e40af; margin-bottom: 4px; letter-spacing: 1px; }
        .header p { font-size: 11px; color: #6b7280; }
        .header .logo-text { font-size: 24px; font-weight: 900; color: #1e40af; margin-bottom: 8px; }
        
        .badge { display: inline-block; background: #dcfce7; color: #166534; padding: 4px 12px; border-radius: 4px; font-size: 11px; font-weight: 700; text-transform: uppercase; margin-bottom: 20px; }
        
        .section { margin-bottom: 24px; }
        .section-title { font-size: 13px; font-weight: 700; color: #1e40af; border-bottom: 1px solid #e5e7eb; padding-bottom: 6px; margin-bottom: 12px; text-transform: uppercase; letter-spacing: 0.5px; }
        
        table.info { width: 100%; border-collapse: collapse; }
        table.info td { padding: 6px 0; vertical-align: top; }
        table.info td.label { width: 160px; font-weight: 600; color: #6b7280; }
        table.info td.value { color: #1a1a1a; }
        
        .two-col { display: table; width: 100%; }
        .two-col .col { display: table-cell; width: 50%; vertical-align: top; padding-right: 16px; }
        .two-col .col:last-child { padding-right: 0; padding-left: 16px; }
        
        .signatures { margin-top: 50px; display: table; width: 100%; }
        .signatures .sig-box { display: table-cell; width: 33.33%; text-align: center; vertical-align: bottom; }
        .sig-box .sig-line { border-top: 1px solid #1a1a1a; width: 150px; margin: 60px auto 6px; }
        .sig-box .sig-name { font-weight: 700; font-size: 11px; }
        .sig-box .sig-role { font-size: 10px; color: #6b7280; }
        
        .footer { margin-top: 40px; text-align: center; font-size: 10px; color: #9ca3af; border-top: 1px solid #e5e7eb; padding-top: 12px; }
        .footer strong { color: #6b7280; }

        .stamp { text-align: center; margin-top: 20px; }
        .stamp .stamp-box { display: inline-block; border: 2px solid #166534; color: #166534; padding: 8px 20px; font-size: 14px; font-weight: 900; text-transform: uppercase; transform: rotate(-5deg); letter-spacing: 2px; }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo-text">🔎 FinderCampus</div>
        <h1>SURAT BUKTI PENGEMBALIAN BARANG</h1>
        <p>Sistem Informasi Manajemen Lost & Found Terpadu</p>
    </div>

    <div style="text-align: center;">
        <span class="badge">✅ BARANG TELAH DIKEMBALIKAN</span>
    </div>

    <!-- Info Laporan -->
    <div class="section">
        <div class="section-title">Informasi Barang</div>
        <table class="info">
            <tr>
                <td class="label">Kode Laporan</td>
                <td class="value">{{ $riwayat->laporan->kode_laporan }}</td>
            </tr>
            <tr>
                <td class="label">Nama Barang</td>
                <td class="value">{{ $riwayat->laporan->nama_barang }}</td>
            </tr>
            <tr>
                <td class="label">Kategori</td>
                <td class="value">{{ $riwayat->laporan->kategori->nama }}</td>
            </tr>
            <tr>
                <td class="label">Warna</td>
                <td class="value">{{ $riwayat->laporan->warna }}</td>
            </tr>
            <tr>
                <td class="label">Lokasi Ditemukan</td>
                <td class="value">{{ $riwayat->laporan->lokasi->nama }}</td>
            </tr>
            <tr>
                <td class="label">Tanggal Kejadian</td>
                <td class="value">{{ $riwayat->laporan->tanggal_kejadian->translatedFormat('d F Y') }}</td>
            </tr>
            @if($riwayat->laporan->ciri_khusus)
            <tr>
                <td class="label">Ciri Khusus</td>
                <td class="value">{{ $riwayat->laporan->ciri_khusus }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Info Pihak -->
    <div class="section">
        <div class="section-title">Pihak Terkait</div>
        <div class="two-col">
            <div class="col">
                <table class="info">
                    <tr><td colspan="2" style="font-weight:700; color:#1e40af; padding-bottom:6px;">Pelapor (Penemu)</td></tr>
                    <tr><td class="label">Nama</td><td class="value">{{ $riwayat->laporan->user->name }}</td></tr>
                    <tr><td class="label">NIM/NIP</td><td class="value">{{ $riwayat->laporan->user->nim_nip ?? '-' }}</td></tr>
                    <tr><td class="label">Fakultas</td><td class="value">{{ $riwayat->laporan->user->fakultas ?? '-' }}</td></tr>
                </table>
            </div>
            <div class="col">
                <table class="info">
                    <tr><td colspan="2" style="font-weight:700; color:#1e40af; padding-bottom:6px;">Penerima (Pemilik)</td></tr>
                    <tr><td class="label">Nama</td><td class="value">{{ $riwayat->klaim->user->name }}</td></tr>
                    <tr><td class="label">NIM/NIP</td><td class="value">{{ $riwayat->klaim->user->nim_nip ?? '-' }}</td></tr>
                    <tr><td class="label">Fakultas</td><td class="value">{{ $riwayat->klaim->user->fakultas ?? '-' }}</td></tr>
                </table>
            </div>
        </div>
    </div>

    <!-- Info Pengembalian -->
    <div class="section">
        <div class="section-title">Detail Pengembalian</div>
        <table class="info">
            <tr>
                <td class="label">Tanggal Pengembalian</td>
                <td class="value">{{ $riwayat->tanggal_pengembalian->translatedFormat('d F Y, H:i') }} WIB</td>
            </tr>
            <tr>
                <td class="label">Diverifikasi Oleh</td>
                <td class="value">{{ $riwayat->admin->name ?? 'Sistem' }}</td>
            </tr>
            @if($riwayat->catatan)
            <tr>
                <td class="label">Catatan</td>
                <td class="value">{{ $riwayat->catatan }}</td>
            </tr>
            @endif
        </table>
    </div>

    <!-- Stamp -->
    <div class="stamp">
        <div class="stamp-box">TERVERIFIKASI</div>
    </div>

    <!-- Signatures -->
    <div class="signatures">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $riwayat->laporan->user->name }}</div>
            <div class="sig-role">Pelapor / Penemu</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $riwayat->admin->name ?? 'Admin' }}</div>
            <div class="sig-role">Admin Verifikator</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-name">{{ $riwayat->klaim->user->name }}</div>
            <div class="sig-role">Penerima / Pemilik</div>
        </div>
    </div>

    <!-- Footer -->
    <div class="footer">
        <p>Dokumen ini dicetak secara otomatis oleh sistem <strong>FinderCampus</strong> pada {{ now()->translatedFormat('d F Y, H:i') }} WIB.</p>
        <p>Tidak memerlukan tanda tangan basah karena sudah diverifikasi secara digital.</p>
    </div>

</body>
</html>
