<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Siswa;
use App\Models\Jadwal;

class Kelas extends Model
{
    protected $table = 'kelas';
    protected $fillable = ['kode_kelas', 'nama_kelas'];

    public function siswas() {
        return $this->hasMany(Siswa::class, 'kelas_id');
    }

    public function jadwals() {
        return $this->hasMany(Jadwal::class, 'kelas_id');
    }
}
