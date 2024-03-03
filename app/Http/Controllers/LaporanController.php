<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use App\Models\Pembelian;
use App\Models\Penjualan;
use App\Models\Pengeluaran;
use App\Models\BackupProduk;
use Illuminate\Http\Request;
use App\Models\PembelianDetail;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\DB;

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
        $pdf  = Pdf::loadView('laporan.pdf', compact('awal', 'akhir', 'data'))->setPaper('a4', 'potrait');

        return $pdf->stream('Laporan-pendapatan-' . date('Y-m-d-his') . '.pdf');
    }

    public function labaPdf($awal, $akhir)
    {
        $produk = Produk::join('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')->select('produk.*', 'pembelian_detail.*')->get();
        $pembelian = PembelianDetail::join('produk', 'pembelian_detail.id_produk', '=', 'produk.id_produk')->select('produk.*', 'pembelian_detail.*')->whereBetween('pembelian_detail.created_at', [$awal, $akhir])->get();

        $jumlah = $produk->sum('total_harga_beli');

        $pdf = PDF::loadView('laporan.laba_rugi', compact('awal', 'akhir', 'pembelian', 'jumlah'))->setPaper('a4');
        return $pdf->stream('Laporan-laba_Rugi-' . date('Y-m-d-his') . '.pdf');
    }

    public function hpp($tanggal_awal, $tanggal_akhir)
    {
        /** Kondisi Bulan dengan hari kurang dari 1 bulan */

        // if ($tanggal_awal && $tanggal_akhir && Carbon::parse($tanggal_awal)->diffInDays(Carbon::parse($tanggal_akhir)) <= 31) {
        //     $results = BackupProduk::whereBetween('created_at', [$tanggal_awal, $tanggal_akhir])->get();
        // } else {
        $results = DB::table('backup_produks')
            ->join('produk', 'backup_produks.id_produk', '=', 'produk.id_produk')
            ->select('backup_produks.id_produk', 'backup_produks.nama_produk', 'backup_produks.satuan', 'backup_produks.harga_beli', 'backup_produks.stok_akhir', DB::raw('sum(backup_produks.stok_awal) as stok_awal'), DB::raw('sum(backup_produks.stok_akhir) as stok_akhir'), DB::raw('sum(backup_produks.stok_belanja) as stok_belanja'), DB::raw('sum(backup_produks.total_belanja) as total_belanja'))
            ->groupBy('backup_produks.id_produk')
            ->whereBetween('backup_produks.created_at', [$tanggal_awal, $tanggal_akhir])
            ->distinct('backup_produks.id_produk')
            ->get();
        // }

        $pdf = PDF::loadView('laporan.hpp', compact('tanggal_awal', 'tanggal_akhir', 'results'))->setPaper('a4', 'landscape');
        return $pdf->stream('Laporan-HPP-' . date('Y-m-d-his') . '.pdf');
    }
}
