<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Bids extends Model
{
    protected $guarded = ['id'];

    protected $table = 'bids';

    protected $casts = [
        'items' => 'array',
    ];

    public function favoritedBy()
    {
        return $this->belongsToMany(\App\Models\User::class, 'favorites', 'bid_id', 'user_id')
            ->withTimestamps();
    }
}
