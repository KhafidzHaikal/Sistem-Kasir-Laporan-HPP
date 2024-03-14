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
                <th width="4%" rowspan="2">No</th>
                <th rowspan="2">Tanggal</th>
                <th rowspan="2">Nama Barang</th>
                <th width="5%" rowspan="2">Jumlah</th>
                <th colspan="2">Pembelian</th>
                <th colspan="2">Penjualan</th>
                <th rowspan="2">Laba-Rugi (Harga Jual - Harga Beli)</th>
            </tr>
            <tr>
                <th width="10%">Harga Beli</th>
                <th>Total</th>
                <th width="10%">Harga Jual</th>
                <th>Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($results as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td width="10%">{{ tanggal_indonesia($row->created_at, false) }}</td>
                    <td style="text-align: left">{{ $row->nama_produk }}</td>
                    <td>
                        @if ($row->stok_belanja == null)
                            0
                        @else
                            {{ $row->stok_belanja }}
                        @endif
                    </td>
                    <td style="text-align: right">{{ format_uang($row->harga_beli) }}</td>
                    <td style="text-align: right">{{ format_uang($row->harga_beli * $row->stok_belanja) }}</td>
                    <td style="text-align: right">{{ format_uang($row->harga_jual) }}</td>
                    <td style="text-align: right">{{ format_uang($row->harga_jual * $row->stok_belanja) }}</td>
                    <td style="text-align: right">
                        {{ format_uang($row->harga_jual * $row->stok_belanja - $row->harga_beli * $row->stok_belanja) }}
                    </td>
                </tr>
            @endforeach
            <tr>
                <td colspan="8"><strong>TOTAL LABA-RUGI</strong></td>
                <td style="text-align: right" width="20%">{{ format_uang($total_laba_rugi) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
