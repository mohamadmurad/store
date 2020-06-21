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
Route::post('/register', 'Api\V1\AuthApiController@registerNewUserAccount');



Route::resource('v1/companies','Api\V1\Company\CompanyController')->only(['index','show']);
Route::get('v1/companies/{company}/branches','Api\V1\Company\CompanyBranchController@index');

Route::resource('v1/products','Api\V1\Product\WebProductController',['only'=>['index','show']]);
Route::resource('v1/sales','Api\V1\Sale\SaleController',['only'=>['index','show']]);

Route::group(['prefix' => 'v1', 'as'=>'api.','namespace'=> 'Api\V1','middleware' => ['auth:api']],function (){


    // logout
    Route::post('/logout', 'AuthApiController@logout');

    // check token
    Route::get('/checkToken',function (){


        return response([
            'message' => 'your token is valid',
        ],200);
    });


    // User
    Route::resource('users','User\UserController');

    Route::get('/myAccount', 'User\UserController@getMyInfo');
    Route::put('/myAccount', 'User\UserController@updateMyInfo');

    // Product
    Route::resource('employee_products','Product\DeskTopProductController');
    Route::get('employee_product_sale','Product\DeskTopProductController@productWithSale');
    Route::get('employee_product_not_sale','Product\DeskTopProductController@productWithoutSale');


    // Sale
    Route::resource('employee_products.sales','Sale\SaleController',['only'=>['store','destroy']]);



    // Category
    Route::resource('categories','Category\CategoryController');

    // Group
    Route::resource('groups','Group\GroupController');

    //Company
    Route::resource('companies','Company\CompanyController')->only(['store','update']);
    //Route::resource('companies.branches','Company\CompanyBranchController',['except'=>['create','edit']]);
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



