<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategori';

    protected $primaryKey = 'KodeKategori';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = [
        'KodeKategori',
        'Nama',
    ];

    public function barang()
    {
        return $this->hasMany(Barang::class, 'KodeKategori', 'KodeKategori');
    }
}
