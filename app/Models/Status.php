<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Status extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'statusable_id',
        'statusable_type',
        'author_id',
    ];

    public function statusable()
    {
        return $this->morphTo();
    }
}
