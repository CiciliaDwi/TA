<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pelanggan extends Model
{
    use HasFactory;

    protected $table = 'pelanggan';
    protected $primaryKey = 'KodePelanggan';
    public $incrementing = true;
    protected $keyType = 'int';
    public $timestamps = true;
    
    protected $fillable = [
        'KodePelanggan',
        'Nama',
        'Alamat',
        'Telepon'
    ];

    public function nota_jual()
    {
        return $this->hasMany(Nota_Jual::class, 'KodePelanggan', 'KodePelanggan');
    }
}
