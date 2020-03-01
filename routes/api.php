<?php

use Illuminate\Http\Request;

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

Route::group(['middleware' => ['json.response', 'cors']], function () {

    Route::middleware('auth:api')->get('/user', function (Request $request) {
        return $request->user();
    });

    // public routes
    Route::post('/login', 'Api\AuthController@login')->name('login.api');
    Route::post('/register', 'Api\AuthController@register')->name('register.api');

    // Password Reset Routes...
    $this->get('password/reset', 'Auth\ForgotPasswordController@showLinkRequestForm');
    $this->post('password/email', 'Auth\ForgotPasswordController@sendResetLinkEmail');
    $this->get('password/reset/{token}', 'Auth\ResetPasswordController@showResetForm');
    $this->post('password/reset', 'Auth\ResetPasswordController@reset');

    // private routes
    Route::middleware('auth:api')->group(function () {

        /*User Routes*/
        //Logout user
        Route::get('/logout', 'Api\AuthController@logout')->name('logout');
        //List users
        Route::get('users', 'UserController@index');
        //List users by types
        Route::get('users/{type}', 'UserController@index');
        //get single user
        Route::get('user/{id}', 'UserController@show');
        //Update user
        Route::put('users/{id}', 'UserController@store');
        //Add user
        Route::post('users/', 'Api\AuthController@addUser');
        //Delete user
        Route::delete('users/{id}', 'UserController@destroy');



        /*Main apartment routes*/
        //List apartment
        Route::get('apartments', 'ApartmentController@index');
        //List apartment by status
        Route::get('apartments/{status}', 'ApartmentController@index');
        //get single apartment
        Route::get('apartments/{id}', 'ApartmentController@show');
        //get apartments from realtor
        Route::get('users/{id}/apartments', 'ApartmentController@getUserApartments');
        //get apartments from realtor by status
        Route::get('users/{id}/apartments/{status}', 'ApartmentController@getUserApartmentsByStatus');
        //Create new apartment
        Route::post('apartments', 'ApartmentController@store');
        //Update apartment info
        Route::put('apartments/{id}', 'ApartmentController@store');
        //Delete apartment
        Route::delete('apartments/{id}', 'ApartmentController@destroy');

        //Dele
        Route::delete('unused/apartments/', 'ApartmentController@deleteUnusedApartments');

    });

});