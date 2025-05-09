<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserListingFavorite extends Model
{

    use SoftDeletes;

    protected $fillable = [
        'user_id',
        'listing_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function listing()
    {
        return $this->belongsTo(Listing::class)->withTrashed();
    }
}
