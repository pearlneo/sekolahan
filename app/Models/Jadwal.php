<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;
use App\Models\Mapel;
use App\Models\Guru;

class Jadwal extends Model
{
    protected $table = 'jadwal';
    protected $fillable = [
        'kelas_id', 'mapel_id', 'guru_id', 'hari', 'jam_pelajaran'
    ];

    public function kelas() {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }

    public function mapel() {
        return $this->belongsTo(Mapel::class, 'mapel_id');
    }

    public function guru() {
        return $this->belongsTo(Guru::class, 'guru_id');
    }
}
