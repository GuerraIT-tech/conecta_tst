<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Radar extends Model
{
    protected $guarded = ['id'];
    protected $casts = [
    'data_hora_encerramento' => 'datetime',];
    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function area_interest()
    {
        return $this->belongsTo(AreaInterest::class);
    }

    public function modality()
    {
        return $this->belongsTo(Modality::class);
    }
}
