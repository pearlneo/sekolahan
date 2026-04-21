<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Jadwal;

class Guru extends Model
{
    protected $table = 'guru';
    protected $fillable = [
        'user_id', 'nip', 'nama', 'tempat_lahir', 'tgl_lahir',
        'gender', 'phone_number', 'email', 'alamat', 'pendidikan'
    ];

    protected $dates = ['tgl_lahir'];
    
    public function user() {
        return $this->belongsTo(User::class, 'user_id');
    }
    
    public function jadwals() {
        return $this->hasMany(Jadwal::class, 'guru_id');
    }
}
