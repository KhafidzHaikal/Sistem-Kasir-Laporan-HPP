<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(10)->create();
        Kategori::create([
            'id_kategori' => 1,
            'nama_kategori' => "Minuman",
        ]);

/////////////////////////////////////////////////////////////////////////
        // $json = Storage::disk('local')->get('/json/produk.json');
        // $produks = json_decode($json, true);

        // foreach ($produks as $produk) {
        //     Produk::query()->updateOrCreate([
        //         'id_produk' => $produk['id_produk'],
        //         'id_kategori' => $produk['id_kategori'],
        //         'kode_produk' => $produk['kode_produk'],
        //         'nama_produk' => $produk['nama_produk'],
        //         'satuan' => $produk['satuan'],
        //         'harga_beli' => $produk['harga_beli'],
        //         'diskon' => $produk['diskon'],
        //         'harga_jual' => $produk['harga_jual'],
        //         'stok' => $produk['stok'],
        //         'stok_lama' => $produk['stok_lama'],
        //         'tanggal_expire' => date(now()),
        //         'created_at' => $produk['created_at'],
        //         'updated_at' => $produk['update_at']
        //     ]);
        // }
////////////////////////////////////////////////////////////////////////////////////
        Supplier::create([
            'id_supplier' => 1,
            'nama' => 'Indomaret',
            'alamat' => 'Bulak',
            'telepon' => '081293058325',
            'email' => 'indomaret@gmail.com',
        ]);

        $this->call([
            SettingTableSeeder::class,
            UserTableSeeder::class,
        ]);
    }
}
