<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Jadwal;

class Mapel extends Model
{
    protected $table = 'mapel';
    protected $fillable = [
        'kode_mapel',
        'nama_mapel'
    ];

    public function jadwals() {
        return $this->hasMany(Jadwal::class, 'mapel_id');
    }
}
