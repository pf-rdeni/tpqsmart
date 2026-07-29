<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Berita Acara Ujian</title>
    <style>
        body { font-family: 'Helvetica', Arial, sans-serif; font-size: 11pt; line-height: 1.5; margin: 30px; }
        .title { text-align: center; font-weight: bold; font-size: 14pt; margin-bottom: 20px; text-decoration: underline; }
        .content { margin-bottom: 20px; text-align: justify; }
        .signature-table { width: 100%; margin-top: 40px; }
        .signature-table td { text-align: center; vertical-align: top; }
    </style>
</head>
<body>
    <div class="title">BERITA ACARA PELAKSANAAN UJIAN MDTA</div>

    <div class="content">
        Pada hari ini <strong><?= date('l') ?></strong> tanggal <strong><?= date('d') ?></strong> bulan <strong><?= date('F') ?></strong> tahun <strong><?= date('Y') ?></strong>, telah dilaksanakan Ujian MDTA dengan rincian sebagai berikut:
    </div>

    <table style="width: 100%; margin-bottom: 20px;">
        <tr>
            <td width="180"><strong>Nama Mata Ujian</strong></td>
            <td width="10">:</td>
            <td><?= esc($jadwal['NamaUjian']) ?></td>
        </tr>
        <tr>
            <td><strong>Waktu Pelaksanaan</strong></td>
            <td>:</td>
            <td><?= date('d/m/Y H:i', strtotime($jadwal['TanggalMulai'])) ?> s.d <?= date('d/m/Y H:i', strtotime($jadwal['TanggalSelesai'])) ?></td>
        </tr>
        <tr>
            <td><strong>Jumlah Peserta Terdaftar</strong></td>
            <td>:</td>
            <td><?= count($sesiList) ?> Orang</td>
        </tr>
    </table>

    <div class="content">
        Catatan Pelaksanaan Ujian:<br>
        Pelaksanaan ujian berlangsung dengan tertib, lancar, dan aman tanpa ada kendala yang berarti.
    </div>

    <table class="signature-table">
        <tr>
            <td width="50%">
                Pengawas Ujian,<br><br><br><br>
                ( .................................... )
            </td>
            <td width="50%">
                Kepala Lembaga / Panitia,<br><br><br><br>
                ( .................................... )
            </td>
        </tr>
    </table>
</body>
</html>
