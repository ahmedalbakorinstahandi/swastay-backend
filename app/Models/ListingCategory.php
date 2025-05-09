<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ListingCategory extends Model
{
    use SoftDeletes;
    public $timestamps = false;

    protected $fillable = [
        'listing_id',
        'category_id',
    ];

    public function listing()
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }

    public function category()
    {
        return $this->belongsTo(Category::class)->withTrashed();
    }
}
