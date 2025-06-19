<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LogPerangkat extends Model
{
    use HasFactory;

    protected $table = 'log_perangkats';

    protected $fillable = [
        'id_opd',
        'id_perangkat',
        'tahun',
        'bulan',
        'tanggal',
        'jam',
        'menit',
        'detik',
        'karakter_unik',
        'keseluruhan',
    ];
}
