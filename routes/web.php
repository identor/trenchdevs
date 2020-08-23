<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\URL;

if (env('APP_ENV') === 'production') {
    URL::forceScheme('https');
}

$baseUrl = env('BASE_URL');

if (empty($baseUrl)) {
    throw new Exception("Base url not found.");
}

// START - Special subdomains here

//Route::domain("blog.{$baseUrl}")->group(function(){
//    Route::get('/blog', function () {
//        dd('@blog');
//    });
//});

// END - Special subdomains here

// START - Portfolio Routes
Route::domain("{username}.{$baseUrl}")->group(function () {
    Route::get('/','PortfolioController@show');
});
// END - Portfolio Routes

Route::get('/', 'PublicController@index');

Auth::routes(['verify' => true]);

Route::middleware(['auth:web', 'verified'])->group(function () {
    Route::get('/home', 'HomeController@index');

    // START - users
    Route::get('admin/users/create', 'Admin\UsersController@create')->name('users.create');
    Route::post('admin/users/upsert', 'Admin\UsersController@upsert')->name('users.upsert');
    Route::post('admin/users/password_reset', 'Admin\UsersController@passwordReset')->name('users.password_reset');
    Route::get('admin/users/{id}', 'Admin\UsersController@edit')->name('users.edit');
    Route::get('admin/users', 'Admin\UsersController@index')->name('users.index');
    // End - users

    // START - mailers
    Route::get('emails/generic', 'EmailTester@genericMail');
    // Announcements
    Route::get('announcements/create','Admin\AnnouncementsController@create');
    Route::post('announcements/announce','Admin\AnnouncementsController@announce');
    Route::get('announcements','Admin\AnnouncementsController@list');
    // END - mailers

    // START - portfolio
    Route::get('portfolio/preview', 'PortfolioController@preview');
    // END - portfolio

});

Route::get('{username}', 'PortfolioController@show');
Route::get('emails/test/{view}', 'EmailTester@test');
Route::get('emails/testsend', 'EmailTester@testSend');
Route::post('aws/sns', 'AwsController@sns');
