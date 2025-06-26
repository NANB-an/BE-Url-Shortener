<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;


class ShortUrl extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'short_urls';

    protected $fillable = [
        'original_url',
        'short_code',
        'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function clicks()
    {
        return $this->hasMany(Click::class);
    }
}
