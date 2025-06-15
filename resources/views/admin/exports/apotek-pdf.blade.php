<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Data Apotek {{ $kecamatan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            font-size: 12px;
            line-height: 1.3;
            margin: 0;
            padding: 0;
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #ddd;
        }
        .header h1 {
            margin: 0;
            padding: 0;
            font-size: 18px;
            font-weight: bold;
        }
        .header p {
            margin: 5px 0;
            font-size: 11px;
        }
        .meta-info {
            margin-bottom: 15px;
            font-size: 11px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 15px;
        }
        table th {
            background-color: #f2f2f2;
            text-align: left;
            padding: 8px;
            font-size: 11px;
            border: 1px solid #ddd;
        }
        table td {
            padding: 6px 8px;
            border: 1px solid #ddd;
            font-size: 10px;
        }
        table tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .footer {
            margin-top: 30px;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-break {
            page-break-after: always;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>DAFTAR APOTEK KOTA BANJARMASIN</h1>
        <p>{{ $kecamatan }}</p>
    </div>

    <div class="meta-info">
        <table style="border: none; width: 100%;">
            <tr style="border: none;">
                <td style="border: none; width: 60%;">
                    <strong>Filter Pencarian:</strong> {{ !empty($search) ? $search : 'Tidak ada' }}<br>
                    <strong>Total Data:</strong> {{ $total }} apotek
                </td>
                <td style="border: none; width: 40%; text-align: right;">
                    <strong>Tanggal Cetak:</strong> {{ $generated_at }}<br>
                    <strong>Dicetak Oleh:</strong> {{ $generated_by }}
                </td>
            </tr>
        </table>
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="20%">Nama Apotek</th>
                <th width="15%">Skala Usaha</th>
                <th width="20%">Alamat</th>
                <th width="15%">Kecamatan</th>
                <th width="15%">Kelurahan</th>
                <th width="10%">Tenaga Kerja</th>
            </tr>
        </thead>
        <tbody>
            @forelse($apoteksList as $index => $apotek)
                <tr>
                    <td>{{ $index + 1 }}</td>
                    <td>{{ $apotek->nama }}</td>
                    <td>{{ $apotek->skala_usaha }}</td>
                    <td>{{ $apotek->alamat }}</td>
                    <td>{{ $apotek->kecamatan }}</td>
                    <td>{{ $apotek->kelurahan }}</td>
                    <td>{{ $apotek->tenaga_kerja ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="7" style="text-align: center;">Tidak ada data apotek yang tersedia</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        <p>Data Apotek Kota Banjarmasin - Dihasilkan pada {{ $generated_at }}</p>
    </div>
</body>
</html>