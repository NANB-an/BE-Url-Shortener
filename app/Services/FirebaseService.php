<?php

namespace App\Services;

use Kreait\Firebase\Factory;
use Kreait\Firebase\Auth;
use Kreait\Firebase\Exception\Auth\FailedToVerifyToken;

class FirebaseService
{
    protected Auth $auth;

    public function __construct()
    {
        $this->auth = (new Factory)
            ->withServiceAccount(base_path(env('FIREBASE_CREDENTIALS')))
            ->createAuth();
    }

    public function verifyIdToken(string $idToken)
    {
        try {
            return $this->auth->verifyIdToken($idToken);
        } catch (FailedToVerifyToken $e) {
            return null;
        }
    }

    public function getAuth(): Auth
    {
        return $this->auth;
    }
}
