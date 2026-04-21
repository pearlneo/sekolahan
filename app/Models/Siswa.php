<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Kelas;

class Siswa extends Model
{
    protected $table = 'siswa';
    protected $fillable = [
        'nis', 'gender', 'nama', 'tempat_lahir', 'tgl_lahir',
        'nama_ortu', 'phone_number', 'email', 'alamat', 'kelas_id'
    ];

    protected $dates = ['tgl_lahir'];
    
    public function kelas() {
        return $this->belongsTo(Kelas::class, 'kelas_id');
    }
}
