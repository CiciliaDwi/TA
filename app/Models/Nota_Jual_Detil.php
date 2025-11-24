<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Barang;
use App\Models\Nota_Jual;

class Nota_Jual_Detil extends Model
{
    use HasFactory;
    protected $table = 'nota_jual_detil';
    // protected $incrementing = false;
    public $timestamps = true;
    protected $fillable = [
        'NoNota',
        'KodeBarang',
        'Harga',
        'Jumlah',
        'Total'
    ];


    public function barang(){
        return $this->belongsTo(Barang::class, 'KodeBarang', 'KodeBarang');
    }
    public function nota_jual(){
        return $this->belongsTo(Nota_Jual::class, 'NoNota', 'NoNota');
    }
}
