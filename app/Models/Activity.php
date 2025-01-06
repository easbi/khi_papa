<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

class Activity extends Model
{
    use HasFactory;

    protected $table = 'daily_activity';
    protected $fillable=[
        'nip',
        'wfo_wfh',
        'tim_kerja_id',
        'project_id',
        'kegiatan_utama_id',
        'kegiatan',
        'keterangan',
        'jenis_kegiatan',
        'bukti',
        'satuan',
        'kuantitas',
        'is_done',
        'link',
        'berkas',
        'tgl',
        'tgl_selesai',
        'created_by'
    ];
}
