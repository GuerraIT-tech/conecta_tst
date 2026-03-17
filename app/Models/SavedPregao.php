<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SavedPregao extends Model
{
    protected $table = 'saved_pregoes';

    protected $fillable = [
        'user_id',
        'id_compra',
        'numero_controle_pncp',
        'orgao',
        'uf',
        'municipio',
        'modalidade',
        'modo_disputa',
        'processo',
        'srp',
        'valor_estimado',
        'data_publicacao',
        'data_abertura',
        'data_encerramento',
        'objeto',
        'payload',
    ];

    protected $casts = [
        'srp' => 'boolean',
        'valor_estimado' => 'decimal:2',
        'data_publicacao' => 'datetime',
        'data_abertura' => 'datetime',
        'data_encerramento' => 'datetime',
        'payload' => 'array',
    ];
}
