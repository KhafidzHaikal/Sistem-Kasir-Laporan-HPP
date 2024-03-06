<?php

namespace App\Http\Controllers;

use App\Models\BackupProduk;
use Illuminate\Http\Request;
use App\Models\backup_produk;
use Illuminate\Support\Facades\DB;
use App\Http\Requests\Storebackup_produkRequest;
use App\Http\Requests\Updatebackup_produkRequest;

class BackupProdukController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
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
     * @param  \App\Http\Requests\Storebackup_produkRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store()
    {    
        $results = DB::table('produk')
            ->select('produk.id_produk', 'produk.nama_produk', 'produk.satuan', 'produk.stok', 'produk.harga_beli', 'produk.stok_lama', 'produk.tanggal_expire', DB::raw('coalesce(sum(pembelian_detail.jumlah), 0) as total_jumlah'), DB::raw('coalesce(sum(pembelian_detail.subtotal), 0) as total_harga'))
            ->leftJoin('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')
            ->groupBy('produk.id_produk')
            ->get();

        // dd($results);
        $results->map(function ($results) {
            $backup = new BackupProduk();
            $backup->id_produk = $results->id_produk;
            $backup->nama_produk = $results->nama_produk;
            $backup->satuan = $results->satuan;
            $backup->harga_beli = $results->harga_beli;
            $backup->stok_awal = $results->stok_lama;
            $backup->stok_akhir = $results->stok;
            $backup->stok_belanja = $results->total_jumlah;
            $backup->total_belanja = $results->total_harga;
            $backup->tanggal_expire = $results->tanggal_expire;
            $backup->created_at = date(now());
            $backup->updated_at = date(now());
            $backup->save();
        });

        DB::table('produk')->update(['stok_lama' => DB::raw('stok')]);
        return redirect()->back()->withToast('success', 'Data Berhasil di Backup');
    }
}
