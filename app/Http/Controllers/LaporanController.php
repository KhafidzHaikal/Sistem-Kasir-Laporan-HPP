<?php

namespace App\Http\Controllers;

use Carbon\Carbon;

use App\Models\Produk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Pengeluaran;
use Illuminate\Http\Request;
use App\Models\PembelianDetail;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf as Barpdf;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $tanggalAwal = date('Y-m-d', mktime(0, 0, 0, date('m'), 1, date('Y')));
        $tanggalAkhir = date('Y-m-d');

        if ($request->has('tanggal_awal') && $request->tanggal_awal != "" && $request->has('tanggal_akhir') && $request->tanggal_akhir) {
            $tanggalAwal = $request->tanggal_awal;
            $tanggalAkhir = $request->tanggal_akhir;
        }

        return view('laporan.index', compact('tanggalAwal', 'tanggalAkhir'));
    }

    public function getData($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['penjualan'] = format_uang($total_penjualan);
            $row['pembelian'] = format_uang($total_pembelian);
            $row['pengeluaran'] = format_uang($total_pengeluaran);
            $row['pendapatan'] = format_uang($pendapatan);

            $data[] = $row;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '',
            'pembelian' => '',
            'pengeluaran' => 'Total Pendapatan',
            'pendapatan' => format_uang($total_pendapatan),
        ];

        // dd($data);
        return $data;
    }

    public function data($awal, $akhir)
    {
        $data = $this->getData($awal, $akhir);

        return datatables()
            ->of($data)
            ->make(true);
    }

    public function exportPDF($awal, $akhir)
    {
        $no = 1;
        $data = array();
        $pendapatan = 0;
        $total_pendapatan = 0;

        while (strtotime($awal) <= strtotime($akhir)) {
            $tanggal = $awal;
            $awal = date('Y-m-d', strtotime("+1 day", strtotime($awal)));

            $total_penjualan = Penjualan::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pembelian = Pembelian::where('created_at', 'LIKE', "%$tanggal%")->sum('bayar');
            $total_pengeluaran = Pengeluaran::where('created_at', 'LIKE', "%$tanggal%")->sum('nominal');

            $pendapatan = $total_penjualan - $total_pembelian - $total_pengeluaran;
            $total_pendapatan += $pendapatan;

            $row = array();
            $row['DT_RowIndex'] = $no++;
            $row['tanggal'] = tanggal_indonesia($tanggal, false);
            $row['penjualan'] = format_uang($total_penjualan);
            $row['pembelian'] = format_uang($total_pembelian);
            $row['pengeluaran'] = format_uang($total_pengeluaran);
            $row['pendapatan'] = format_uang($pendapatan);

            $data[] = $row;
        }

        $data[] = [
            'DT_RowIndex' => '',
            'tanggal' => '',
            'penjualan' => '',
            'pembelian' => '',
            'pengeluaran' => 'Total Pendapatan',
            'pendapatan' => format_uang($total_pendapatan),
        ];

        $data = collect($data)->map(function ($item) {
            return (object) $item;
        });
        $pdf  = Barpdf::loadView('laporan.pdf', compact('awal', 'akhir', 'data'))->setPaper('a4', 'potrait');

        return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-his') . '.pdf');
    }

    public function labaPdf($awal, $akhir)
    {
        $akhir = Carbon::parse($akhir)->endOfDay();
        $results = DB::table('backup_produks')
            ->join('produk', 'backup_produks.id_produk', '=', 'produk.id_produk')
            ->leftJoin('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')
            ->whereBetween('backup_produks.created_at', [$awal, $akhir])
            ->select(
                'backup_produks.id_produk',
                'backup_produks.nama_produk',
                'backup_produks.satuan',
                'backup_produks.harga_beli',
                DB::raw('(select sum(jumlah) from pembelian_detail where pembelian_detail.id_produk = backup_produks.id_produk and pembelian_detail.created_at between "'.$awal.'" and "'.$akhir.'" group by pembelian_detail.id_produk) as stok_belanja'),
                'backup_produks.created_at',
                'produk.harga_jual',
            )
            ->groupBy('backup_produks.id_produk')
            ->get();
        // dd($results);

        $total_laba_rugi = 0;

        foreach ($results as $row) {
            $total_laba_rugi += ($row->harga_jual * $row->stok_belanja) - ($row->harga_beli * $row->stok_belanja);
        }

        // dd($total_laba_rugi);
        $pdf = PDF::loadView('laporan.laba_rugi', compact('awal', 'akhir', 'results', 'total_laba_rugi'))->setPaper('a4');
        return $pdf->inline('Laporan-laba_Rugi-' . date('Y-m-d-his') . '.pdf');
    }

    public function hpp($tanggal_awal, $tanggal_akhir)
    {
        $tanggal_akhir = Carbon::parse($tanggal_akhir)->endOfDay();
        $results = DB::table('backup_produks')
            ->join('produk', 'backup_produks.id_produk', '=', 'produk.id_produk')
            ->leftJoin('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')
            ->whereBetween('backup_produks.created_at', [$tanggal_awal, $tanggal_akhir])
            ->select(
                'backup_produks.id_produk',
                'backup_produks.nama_produk',
                'backup_produks.satuan',
                'backup_produks.harga_beli',
                DB::raw("(select stok_awal from backup_produks as bp where bp.id_produk = backup_produks.id_produk and bp.created_at >= '$tanggal_awal' order by created_at asc limit 1) as stok_awal"),
                DB::raw("(select stok_akhir from backup_produks as bp where bp.id_produk = backup_produks.id_produk and bp.created_at <= '$tanggal_akhir' order by created_at desc limit 1) as stok_akhir"),
                DB::raw('(select sum(jumlah) from pembelian_detail where pembelian_detail.id_produk = backup_produks.id_produk and pembelian_detail.created_at between "'.$tanggal_awal.'" and "'.$tanggal_akhir.'" group by pembelian_detail.id_produk) as stok_belanja'),
            )
            ->groupBy('backup_produks.id_produk')
            ->get();

        // dd($results);
        $totalValue = 0;
        $totalAwal = 0;
        $totalBeli = 0;
        $totalAkhir = 0;

        foreach ($results as $result) {
            $totalValue += (($result->harga_beli * $result->stok_awal) + ($result->stok_belanja * $result->harga_beli)) - ($result->harga_beli * $result->stok_akhir);
            $totalAwal += $result->harga_beli * $result->stok_awal;
            $totalBeli += $result->stok_belanja * $result->harga_beli;
            $totalAkhir += $result->harga_beli * $result->stok_akhir;
        }

        $pdf = PDF::loadView('laporan.hpp', compact('tanggal_awal', 'tanggal_akhir', 'results', 'totalValue', 'totalAwal', 'totalBeli', 'totalAkhir'))->setPaper('a4')->setOrientation('landscape');
        return $pdf->inline('Laporan-HPP-' . date('Y-m-d-his') . '.pdf');
    }
}
