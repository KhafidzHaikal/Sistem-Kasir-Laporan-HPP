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
        Kategori::create([
            'id_kategori' => 2,
            'nama_kategori' => "Makanan",
        ]);

        Produk::create([
            'id_produk' => 1,
            'id_kategori' => 1,
            'kode_produk' => 'P00001',
            'nama_produk' => 'Fanta 1500 ml',
            'satuan' => 'pcs',
            'harga_beli' => 12000,
            'diskon' => 0,
            'harga_jual' => 15000,
            'stok' => 40,
            'stok_lama' => 40,
            'tanggal_expire' => now()->addDays(14),
            'created_at' => date(now()),
            'updated_at' => date(now())
        ]);

        Produk::create([
            'id_produk' => 2,
            'id_kategori' => 1,
            'kode_produk' => 'P00002',
            'nama_produk' => 'Sprite 1500 ml',
            'satuan' => 'pcs',
            'harga_beli' => 13000,
            'diskon' => 0,
            'harga_jual' => 14000,
            'stok' => 35,
            'stok_lama' => 35,
            'tanggal_expire' => now()->addDays(14),
            'created_at' => date(now()),
            'updated_at' => date(now())
        ]);

        Produk::create([
            'id_produk' => 3,
            'id_kategori' => 2,
            'kode_produk' => 'P00003',
            'nama_produk' => 'Lays 600 gr',
            'satuan' => 'pcs',
            'harga_beli' => 4000,
            'diskon' => 0,
            'harga_jual' => 6000,
            'stok' => 30,
            'stok_lama' => 30,
            'tanggal_expire' => now()->addDays(14),
            'created_at' => date(now()),
            'updated_at' => date(now())
        ]);

        Produk::create([
            'id_produk' => 4,
            'id_kategori' => 2,
            'kode_produk' => 'P00004',
            'nama_produk' => 'Chitato 600 gr',
            'satuan' => 'pcs',
            'harga_beli' => 3500,
            'diskon' => 0,
            'harga_jual' => 5000,
            'stok' => 37,
            'stok_lama' => 37,
            'tanggal_expire' => now()->addDays(14),
            'created_at' => date(now()),
            'updated_at' => date(now())
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
        //         'tanggal_expire' => now()->addDays(14),
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
