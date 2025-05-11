<?php

namespace App\Models;

use App\Traits\LanguageTrait;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Translatable\HasTranslations;

class Notification extends Model
{
    use SoftDeletes, LanguageTrait, HasTranslations;

    protected $fillable = [
        'user_id',
        'title',
        'message',
        'notificationable_id',
        'notificationable_type',
        'read_at',
        'metadata',
    ];

    protected $casts = [
        'read_at' => 'datetime',
        'metadata' => 'array',
    ];

    protected $translatable = [
        'title',
        'message',
    ];

    protected function title(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $raw = $this->getRawOriginal('title');

                if (is_string($raw)) {
                    $raw = json_decode($raw, true);
                }

                if (is_array($raw) && isset($raw['cu'])) {
                    return $raw['cu'];
                }

                return $value;
            }
        );
    }
    protected function message(): Attribute
    {
        return Attribute::make(
            get: function (string $value) {
                $raw = $this->getRawOriginal('message');

                if (is_string($raw)) {
                    $raw = json_decode($raw, true);
                }

                if (is_array($raw) && isset($raw['cu'])) {
                    return $raw['cu'];
                }

                return $value;
            }
        );
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

    public function notificationable()
    {
        return $this->morphTo();
    }
}
