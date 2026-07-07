<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PublicLink extends Model
{
    use HasFactory;

    protected $table = 'public_links';

    protected $fillable = [
        'token',
        'title',
        'activity_ids',
        'created_by_nip',
    ];

    protected $casts = [
        'activity_ids' => 'array',
    ];
}
