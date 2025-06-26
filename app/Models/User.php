<?php

namespace App\Models;

use Jenssegers\Mongodb\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use Notifiable;
    protected $connection = 'mongodb';
    protected $collection = 'users';

    protected $fillable = [
        'name', 'email', 'firebase_uid',
    ];

    protected $hidden = [
     
    ];
}
