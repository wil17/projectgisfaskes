<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Rumah Sakit - {{ $kecamatan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 12px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #007bff;
            margin-bottom: 10px;
        }
        .title {
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 5px;
        }
        .subtitle {
            font-size: 14px;
            color: #666;
        }
        .info-box {
            background-color: #f8f9fa;
            padding: 15px;
            border-left: 4px solid #007bff;
            margin-bottom: 20px;
        }
        .info-row {
            display: flex;
            margin-bottom: 5px;
        }
        .info-label {
            font-weight: bold;
            width: 120px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 6px;
            text-align: left;
            font-size: 11px;
            vertical-align: top;
        }
        th {
            background-color: #f8f9fa;
            font-weight: bold;
            color: #495057;
        }
        .text-center {
            text-align: center;
        }
        .page-break {
            page-break-after: always;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 10px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-number::after {
            content: counter(page);
        }
        .metadata {
            font-size: 9px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SIG Fasilitas Kesehatan Banjarmasin</div>
        <div class="title">Data Rumah Sakit di Kota Banjarmasin</div>
        <div class="subtitle">{{ $kecamatan }}</div>
    </div>

    <div class="info-box">
        <div class="info-row">
            <span class="info-label">Kecamatan:</span>
            <span>{{ $kecamatan }}</span>
        </div>
        @if($search)
        <div class="info-row">
            <span class="info-label">Pencarian:</span>
            <span>{{ $search }}</span>
        </div>
        @endif
        <div class="info-row">
            <span class="info-label">Total Data:</span>
            <span>{{ $total }} Rumah Sakit</span>
        </div>
        <div class="info-row">
            <span class="info-label">Digenerate:</span>
            <span>{{ $generated_at }}</span>
        </div>
        <div class="info-row">
            <span class="info-label">Admin:</span>
            <span>{{ $generated_by }}</span>
        </div>
    </div>

    <table>
        <thead>
            <tr>
                <th width="4%">No</th>
                <th width="20%">Nama Rumah Sakit</th>
                <th width="22%">Alamat</th>
                <th width="18%">Poliklinik</th>
                <th width="18%">Dokter</th>
                <th width="9%">Kecamatan</th>
                <th width="9%">Kelurahan</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rumahsakits as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_rs ?? 'RS ' . $item->id_rs }}</td>
                <td>{{ $item->alamat ?? '-' }}</td>
                <td>
                    @php
                        $polikliniks = explode(',', $item->poliklinik ?? '');
                        $polikliniks = array_map('trim', $polikliniks);
                        echo implode(', ', array_filter($polikliniks));
                    @endphp
                </td>
                <td>
                    @php
                        $dokters = explode(',', $item->nama_dokter ?? '');
                        $dokters = array_map('trim', $dokters);
                        echo implode(', ', array_filter($dokters));
                    @endphp
                </td>
                <td>{{ $item->kecamatan ?? '-' }}</td>
                <td>{{ $item->kelurahan ?? '-' }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Halaman <span class="page-number"></span> - Digenerate dari SIG Fasilitas Kesehatan Banjarmasin pada {{ $generated_at }}</p>
        <p class="metadata">ID: PDF-RS-{{ date('YmdHis') }}</p>
    </div>
</body>
</html>