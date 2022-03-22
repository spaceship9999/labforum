<?php

use App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
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

//Route for all users

// - Authorisation
Route::group(['prefix' => 'auth', 'middleware' => 'throttle:30,1'], function() {
    Route::post('login', [Controllers\Auth\LoginController::class, "login"]);
    Route::get('logout', [Controllers\Auth\LoginController::class, "logout"])->middleware('auth:api');
    Route::get('attempts', [Controllers\Auth\LoginController::class, "getAttemptDetails"]);
});

// - Categories listing
Route::group(['prefix' => 'category'], function() {
    Route::get('/', [Controllers\TaxonomyController::class, 'listCategories']);
    Route::get('/featured', [Controllers\TaxonomyController::class, 'getFeaturedCategories']);
    Route::get('/{id}',[Controllers\TaxonomyController::class, 'getTaxonomyBySlug']);
});

//Routes for all registered users
Route::group(['middleware' => 'auth:api'], function() {
    Route::group(['prefix' => 'user'], function() {
        Route::get('details', [Controllers\UserController::class, 'getSafeUserDetails']);
    });
});



//Posts
Route::get('post/{id}', [Controllers\PostController::class, "getPost"]);

//Route::get('index', []);