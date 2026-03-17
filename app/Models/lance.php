<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class lance extends Model
{
    protected $table = 'lances';
    protected $guarded = ['id'];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}
