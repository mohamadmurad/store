<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Laravel\Passport\Http\Controllers\AccessTokenController;

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
Route::post('/login', 'Api\V1\AuthApiController@login');
Route::post('/register', 'Api\V1\User\UserController@registerNewUserAccount');

Route::group(['prefix' => 'v1', 'as'=>'api.','namespace'=> 'Api\V1','middleware' => [/*'auth:api'*/]],function (){


    // logout
    Route::post('/logout', 'AuthApiController@logout');


    // User
    Route::resource('users','User\UserController');

    // Product
    Route::resource('products','Product\ProductController',['except'=>['create','edit']]);

    // Sale
    Route::resource('sale','Sale\SaleController',['except'=>['create','edit']]);

    // Category
    Route::resource('categories','Category\CategoryController');

    // Group
    Route::resource('groups','Group\GroupController');

    //Company
    Route::resource('companies','Company\CompanyController');

    // Branch
    Route::resource('branches','Branch\BranchController');

    // Attributes
    Route::resource('attributes','Attribute\AttributeController');


    // Attachment
    Route::resource('attachmentType','Attachment\AttachmentTypeController');

    // Attachment
    Route::resource('cards','Card\CardController');

    // Coupons
    Route::resource('coupons','Coupon\CouponController');


});



