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
    ];

    public function statusable()
    {
        return $this->morphTo();
    }
}
