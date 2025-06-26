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
        $firebaseCredentialsPath = env('FIREBASE_CREDENTIALS', '/etc/secrets/url-shortener-18438-firebase-adminsdk-fbsvc-e78a3f1a02.json');

        if (!file_exists($firebaseCredentialsPath)) {
            throw new \Exception("Firebase credentials file not found at: {$firebaseCredentialsPath}");
        }

        $this->auth = (new Factory)
            ->withServiceAccount($firebaseCredentialsPath)
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
