<!-- resources/views/admin/exports/apotek-pdf.blade.php -->
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Apotek - {{ $kecamatan }}</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 20px;
            font-size: 11px;
            color: #333;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 22px;
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
        .badge {
            display: inline-block;
            padding: 2px 5px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
        }
        .badge-primary {
            background-color: #007bff;
        }
        .badge-success {
            background-color: #28a745;
        }
        .badge-secondary {
            background-color: #6c757d;
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
        <div class="title">Data Apotek di Kota Banjarmasin</div>
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
            <span>{{ $total }} Apotek</span>
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
                <th width="18%">Nama Apotek</th>
                <th width="10%">Skala Usaha</th>
                <th width="18%">Alamat</th>
                <th width="8%">Kecamatan</th>
                <th width="8%">Kelurahan</th>
                <th width="7%">Kota</th>
                <th width="10%">Tanggal Berdiri</th>
                <th width="7%">Tenaga Kerja</th>
                <th width="10%">Koordinat</th>
            </tr>
        </thead>
        <tbody>
            @foreach($apoteksList as $index => $item)
            <tr>
                <td class="text-center">{{ $index + 1 }}</td>
                <td>{{ $item->nama_apotek ?? 'Apotek ' . $item->id_apotek }}</td>
                <td class="text-center">
                    @if($item->skala_usaha == 'Kecil')
                        <span class="badge badge-info">Kecil</span>
                    @elseif($item->skala_usaha == 'Mikro')
                        <span class="badge badge-primary">Mikro</span>
                    @elseif($item->skala_usaha == 'Besar')
                        <span class="badge badge-success">Besar</span>
                    @elseif($item->skala_usaha == 'Menengah')
                        <span class="badge badge-secondary">Menengah</span>
                    @else
                        {{ $item->skala_usaha ?? '-' }}
                    @endif
                </td>
                <td>{{ $item->alamat ?? '-' }}</td>
                <td>{{ $item->kecamatan ?? '-' }}</td>
                <td>{{ $item->kelurahan ?? '-' }}</td>
                <td>{{ $item->kota ?? 'Banjarmasin' }}</td>
                <td>{{ $item->tgl_berdiri ?? '-' }}</td>
                <td class="text-center">{{ $item->tenaga_kerja ?? '-' }}</td>
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
        <p class="metadata">ID: PDF-APT-{{ date('YmdHis') }}</p>
    </div>
</body>
</html>