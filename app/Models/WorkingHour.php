<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WorkingHour extends Model
{
    protected $fillable = [
        'nama',
        'jam_masuk',
        'jam_pulang',
    ];

    // Tidak perlu casting karena kolom sudah tipe TIME di database
}
