<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Laba Rugi</title>

    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            line-height: normal;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        h2 {
            font-size: 14px;
        }

        body {
            margin: 0;
            font-size: 12px;
            font-weight: 400;
            color: #000000;
            text-align: center;
        }

        td,
        th {
            border: 1px solid #000000;
            text-align: center;
            padding: 5px;
            /* vertical-align: top; */
        }

        td {
            text-align: left;
            word-spacing: 0px;
            vertical-align: top;
            text-align: center;
        }

        table {
            border-collapse: collapse;
            width: 100%;
        }
    </style>
</head>

<body>
    <h3 class="text-center">Laporan Laba Rugi</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($awal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th width="5%" rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Nama Barang</th>
                <th colspan="3">Pembelian</th>
                <th rowspan="2">Harga Jual</th>
                <th rowspan="2">Laba-Rugi (Harga Jual - Harga Beli)</th>
            </tr>
            <tr>
                <th width="5%">Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ tanggal_indonesia($row->created_at, false) }}</td>
                    <td>{{ $row->produk->merk }}</td>
                    <td>{{ $row->produk->stok }}</td>
                    <td style="text-align: right">{{ format_uang($row->produk->harga_beli) }}</td>
                    <td style="text-align: right">{{ format_uang($row->produk->total_harga_beli) }}</td>
                    <td style="text-align: right">{{ format_uang($row->produk->harga_jual * $row->produk->stok) }}</td>
                    <td style="text-align: right">
                        {{ format_uang($row->produk->harga_jual * $row->produk->stok - $row->produk->total_harga_beli) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7"><strong>TOTAL LABA-RUGI</strong></td>
                <td style="text-align: right">{{ format_uang($total_laba_rugi) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
