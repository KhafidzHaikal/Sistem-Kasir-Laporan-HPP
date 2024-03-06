<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Stok Produk</title>

    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@100;200;300;400;500;600;700;800;900&family=Nunito+Sans:ital,wght@0,200;0,300;0,400;0,600;0,700;0,800;0,900;1,200;1,300;1,400;1,600;1,700;1,800;1,900&family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900;1,1000&family=Roboto:wght@100;300;400;500;700;900&display=swap');

        *,
        *::before,
        *::after {
            box-sizing: border-box;
        }

        html {
            font-family: 'Roboto', sans-serif;
            line-height: normal;
            -webkit-text-size-adjust: 100%;
            -webkit-tap-highlight-color: rgba(0, 0, 0, 0);
        }

        h2 {
            font-size: 14px;
            font-family: 'Nunito Sans', sans-serif;
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
    <h3 class="text-center">Laporan Stok Produk</h3>
    <h4 class="text-center">
        Tanggal {{ tanggal_indonesia($awal, false) }}
        s/d
        Tanggal {{ tanggal_indonesia($akhir, false) }}
    </h4>

    <table class="table table-striped">
        <thead>
            <tr>
                <th width="5%">No</th>
                <th>Tanggal</th>
                <th>Nama Barang</th>
                <th width="7%">Stok Awal</th>
                <th width="10%">Penjualan</th>
                <th width="10%">Stok Sekarang</th>
                <th>Harga Satuan</th>
                <th>Total</th>
                <th>Kasir</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($produk as $row)
                <tr>
                    <td>{{ $loop->iteration }}</td>
                    <td>{{ tanggal_indonesia($row->created_at, false) }}</td>
                    <td>{{ $row->nama_produk }}</td>
                    <td>{{ $row->stok_lama }}</td>
                    <td>
                        @if (is_null($row->id_penjualan_detail))
                            0
                        @else
                            {{ $row->stok_penjualan }}
                        @endif
                    </td>
                    <td>{{ $row->stok }}</td>
                    <td style="text-align: right">{{ format_uang($row->harga_beli) }}</td>
                    <td style="text-align: right">{{ format_uang($row->harga_beli * $row->stok_penjualan) }}</td>
                    <td>{{ $row->name }}</td>
                </tr>
            @endforeach
            <tr>
                <td colspan="7"><strong>Total Stok</strong></td>
                <td style="text-align: right"><strong>{{ format_uang($total_penjualan) }}</strong></td>
                <td></td>
            </tr>
        </tbody>
    </table>
</body>

</html>
