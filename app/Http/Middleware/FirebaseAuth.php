<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use App\Services\FirebaseService;
use App\Models\User;
use Illuminate\Support\Facades\Auth;

class FirebaseAuth
{
    public function handle(Request $request, Closure $next)
    {
        $authHeader = $request->bearerToken();

        if (!$authHeader) {
            return response()->json(['message' => 'Unauthorized: Missing token'], 401);
        }

        $firebase = new FirebaseService();
        $verifiedToken = $firebase->verifyIdToken($authHeader);

        if (!$verifiedToken) {
            return response()->json(['message' => 'Unauthorized: Invalid token'], 401);
        }

        // ✅ Extract Firebase claims
        $uid = $verifiedToken->claims()->get('sub');
        $email = $verifiedToken->claims()->get('email');
        $name = $verifiedToken->claims()->get('name') ?? 'Anonymous';

        // ✅ Find or create user in MongoDB
        $user = User::firstOrCreate(
            ['firebase_uid' => $uid],
            ['email' => $email, 'name' => $name]
        );

        // ✅ Set authenticated user (important: this is what Auth::user() returns)
        Auth::login($user); // ← this is the key fix

        return $next($request);
    }
}
