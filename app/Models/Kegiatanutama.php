<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kegiatanutama extends Model
{
    use HasFactory;
    protected $table = 'master_kegiatan_utama';
    protected $fillable=[
        'tim_kerja_id',
        'project_id',
        'nama_kegiatan_utama',
        'weight',
        'periode_awal',
        'periode_akhir',
        'created_by'
    ];
}
