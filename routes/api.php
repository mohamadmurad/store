<?php

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
Route::group(['prefix' => 'v1', 'as'=>'api.','namespace'=> 'Api\V1','middleware' => []],function (){


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
Route::middleware('auth:api')->get('/user', function (Request $request) {
    return $request->user();
});


