<?php

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
Route::post('v1/login', 'Api\V1\AuthApiController@login');
Route::post('v1/register', 'Api\V1\AuthApiController@registerNewUserAccount');



Route::resource('v1/companies','Api\V1\Company\CompanyController')->only(['index','show']);

// get all branches for company
Route::get('v1/companies/{company}/branches','Api\V1\Company\CompanyBranchController@index');

// get all products by branch
Route::get('v1/branches/{branch}/products','Api\V1\Branch\BranchProductController@index');

// get all sales by branch
Route::get('v1/branches/{branch}/sales','Api\V1\Branch\BranchSalesController@index');

// all category
Route::get('v1/categories','Api\V1\Category\CategoryController@index');

// all product by category
Route::get('v1/categories/{category}/products','Api\V1\Category\CategoryProductController@index');

Route::resource('v1/products','Api\V1\Product\WebProductController',['only'=>['index','show']]);

Route::resource('v1/sales','Api\V1\Sale\SaleController',['only'=>['index']]);


// offers
Route::apiResource('v1/offers','Api\V1\Offer\OfferController')->only(['index','show']);



Route::group(['prefix' => 'v1', 'as'=>'api.','namespace'=> 'Api\V1','middleware' => ['auth:api']],function (){


    // get rouls
    Route::get('roles','toolsController@getAllRoles');

    // employee search by email
    Route::get('employeeSearchEmail','SearchController@employeeSearchByEmail');
    // category search by name
    Route::get('categorySearch','SearchController@categorySearch');

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
    Route::get('/myCard', 'User\UserController@getMyCardInfo');
    Route::put('/myAccount', 'User\UserController@updateMyInfo');

    Route::get('/users/{user}/card', 'User\UserController@getUserCard');

    // Product

    Route::resource('employee_products','Product\DeskTopProductController');
    Route::get('employee_product_sale','Product\DeskTopProductController@productWithSale');
    Route::get('employee_product_not_sale','Product\DeskTopProductController@productWithoutSale');

    Route::resource('employee_products.attachment','Product\DeskTopProductAttachmentController')->only(['store','destroy']);

    // Sale
    Route::resource('employee_products.sales','Sale\SaleController',['only'=>['store','destroy']]);

    // Category
    Route::resource('categories','Category\CategoryController')->only(['store','update','destroy']);

    //Company
    Route::resource('companies','Company\CompanyController')->only(['store','update','destroy']);

    // Branch
    Route::resource('branches','Branch\BranchController')->only(['store','update','destroy']);
    Route::post('syncAttributes/{branch}','Branch\BranchController@syncAttribute');

    // Attributes
    Route::resource('attributes','Attribute\AttributeController')->except(['show','showAttributeBranch']);
    Route::get('showAttributeBranch/{branch}','Attribute\AttributeController@showAttributeBranch');


    // Attachment
    Route::resource('attachmentType','Attachment\AttachmentTypeController');

    // offers
    Route::apiResource('offers','Offer\OfferController')->only(['store','destroy']);

    // card
    Route::resource('cards','Card\CardController')->only(['update']);



    // order
    Route::post('/order','Order\OrderController@checkout');





    // Coupons
    Route::resource('coupons','Coupon\CouponController');
    // Group
    Route::resource('groups','Group\GroupController');














});



