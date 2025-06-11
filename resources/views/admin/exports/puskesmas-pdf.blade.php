<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Puskesmas - {{ $kecamatan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #0d6efd;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 22px;
            font-weight: bold;
            color: #0d6efd;
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
            border-left: 4px solid #0d6efd;
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
            padding: 5px;
            text-align: left;
            font-size: 10px;
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
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            text-align: center;
            font-size: 9px;
            color: #666;
            border-top: 1px solid #ddd;
            padding-top: 10px;
        }
        .page-number::after {
            content: counter(page);
        }
        .metadata {
            font-size: 8px;
            color: #999;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="logo">SIG Fasilitas Kesehatan Banjarmasin</div>
        <div class="title">Data Puskesmas di Kota Banjarmasin</div>
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
            <span>{{ $total }} Puskesmas</span>
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
                <th width="18%">Nama Puskesmas</th>
                <th width="20%">Alamat</th>
                <th width="14%">Kepala Puskesmas</th>
                <th width="13%">Jam Operasional</th>
                <th width="8%">Kecamatan</th>
                <th width="8%">Kelurahan</th>
                <th width="7%">Kota</th>
                <th width="8%">Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($puskesmasList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_puskesmas ?? 'Puskesmas ' . $item->id_puskesmas }}</td>
                <td>{{ $item->alamat ?? '-' }}</td>
                <td>{{ $item->kepala_puskesmas ?? '-' }}</td>
                <td>{{ $item->jam_operasional ?? '-' }}</td>
                <td>{{ $item->kecamatan ?? '-' }}</td>
                <td>{{ $item->kelurahan ?? '-' }}</td>
                <td>{{ $item->kota ?? 'Banjarmasin' }}</td>
                <td>
                    @if($item->latitude && $item->longitude)
                        {{ number_format($item->latitude, 6) }}, {{ number_format($item->longitude, 6) }}
                    @else
                        -
                    @endif
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        <p>Halaman <span class="page-number"></span> - Digenerate dari SIG Fasilitas Kesehatan Banjarmasin pada {{ $generated_at }}</p>
        <p class="metadata">ID: PDF-PKM-{{ date('YmdHis') }}</p>
    </div>
</body>
</html>