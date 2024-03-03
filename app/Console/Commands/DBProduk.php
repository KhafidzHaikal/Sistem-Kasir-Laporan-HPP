<?php

namespace App\Console\Commands;

use App\Models\BackupProduk;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class DBProduk extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:produk';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Data Backup Produk ';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $results = DB::table('produk')
            ->select('produk.id_produk', 'produk.nama_produk', 'produk.satuan', 'produk.stok', 'produk.harga_beli', 'produk.stok_lama', DB::raw('sum(pembelian_detail.jumlah) as total_jumlah'), DB::raw('sum(pembelian_detail.subtotal) as total_harga'))
            ->join('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')
            ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.satuan', 'produk.stok', 'produk.harga_beli', 'produk.stok_lama')
            ->get();

        $produk = $results->map(function ($results) {
            $backup = new BackupProduk();
            $backup->id_produk = $results->id_produk;
            $backup->nama_produk = $results->nama_produk;
            $backup->satuan = $results->satuan;
            $backup->harga_beli = $results->harga_beli;
            $backup->stok_awal = $results->stok_lama;
            $backup->stok_akhir = $results->stok;
            $backup->stok_belanja = $results->total_jumlah;
            $backup->total_belanja = $results->total_harga;
            $backup->created_at = date(now());
            $backup->updated_at = date(now());
            // dd($backup);
            $backup->save();
        });

        exec($produk);
    }
}
