<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'type',
        'allow_null',
        'is_settings',
    ];

    protected $casts = [
        'allow_null'   => 'boolean',
        'is_settings'  => 'boolean',
    ];
}
