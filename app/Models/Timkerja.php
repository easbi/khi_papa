<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Timkerja extends Model
{
    use HasFactory;
    protected $table = 'master_tim_kerja';
    protected $fillable=[
        'nip_ketua_tim',
        'nama_tim_kerja',
        'tahun_kerja',
        'created_by'
    ];
}
