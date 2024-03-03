<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Laporan Pendapatan</title>

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

        article,
        aside,
        figcaption,
        figure,
        footer,
        header,
        hgroup,
        main,
        nav,
        section {
            display: block;
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
            line-height: 2;
            color: #000000;
            text-align: center;
        }

        td,
        th {
            border: 1px solid #000000;
            padding: 0.5rem;
            text-align: center;
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

        .header {
            text-align: left;
            margin-bottom: 3em;

        }

        .header .left {
            position: relative;
            left: 0;
        }

        .header .right {
            position: absolute;
            right: 0;
            width: 58%;
            top: 11.5em;
        }

        .kop-surat .pemkab {
            position: absolute;
        }

        .kop-surat .puskesmas {
            position: absolute;
            top: 0;
            right: 0;
        }

        .kop-surat div {
            line-height: 70%;
        }

        .kop-surat div p {
            font-weight: 600;
            font-size: 12px;
        }

        .kop-surat div h3 {
            font-weight: 400;
            font-family: 'Times New Roman', Times, serif;
            font-size: 13px;
        }

        .kop-surat div h1 {
            letter-spacing: 0.1rem;
            font-weight: 800;
            font-size: 17px;
            margin-bottom: 1rem;
        }

        .line-2 {
            border-top: 1px solid black;
            margin-bottom: 0.1rem;
        }

        .line-3 {
            border-top: 3px solid black;
            margin-bottom: 1rem;
        }
    </style>
</head>

<body>
    <h3 class="text-center">Laporan Pendapatan</h3>
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
                <th>Penjualan</th>
                <th>Pembelian</th>
                <th>Pengeluaran</th>
                <th>Pendapatan</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($data as $row)
                <tr>
                    <td>{{ $row->DT_RowIndex }}</td>
                    <td>{{ $row->tanggal }}</td>
                    <td>{{ $row->penjualan }}</td>
                    <td>{{ $row->pembelian }}</td>
                    <td>{{ $row->pengeluaran }}</td>
                    <td>{{ $row->pendapatan }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>

</html>
