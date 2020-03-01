<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Apartment extends Model
{
    protected $casts = [
        'price_per_month' => 'decimal:2',
    ];

    public function user()
    {
        return $this->belongsTo("App\User");
    }}
