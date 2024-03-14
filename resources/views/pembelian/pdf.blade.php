<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pembelian</title>

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
            font-family: 'Times New Roman', Times, serif;
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
    <h3 class="text-center">Laporan Pembelian</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($awal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th width="10%">Tanggal</th>
                <th>Nama Barang</th>
                <th width="5%">Jumlah</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th width="10%">Nama Supplier</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($pembelian as $row)
                @if ($row->produk->nama_produk == null)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                </tr>
                    @else
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ tanggal_indonesia($row->created_at, false) }}</td>
                    <td style="text-align: left">{{ $row->produk->nama_produk }}</td>
                    <td>{{ $row->jumlah }}</td>
                    <td style="text-align: right" width="10%">{{ format_uang($row->produk->harga_beli) }}</td>
                    <td style="text-align: right" width="15%">{{ format_uang($row->subtotal) }}</td>
                    <td width="13%">{{ $row->supplier->nama }}</td>
                </tr>
                @endif
            @endforeach
            <tr>
                <td colspan="5"><strong>Total Pembelian</strong></td>
                <td style="text-align: right"><strong>{{ format_uang($jumlah) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
