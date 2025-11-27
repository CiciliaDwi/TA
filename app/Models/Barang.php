<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    use HasFactory;

    protected $table = 'barang';

    protected $primaryKey = 'KodeBarang';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = true;

    protected $fillable = [
        'KodeBarang',
        'Barcode',
        'Nama',
        'HargaJual',
        'Stok',
        'KodeKategori',
    ];

    public function kurangiStok($jumlah)
    {
        $this->Stok -= $jumlah;
        $this->save();
    }

    public function nota_jual()
    {
        return $this->belongsToMany(Nota_Jual::class, 'nota_jual_detil', 'KodeBarang', 'NoNota')
            ->withPivot('Jumlah', 'Harga', 'Total');
    }

    public function notaJualDetil()
    {
        return $this->hasMany(Nota_Jual_Detil::class, 'KodeBarang', 'KodeBarang');
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'KodeKategori', 'KodeKategori');
    }
}
