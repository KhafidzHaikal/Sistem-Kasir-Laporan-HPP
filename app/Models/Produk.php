<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    protected $primaryKey = 'id_produk';
    protected $guarded = [];

    public function getHargaTotalAttribute()
    {
        return (($this->stok) * ($this->harga_beli));
    }

    public function getTotalHargaBeliAttribute()
    {
        return (($this->stok) * ($this->harga_beli));
    }

    public function getLabaRugiAttribute()
    {
        return (($this->harga_jual) - ($this->harga_beli));
    }

    public function penjualan_detail()
    {
        return $this->belongsTo(PenjualanDetail::class, 'id_penjualan_detail', 'id_penjualan_detail');
    }

    public function pembelian_detail()
    {
        return $this->belongsToMany(PembelianDetail::class, 'pembelian_detail', 'id_produk', 'id_pembelian_detail')
                    ->withPivot('jumlah', 'subtotal')
                    ->withTimestamps();
    }
}
