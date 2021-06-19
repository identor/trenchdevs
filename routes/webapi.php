<?php

/*
|--------------------------------------------------------------------------
| Web API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register webapi routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "web" and "webapi" middleware group. Enjoy building your API!
|  Perfect for SPAs like React using Sessions for authentication
| eg. trenchdevs.org/webapi/*
|
*/

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;


Route::middleware(['auth:web', 'webapi'])->group(function () {

    Route::get('blogs', 'Blogs\BlogsController@index');

    Route::get('me', function () {
        return response()->json(auth()->user());
    });

});


