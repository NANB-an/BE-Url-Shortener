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
        $serviceAccountPath = $this->prepareFirebaseCredentials();
        
        $this->auth = (new Factory)
            ->withServiceAccount($serviceAccountPath)
            ->createAuth();
    }

    protected function prepareFirebaseCredentials(): string
    {
        // Path where we'll store the decoded JSON file
        $targetPath = storage_path('app/firebase.json');

        // Only decode and write if file doesn't already exist
        if (!file_exists($targetPath)) {
            $base64 = env('FIREBASE_CREDENTIALS_B64');
            if (!$base64) {
                throw new \Exception('Firebase credentials are missing.');
            }

            file_put_contents($targetPath, base64_decode($base64));
        }

        return $targetPath;
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
