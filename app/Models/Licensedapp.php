<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Licensedapp extends Model
{
    use HasFactory;

    protected $table = 'licensedapp'; 
    protected $fillable = ['nama_aplikasi', 'keterangan', 'awal_lisensi', 'akhir_lisensi', 'username', 'password', 'created_by']; 
}
