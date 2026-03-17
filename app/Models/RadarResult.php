<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadarResult extends Model
{
    protected $fillable = [
        'user_id',
        'id_compra',
        'numero_controle_pncp',
        'orgao',
        'uf',
        'municipio',
        'modalidade',
        'data_publicacao',
        'data_encerramento',
        'objeto',
        'payload',
    ];

    protected $casts = [
        'data_publicacao' => 'datetime',
        'data_encerramento' => 'datetime',
        'payload' => 'array',
    ];
}
