<?php

namespace Database\Seeders;

use App\Models\Produk;
use App\Models\Kategori;
use App\Models\Supplier;
use Illuminate\Database\Seeder;

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

        Produk::create([
            'id_produk' => 1,
            'id_kategori' => 1,
            'kode_produk' => 'P00001',
            'nama_produk' => 'Fanta 1.5l',
            'merk' => 'Fanta',
            'satuan' => 'pcs',
            'harga_beli' => 12000,
            'diskon' => 0,
            'harga_jual' => 15000,
            'stok' => 50,
            'stok_lama' => 50,
        ]);

        Produk::create([
            'id_produk' => 2,
            'id_kategori' => 1,
            'kode_produk' => 'P00002',
            'nama_produk' => 'Sprite 1.5l',
            'merk' => 'Sprite',
            'satuan' => 'pcs',
            'harga_beli' => 12000,
            'diskon' => 0,
            'harga_jual' => 15000,
            'stok' => 50,
            'stok_lama' => 50,
        ]);

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
