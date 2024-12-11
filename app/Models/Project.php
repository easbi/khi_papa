<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    use HasFactory;
    protected $table = 'master_project';
    protected $fillable=[
        'tim_kerja_id',
        'nama_project',
        'created_by'
    ];
}
