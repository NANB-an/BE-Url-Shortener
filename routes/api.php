<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ShortUrlController;
use App\Http\Controllers\Api\AuthController;


/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();


});
Route::middleware(['firebase.auth'])->group(function () {
    Route::post('/shorten', [ShortUrlController::class, 'store']);
    Route::get('/stats/{code}', [ShortUrlController::class, 'stats']);  
    Route::get('/getcodes', [ShortUrlController::class, 'getCodes']);
});
Route::middleware('firebase.auth')->post('/sync-user', function () {
    return response()->json(['status' => 'User synced', 'user' => auth()->user()]);
});


