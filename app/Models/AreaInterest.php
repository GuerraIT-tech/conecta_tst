<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AreaInterest extends Model
{
    protected $guarded = ['id'];

    public function radar()
    {
        return $this->hasMany(Radar::class);
    }
}
