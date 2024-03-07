<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Barryvdh\Snappy\Facades\SnappyPdf as PDF;
use App\Models\Produk;
use App\Models\Kategori;
use App\Models\BackupProduk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $kategori = Kategori::all()->pluck('nama_kategori', 'id_kategori');
        $buttonClass = '';
        $buttonAttributes = '';

        $now = Carbon::now();
        $backups = BackupProduk::select('created_at')->get();

        foreach ($backups as $backup) {
            $backupDate = Carbon::parse($backup->created_at);

            if ($backupDate->month == $now->month) {
                $buttonClass = 'disabled';
                break;
            }
        }

        $buttonAttributes = $buttonClass ? " disabled" : "";

        return view('produk.index', compact('kategori' , 'buttonAttributes', 'buttonClass'));
    }

    public function data()
    {
        $produk = Produk::leftJoin('kategori', 'kategori.id_kategori', 'produk.id_kategori')
            ->select('produk.*', 'nama_kategori')
            ->orderBy('kode_produk', 'asc')
            ->get();

        return datatables()
            ->of($produk)
            ->addIndexColumn()
            ->addColumn('select_all', function ($produk) {
                return '
                    <input type="checkbox" name="id_produk[]" value="' . $produk->id_produk . '">
                ';
            })
            ->addColumn('kode_produk', function ($produk) {
                return '<span class="label label-success">' . $produk->kode_produk . '</span>';
            })
            ->addColumn('harga_beli', function ($produk) {
                return format_uang($produk->harga_beli);
            })
            ->addColumn('harga_jual', function ($produk) {
                return format_uang($produk->harga_jual);
            })
            ->addColumn('stok', function ($produk) {
                return format_uang($produk->stok);
            })
            ->addColumn('aksi', function ($produk) {
                return '
                <div class="btn-group">
                    <button type="button" onclick="editForm(`' . route('produk.update', $produk->id_produk) . '`)" class="btn btn-xs btn-info btn-flat"><i class="fa fa-pencil"></i></button>
                    <button type="button" onclick="deleteData(`' . route('produk.destroy', $produk->id_produk) . '`)" class="btn btn-xs btn-danger btn-flat"><i class="fa fa-trash"></i></button>
                </div>
                ';
            })
            ->rawColumns(['aksi', 'kode_produk', 'select_all'])
            ->make(true);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $produk = Produk::latest()->first() ?? new Produk();
        $request['kode_produk'] = 'P' . tambah_nol_didepan((int)$produk->id_produk + 1, 6);
        $request['stok_lama'] = $request->stok;

        $produk = Produk::create($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $produk = Produk::find($id);

        return response()->json($produk);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $produk = Produk::find($id);
        $produk->update($request->all());

        return response()->json('Data berhasil disimpan', 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $produk = Produk::find($id);
        $produk->delete();

        return response(null, 204);
    }

    public function deleteSelected(Request $request)
    {
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $produk->delete();
        }

        return response(null, 204);
    }

    public function cetakBarcode(Request $request)
    {
        $dataproduk = array();
        foreach ($request->id_produk as $id) {
            $produk = Produk::find($id);
            $dataproduk[] = $produk;
        }

        $no  = 1;
        $pdf = PDF::loadView('produk.barcode', compact('dataproduk', 'no'));
        $pdf->setPaper('a4', 'potrait');
        return $pdf->inline('produk.pdf');
    }

    public function pdf($awal, $akhir)
    {
        $produk = Produk::leftJoin('pembelian_detail', 'pembelian_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('penjualan_detail', 'penjualan_detail.id_produk', '=', 'produk.id_produk')
            ->leftJoin('penjualan', 'penjualan_detail.id_penjualan', '=', 'penjualan.id_penjualan')
            ->leftJoin('users', 'penjualan.id_user', '=', 'users.id')
            ->select('produk.id_produk', 'produk.nama_produk', 'produk.created_at', 'produk.stok', 'produk.stok_lama', 'produk.harga_beli', 'pembelian_detail.id_pembelian_detail', 'pembelian_detail.jumlah', 'penjualan_detail.id_penjualan_detail', DB::raw('sum(penjualan_detail.jumlah) as stok_penjualan'), 'users.name')
            ->whereBetween('produk.created_at', [$awal, $akhir])
            ->groupBy('produk.id_produk')
            ->get();
        // dd($produk);

        $total_penjualan = 0;

        foreach ($produk as $item) {
            $total_penjualan += $item->harga_beli * $item->stok_penjualan;
        }

        $pdf = PDF::loadView('produk.pdf', compact('awal', 'akhir', 'produk', 'total_penjualan'))->setPaper('a4');
        return $pdf->inline('Laporan-Produk-' . date('Y-m-d-his') . '.pdf');
    }
}
