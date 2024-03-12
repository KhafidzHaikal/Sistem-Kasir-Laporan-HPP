<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Produk;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Models\Penjualan;
use Illuminate\Http\Request;
use App\Models\PenjualanDetail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class KasirController extends Controller
{
    public function index()
    {
        return view('kasir.index_kasir');
    }

    public function data()
    {
        if (auth()->user()->level == 1) {
            $penjualan = Penjualan::with('member')->orderBy('id_penjualan', 'desc')->get();

            return datatables()
                ->of($penjualan)
                ->addIndexColumn()
                ->addColumn('total_item', function ($penjualan) {
                    return format_uang($penjualan->total_item);
                })
                ->addColumn('total_harga', function ($penjualan) {
                    return 'Rp. ' . format_uang($penjualan->total_harga);
                })
                ->addColumn('bayar', function ($penjualan) {
                    return 'Rp. ' . format_uang($penjualan->bayar);
                })
                ->addColumn('tanggal', function ($penjualan) {
                    return tanggal_indonesia($penjualan->created_at, false);
                })
                ->addColumn('kode_member', function ($penjualan) {
                    $member = $penjualan->member->kode_member ?? '';
                    return '<span class="label label-success">' . $member . '</spa>';
                })
                ->editColumn('diskon', function ($penjualan) {
                    return $penjualan->diskon . '%';
                })
                ->editColumn('kasir', function ($penjualan) {
                    return $penjualan->user->name ?? '';
                })
                ->addColumn('aksi', function ($penjualan) {
                    return '
                <div class="btn-group">
                    <button onclick="showDetail(`' . route('penjualan.show', $penjualan->id_penjualan) . '`)" class="btn btn-info btn-flat"><i class="fa fa-eye"></i></button>
                    <button onclick="deleteData(`' . route('penjualan.destroy', $penjualan->id_penjualan) . '`)" class="btn btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
                })
                ->rawColumns(['aksi', 'kode_member'])
                ->make(true);
        } else {
            $penjualan = Penjualan::with('member')
                ->where('id_user', '=', Auth::id())
                ->orderBy('id_penjualan', 'desc')
                ->get();

            return datatables()
                ->of($penjualan)
                ->addIndexColumn()
                ->addColumn('total_item', function ($penjualan) {
                    return format_uang($penjualan->total_item);
                })
                ->addColumn('total_harga', function ($penjualan) {
                    return 'Rp. ' . format_uang($penjualan->total_harga);
                })
                ->addColumn('bayar', function ($penjualan) {
                    return 'Rp. ' . format_uang($penjualan->bayar);
                })
                ->addColumn('tanggal', function ($penjualan) {
                    return tanggal_indonesia($penjualan->created_at, false);
                })
                ->addColumn('kode_member', function ($penjualan) {
                    $member = $penjualan->member->kode_member ?? '';
                    return '<span class="label label-success">' . $member . '</spa>';
                })
                ->editColumn('diskon', function ($penjualan) {
                    return $penjualan->diskon . '%';
                })
                ->editColumn('kasir', function ($penjualan) {
                    return $penjualan->user->name ?? '';
                })
                ->addColumn('aksi', function ($penjualan) {
                    return '
                <div class="btn-group">
                    <button onclick="showDetail(`' . route('kasir.show', $penjualan->id_penjualan) . '`)" class="btn btn-info btn-flat"><i class="fa fa-eye"></i></button>
                </div>
                ';
                })
                ->rawColumns(['aksi', 'kode_member'])
                ->make(true);
        }
    }

    public function show($id)
    {
        $detail = PenjualanDetail::with('produk')->where('id_penjualan', $id)->get();

        return datatables()
            ->of($detail)
            ->addIndexColumn()
            ->addColumn('kode_produk', function ($detail) {
                return '<span class="label label-success">' . $detail->produk->kode_produk . '</span>';
            })
            ->addColumn('nama_produk', function ($detail) {
                return $detail->produk->nama_produk;
            })
            ->addColumn('harga_jual', function ($detail) {
                return 'Rp. ' . format_uang($detail->harga_jual);
            })
            ->addColumn('jumlah', function ($detail) {
                return format_uang($detail->jumlah);
            })
            ->addColumn('subtotal', function ($detail) {
                return 'Rp. ' . format_uang($detail->subtotal);
            })
            ->rawColumns(['kode_produk'])
            ->make(true);
    }

    public function laporan($awal, $akhir)
    {
        $akhir = Carbon::parse($akhir)->endOfDay();
        if (auth()->user()->level == 1) {
            $penjualan = PenjualanDetail::join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                ->select('penjualan_detail.*', 'penjualan.*')
                ->whereBetween('penjualan_detail.created_at', [$awal, $akhir])
                ->get();
        } else {
            $penjualan = PenjualanDetail::join('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
                ->select('penjualan_detail.*', 'penjualan.*')
                ->where('penjualan.id_user', '=', Auth::id())
                ->whereBetween('penjualan_detail.created_at', [$awal, $akhir])
                ->get();
        }

        $total = 0;
        foreach ($penjualan as $item) {
            $total += $item->subtotal;
        }

        $pdf  = PDF::loadView('kasir.laporan', compact('awal', 'akhir', 'penjualan', 'total'));
        return $pdf->inline('Laporan-Penjualan-' . date('Y-m-d-his') . '.pdf');
    }
}
