<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PembelianDetail extends Model
{
    use HasFactory;

    protected $table = 'pembelian_detail';
    protected $primaryKey = 'id_pembelian_detail';
    protected $guarded = [];

    public function produk()
    {
        return $this->hasOne(Produk::class, 'id_produk', 'id_produk');
    }

    public function supplier()
    {
        return $this->hasOne(Supplier::class, 'id_supplier', 'id_supplier');
    }

    public function pembelian()
    {
        return $this->hasOne(Pembelian::class, 'id_pembelian', 'id_pembelian');
    }

    public function getJumlahSemuaAttribute() {
        $jumlah = 0;
        return (
            $jumlah += ($this->subtotal)  
        );
    }

    public function scopeGetTotalSubtotal() 
    {
        
    }

}
