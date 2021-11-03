<?php

use App\Models\Post;
use App\Models\Subscription;
use App\Models\User;
use App\Models\Website;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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

Route::post('/create-website', function () {
    return Website::create([
        'name' => request('name')
    ]);
});

Route::post('/posts', function () {
    request()->validate([
        'title' => 'required',
        'description' => 'required',
        'website_id' => 'required'
    ]);
    $websiteId = request('website_id');
    $websiteData = Website::get('id', $websiteId);
    if (!$websiteData) {
        return [
            'success' => false,
            'message' => "Website doesn't doesn't exist"
        ];
    }

    $success = Post::create([
        'title' => request('title'),
        'description' => request('description'),
        'website_id' => $websiteId
    ]);

    $message = ($success) ? "Post created successfully" : "Post creation failed";
    return [
        'success' => $success,
        'message' => $message
    ];
});

Route::post('/subscribe', function () {
    request()->validate([
        'email' => 'required',
        'website_id' => 'required'
    ]);
    $email = request('email');
    $websiteId = request('website_id');

    $userData = DB::table('users')->where('email', '=', $email)->first();
    $websiteData = Website::where('id', $websiteId);

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

    $message = ($success) ? "Subscribed successfully" : "Subscription failed";
    return [
        'success' => $success,
        'message' => $message
    ];
});
