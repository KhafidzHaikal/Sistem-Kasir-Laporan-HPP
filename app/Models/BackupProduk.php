<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BackupProduk extends Model
{
    use HasFactory;

    protected $table = 'backup_produks';
    protected $primaryKey = 'id';
    protected $guarded = [];
}
