<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Nota_Jual extends Model
{
    use HasFactory;

    protected $table = 'nota_jual';
    protected $primaryKey = 'NoNota';
    public $incrementing = false;
    public $timestamps = false;

    const PAYMENT_METHODS = [
        'cash' => 'Tunai',
        'debit' => 'Debit',
        'kredit' => 'Kredit'
    ];

    protected $fillable = [
        'NoNota',
        'Tanggal',
        'KodePelanggan',
        'id_pegawai', 
        'metode_pembayaran'
    ];
    

    public function pegawai()
    {
        return $this->belongsTo(User::class, 'id_pegawai', 'id');
    }
    // public function pelanggan(){
    //     return $this->belongsTo(Pelanggan::class, 'KodePelanggan', 'KodePelanggan');
    // }
    public function detil()
    {
        return $this->hasMany(Nota_Jual_Detil::class, 'NoNota', 'NoNota');
    }

    public function barang()
    {
        return $this->belongsToMany(Barang::class, 'nota_jual_detil', 'NoNota', 'KodeBarang')
            ->withPivot(['Harga', 'Jumlah'])
            ->using(Nota_Jual_Detil::class);
    }
}
