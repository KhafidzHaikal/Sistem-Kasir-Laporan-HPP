<?php

namespace App\Console;

use App\Models\Produk;
use App\Models\BackupProduk;
use Illuminate\Support\Facades\DB;
use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        // "App\Console\Commands\DBProduk"
    ];

    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        $schedule->call(function () {
            DB::table('produk')->update(['stok_lama' => DB::raw('stok')]);
        })->everyMinute();
        $schedule->call(function () {
            $results = DB::table('produk')
                ->select('produk.id_produk', 'produk.nama_produk', 'produk.satuan', 'produk.stok', 'produk.harga_beli', 'produk.stok_lama', DB::raw('sum(pembelian_detail.jumlah) as total_jumlah'), DB::raw('sum(pembelian_detail.subtotal) as total_harga'))
                ->join('pembelian_detail', 'produk.id_produk', '=', 'pembelian_detail.id_produk')
                ->groupBy('produk.id_produk', 'produk.nama_produk', 'produk.satuan', 'produk.stok', 'produk.harga_beli', 'produk.stok_lama')
                ->get();

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
                $backup->created_at = date(now());
                $backup->updated_at = date(now());
                // dd($backup);
                $backup->save();
            })->toArray();
        })->everyMinute();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
