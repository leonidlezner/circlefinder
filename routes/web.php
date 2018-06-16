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

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

# Let the logout be accessible via a GET request
Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
Route::get('/home', 'HomeController@index')->name('home');


Route::group(['prefix' => 'profile', 'middleware' => 'auth',
                'as' => 'profile.'], function() {
    
    Route::get('/', 'ProfileController@index')->name('index');
    Route::get('/edit', 'ProfileController@edit')->name('edit');
    Route::put('/update', 'ProfileController@update')->name('update');
    
    Route::group(['prefix' => 'password', 'as' => 'password.'], function() {
        Route::get('/', 'PasswordController@index')->name('index');
        Route::get('/edit', 'PasswordController@edit')->name('edit');
        Route::put('/update', 'PasswordController@update')->name('update');
    });

    Route::group(['prefix' => 'avatar', 'as' => 'avatar.'], function() {
        Route::get('/', 'AvatarController@index')->name('index');
        Route::get('/edit', 'AvatarController@edit')->name('edit');
        Route::put('/update', 'AvatarController@update')->name('update');
        Route::get('/download/{file}', 'AvatarController@download')->name('download');
    });

    Route::get('/{uuid}', 'ProfileController@show')->name('show');
});



# Restricted Admin URLs
Route::group(['prefix' => 'admin', 'middleware' => 'auth:admin',
                'namespace' => 'Admin', 'as' => 'admin.'], function() {
                    
    Route::get('/', 'DashboardController@index')->name('dashboard');
    
    Route::group(['prefix' => 'users', 'as' => 'users.'], function() {
        Route::post('/{user}/restore', 'UsersController@restore')->name('restore');
        Route::delete('/{user}/forcedelete', 'UsersController@forceDelete')->name('forcedelete');
        Route::get('/trash', 'UsersController@trash')->name('trash');
    });
    Route::resource('users', 'UsersController');

    Route::group(['prefix' => 'roles', 'as' => 'roles.'], function() {
        Route::post('/{role}/restore', 'RolesController@restore')->name('restore');
        Route::delete('/{role}/forcedelete', 'RolesController@forceDelete')->name('forcedelete');
        Route::get('/trash', 'RolesController@trash')->name('trash');
    });
    Route::resource('roles', 'RolesController');
});

# Admin Login URLs
Route::group(['prefix' => 'admin', 'namespace' => 'Admin', 'as' => 'admin.'], function() {
    Route::get('/login', 'Auth\LoginController@showLoginForm')->name('login');
    Route::post('/login', 'Auth\LoginController@login')->name('login.submit');
    Route::get('/logout', 'Auth\LoginController@logout')->name('logout');
    Route::post('/logout', 'Auth\LoginController@logout')->name('logout.submit');
});