<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Image extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'path',
        'type',
        'imageable_id',
        'imageable_type',
        'orders',
    ];

    public function imageable()
    {
        return $this->morphTo();
    }

    public function getUrlAttribute(): string
    {
        return asset('storage/' . $this->path);
    }
}
