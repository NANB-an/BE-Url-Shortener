<?php

namespace App\Models;

use Jenssegers\Mongodb\Eloquent\Model as Eloquent;

class Click extends Eloquent
{
    protected $connection = 'mongodb';
    protected $collection = 'clicks';

    protected $fillable = [
        'short_url_id', 'ip', 'referrer', 'country', 'user_agent', 'clicked_at', 'user_id',
    ];

    public function shortUrl()
    {
        return $this->belongsTo(ShortUrl::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}

