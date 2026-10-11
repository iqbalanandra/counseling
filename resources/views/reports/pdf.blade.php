<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>{{ $title }}</title>
    <style>
        body {
            font-family: 'Helvetica Neue', Helvetica, Arial, sans-serif;
            font-size: 11pt;
            color: #333;
            line-height: 1.4;
            margin: 20px;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #333;
            padding-bottom: 12px;
            margin-bottom: 20px;
        }
        .header h1 {
            margin: 0;
            font-size: 16pt;
            text-transform: uppercase;
        }
        .header h2 {
            margin: 4px 0;
            font-size: 13pt;
            font-weight: normal;
        }
        .header p {
            margin: 0;
            font-size: 9pt;
            color: #666;
        }
        .meta-info {
            margin-bottom: 20px;
            font-size: 10pt;
        }
        .meta-info table {
            width: 100%;
        }
        table.data-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 10px;
        }
        table.data-table th, table.data-table td {
            border: 1px solid #999;
            padding: 7px 10px;
            text-align: left;
            font-size: 10pt;
        }
        table.data-table th {
            background-color: #f2f2f2;
            font-weight: bold;
        }
        .footer {
            margin-top: 40px;
            width: 100%;
        }
        .footer td {
            vertical-align: top;
            width: 50%;
            font-size: 10pt;
        }
        .signature-space {
            height: 60px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>SMP NEGERI 1 CERDAS MANDIRI</h1>
        <h2>UNIT LAYANAN BIMBINGAN DAN KONSELING (BK)</h2>
        <p>Jl. Pendidikan No. 123, Telepon: (021) 1234567 | Email: bk@smpn1.sch.id</p>
    </div>

    <div class="meta-info">
        <table>
            <tr>
                <td style="width: 20%;"><strong>Laporan:</strong></td>
                <td>{{ $title }}</td>
                <td style="width: 20%; text-align: right;"><strong>Tanggal Cetak:</strong></td>
                <td style="width: 20%; text-align: right;">{{ $generated_at }}</td>
            </tr>
            <tr>
                <td><strong>Periode:</strong></td>
                <td>{{ $period }}</td>
                <td style="text-align: right;"><strong>Dicetak Oleh:</strong></td>
                <td style="text-align: right;">{{ $printed_by }}</td>
            </tr>
        </table>
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No</th>
                @foreach ($headers as $header)
                    <th>{{ $header }}</th>
                @endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td style="text-align: center;">{{ $index + 1 }}</td>
                    @foreach ($row as $cell)
                        <td>{{ $cell }}</td>
                    @endforeach
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + 1 }}" style="text-align: center; color: #888;">
                        Tidak ada data untuk periode ini.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <table class="footer">
        <tr>
            <td></td>
            <td style="text-align: right;">
                <p>Mengetahui,<br>Koordinator Guru BK</p>
                <div class="signature-space"></div>
                <p><strong>Dra. Hj. Siti Rahma, M.Pd</strong><br>NIP. 19780512 200312 2 001</p>
            </td>
        </tr>
    </table>
</body>
</html>
