<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\LaravelPackageTools\Concerns\Package\HasTranslations;

class Transaction extends Model
{

    use SoftDeletes, LanguageTrait, HasTranslations;
    protected $fillable = [
        'user_id',
        'amount',
        'description',
        'status',
        'type',
        'direction',
        'method',
        'transactionable_id',
        'transactionable_type',
        'attached',
    ];

    protected $casts = [
        'amount' => 'float',
        'status' => 'string',
        'type' => 'string',
        'direction' => 'string',
        'method' => 'string',
        'transactionable_id' => 'integer',
        'transactionable_type' => 'string',
        'attached' => 'string',
    ];

    protected $translatable = [
        'description',
    ];

    protected function description(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) =>  $this->getAllTranslations('description'),
        );
    }

    public function getAttachedUrlAttribute(): string
    {
        return asset('storage/' . $this->attached);
    }

    protected function metadata(): Attribute
    {
        return Attribute::make(
            get: fn(string $value) => json_decode($value, true),
            set: fn($value) => json_encode($value),
        );
    }


    // Relations

    public function user()
    {
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function transactionable()
    {
        return $this->morphTo();
    }
}
