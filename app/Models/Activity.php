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
        'kegiatan',
        'bukti',
        'satuan',
        'kuantitas',
        'is_done',
        'tgl',
        'tgl_selesai',
        'created_by'
    ];
}
