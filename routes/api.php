<?php

use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::get('/posts', function () {
    return Post::all();
});

Route::post('/posts', function () {
    request()->validate([
        'title' => 'required',
        'description' => 'required'
    ]);

    return Post::create([
        'title' => request('title'),
        'description' => request('description')
    ]);
});

Route::post('/subscribe', function () {
    request()->validate([
        'email' => 'required',
        'website_id' => 'required'
    ]);
    $email = request('email');
    $websiteId = request('website_id');

    $userData = User::where('email', $email);
    $websiteData = Website::get($websiteId);

    if (!$userData) {
        return [
            'success' => false,
            'message' => "User with {$email} doesn't exist"
        ];
    }

    if (!$websiteData) {
        return [
            'success' => false,
            'message' => "Website doesn't doesn't exist"
        ];
    }

    $userId = $userData->id;
    $success = Subscription::create([
        'user_id' => $userId,
        'website_id' => $websiteId
    ]);

    $message = ($success) ? "Subscribed successfully" : "Subscription faild";
    return [
        'success' => $success,
        'message' => $message
    ];
});
