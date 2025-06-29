<?php
namespace App\Http\Controllers;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Stevebauman\Location\Facades\Location;
use App\Models\ShortUrl;
use App\Models\Click;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Http;
use Jenssegers\Agent\Agent;

class ShortUrlController extends Controller
{
   public function store(Request $request)
    {
        $request->validate(['url' => 'required|url']);

        $user = Auth::user(); // Get Firebase-authenticated user

        if (!$user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        $shortCode = Str::random(6);

        $shortUrl = ShortUrl::create([
            'original_url' => $request->url,
            'short_code' => $shortCode,
            'user_id' => $user->_id, // ✅ Important for filtering later
        ]);

        return response()->json([
            'short_url' => url("/r/$shortCode"),
            'code' => $shortCode,
        ]);
    }

    public function redirect($code, Request $request)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();
        if (!$shortUrl) return response()->json(['message' => 'Not found'], 404);

        $referrer = $request->header('referer') ?? 'Direct';
        $ip = $request->ip();
        // $position = Location::get($ip);
        
        
       //dev mode
        // if ($ip === '127.0.0.1') {
        //     $ip = '8.8.8.8'; // Google's IP (United States)
        // }
        // dev mode
        $position = Location::get($ip);
        $country = $position ? $position->countryName : 'Unknown';
        // try {
        //     $res = Http::get("http://ip-api.com/json/{$ip}");
        //     $country = $res->json()['country'] ?? 'Unknown';
        // } catch (\Exception $e) {}

        $agent = new Agent();
        $browser = $agent->browser();   // e.g., Chrome
        $platform = $agent->platform(); // e.g., Windows
        Click::create([
            'short_url_id' => $shortUrl->_id,
            'ip' => $ip,
            'referrer' => $referrer,
            'country' => $country,
            'user_agent' => "$browser on $platform",
            'clicked_at' => now(),
        ]);

        return redirect($shortUrl->original_url);
    }

    public function stats($code)
    {
        $shortUrl = ShortUrl::where('short_code', $code)->first();

        if (!$shortUrl) {
            return response()->json(['message' => 'Short URL not found'], 404);
        }

        $clicks = Click::where('short_url_id', $shortUrl->_id)->get();

        return response()->json([
            'total_clicks' => $clicks->count(),
            'original_url' => $shortUrl->original_url,
            'by_country' => $clicks->groupBy('country')->map->count(),
            'by_referrer' => $clicks->groupBy('referrer')->map->count(),
            'timestamps' => $clicks->pluck('clicked_at')->map(function ($ts) {
                try {
                    return \Carbon\Carbon::parse($ts)->toDateTimeString();
                } catch (\Exception $e) {
                    return null;
                }
            }),
        ]);
    }


    public function getCodes()
        {
            $user = Auth::user();

            if (!$user) {
                return response()->json(['message' => 'Unauthorized'], 401);
            }

            $codes = ShortUrl::where('user_id', $user->_id)
                            ->get(['short_code', 'original_url']);

            return response()->json($codes);
        }
}
