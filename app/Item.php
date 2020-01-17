<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    protected $fillable = [
        'title',
        'content',
        'is_important'
    ];

    protected $casts = [
        'is_important' => 'boolean'
    ];
}
