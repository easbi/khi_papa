<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assigntim extends Model
{
    use HasFactory;
    protected $table = 'master_assign_anggota';
    protected $fillable=[
        'tim_kerja_id',
        'project_id',
        'kegiatan_utama_id',
        'anggota_nip',
        'created_by'
    ];
}

