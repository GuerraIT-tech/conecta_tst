<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RadarPreference extends Model
{
    protected $fillable = ['user_id', 'keyword', 'regions', 'ufs', 'last_synced_at'];

    protected $casts = [
        'regions' => 'array',
        'ufs' => 'array',
        'last_synced_at' => 'datetime',
    ];
}