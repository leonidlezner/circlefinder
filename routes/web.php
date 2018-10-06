<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
 */

if (App::environment('production')) {
    URL::forceScheme('https');
}

Auth::routes();
Route::get('login/facebook', 'Auth\LoginController@redirectToFacebook')->name('login.facebook');
Route::get('login/facebook/callback', 'Auth\LoginController@getFacebookCallback');
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');

# Let the logout be accessible via a GET request
Route::get('/', 'GuestController@index')->name('index');
Route::get('/home', 'HomeController@index')->name('home');

Route::group(['prefix' => 'profile', 'middleware' => 'auth',
    'as' => 'profile.'], function () {

        Route::get('/', 'ProfileController@index')->name('index');
        Route::get('/edit', 'ProfileController@edit')->name('edit');
        Route::put('/update', 'ProfileController@update')->name('update');

        Route::group(['prefix' => 'password', 'as' => 'password.'], function () {
            Route::get('/', 'PasswordController@index')->name('index');
            Route::get('/edit', 'PasswordController@edit')->name('edit');
            Route::put('/update', 'PasswordController@update')->name('update');
        });

        Route::group(['prefix' => 'timezone', 'as' => 'timezone.'], function () {
            Route::get('/', 'TimezoneController@index')->name('index');
            Route::get('/edit', 'TimezoneController@edit')->name('edit');
            Route::put('/update', 'TimezoneController@update')->name('update');
        });

        Route::group(['prefix' => 'avatar', 'as' => 'avatar.'], function () {
            Route::get('/', 'AvatarController@index')->name('index');
            Route::get('/edit', 'AvatarController@edit')->name('edit');
            Route::put('/update', 'AvatarController@update')->name('update');
            Route::get('/download/{w}_{h}_{uuid}.jpg', 'AvatarController@downloadResized')->name('download.resized');
            Route::get('/download/{uuid}.jpg', 'AvatarController@download')->name('download');
        });

        Route::get('/{uuid}', 'ProfileController@show')->name('show');
    });

Route::group(['prefix' => '/circles', 'as' => 'circles.', 'middleware' => ['auth', 'timezone']], function () {
    Route::group(['prefix' => '/{uuid}/membership', 'as' => 'membership.'], function ($circle_uuid) {
        Route::get('/edit', 'MembershipController@edit')->name('edit');
        Route::put('/update', 'MembershipController@update')->name('update');
    });
    
    Route::group(['prefix' => '{circle_uuid}/messages', 'as' => 'messages.'], function ($circle_uuid) {
        Route::post('/', 'MessagesController@store')->name('store');
        Route::put('/{uuid}/update', 'MessagesController@update')->name('update');
        Route::delete('/{uuid}/destroy', 'MessagesController@destroy')->name('destroy');
    });

    Route::get('/', 'CirclesController@index')->name('index');
    Route::get('/search', 'CirclesController@search')->name('search');

    Route::get('/create', 'CirclesController@create')->name('create');
    Route::post('/store', 'CirclesController@store')->name('store');
    Route::get('/{uuid}', 'CirclesController@show')->name('show');
    Route::get('/{uuid}/edit', 'CirclesController@edit')->name('edit');
    Route::put('/{uuid}/update', 'CirclesController@update')->name('update');
    Route::delete('/{uuid}/destroy', 'CirclesController@destroy')->name('destroy');
    Route::post('/{uuid}/complete', 'CirclesController@complete')->name('complete');
    Route::post('/{uuid}/uncomplete', 'CirclesController@uncomplete')->name('uncomplete');
    Route::post('/{uuid}/join', 'CirclesController@join')->name('join');
    Route::post('/{uuid}/leave', 'CirclesController@leave')->name('leave');
    Route::delete('/{uuid}/remove/{user_uuid}', 'CirclesController@remove')->name('remove');
});

Route::group(['prefix' => '/notifications', 'as' => 'notifications.', 'middleware' => 'auth'], function () {
    Route::get('/{id}', 'NotificationsController@show')->name('show');
});

Route::group(['prefix' => '/private_messages', 'as' => 'private_messages.', 'middleware' => 'auth'], function () {
    Route::get('/', 'PrivateMessagesController@inbox')->name('inbox');
    Route::get('/sent', 'PrivateMessagesController@sent')->name('sent');
    Route::get('/{uuid}/create', 'PrivateMessagesController@create')->name('create');
    Route::post('/{uuid}/send', 'PrivateMessagesController@send')->name('send');
    Route::get('/{uuid}/read', 'PrivateMessagesController@read')->name('read');
    Route::get('/{uuid}/reply/{replyUuid}', 'PrivateMessagesController@create')->name('reply');
});

# Restricted Admin URLs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin',
    'namespace' => 'Admin', 'as' => 'admin.'], function () {

        Route::get('/', 'DashboardController@index')->name('dashboard');

        Route::group(['prefix' => 'users', 'as' => 'users.'], function () {
            Route::post('/{user}/restore', 'UsersController@restore')->name('restore');
            Route::delete('/{user}/forcedelete', 'UsersController@forceDelete')->name('forcedelete');
            Route::get('/trash', 'UsersController@trash')->name('trash');
        });
        Route::resource('users', 'UsersController');

        Route::group(['prefix' => 'roles', 'as' => 'roles.'], function () {
            Route::post('/{role}/restore', 'RolesController@restore')->name('restore');
            Route::delete('/{role}/forcedelete', 'RolesController@forceDelete')->name('forcedelete');
            Route::get('/trash', 'RolesController@trash')->name('trash');
        });
        Route::resource('roles', 'RolesController');

        Route::group(['prefix' => 'languages', 'as' => 'languages.'], function () {
            Route::post('/{role}/restore', 'LanguagesController@restore')->name('restore');
            Route::delete('/{role}/forcedelete', 'LanguagesController@forceDelete')->name('forcedelete');
            Route::get('/trash', 'LanguagesController@trash')->name('trash');
        });
        Route::resource('languages', 'LanguagesController');

        Route::resource('circles', 'CirclesController')->only(['index', 'show']);
        Route::resource('messages', 'MessagesController')->only(['index', 'show']);
    });

# Admin Login URLs
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function () {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login')->name('login.submit');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout.submit');
});
