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

Route::get('v1/branches/{branch}','Api\V1\Branch\BranchController@show');

// get all branches for company
Route::get('v1/branchesByCompany/{company}/','Api\V1\Company\CompanyBranchController@index');

// get all products by branch
Route::get('v1/productsByBranch/{branch}','Api\V1\Branch\BranchProductController@index');

// get all sales by branch
Route::get('v1/salesByBranch/{branch}/','Api\V1\Branch\BranchSalesController@index');

// all category
Route::get('v1/categories','Api\V1\Category\CategoryController@index');

// all product by category
Route::get('v1/productByCategory/{category}','Api\V1\Category\CategoryProductController@index');

// products
Route::resource('v1/products','Api\V1\Product\WebProductController',['only'=>['index','show']]);

// sales
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

    Route::get('/userCard/{user}', 'User\UserController@getUserCard');

    // Product

    Route::resource('employee_products','Product\DeskTopProductController');
    Route::post('addAttribute/{employee_product}','Product\DeskTopProductController@addAttributeToProduct');
    Route::get('employee_product_sale','Product\DeskTopProductController@productWithSale');
    Route::get('employee_product_not_sale','Product\DeskTopProductController@productWithoutSale');


    Route::post('AddAttachmentToProduct/{employee_product}','Product\DeskTopProductAttachmentController@store');
    Route::delete('deleteAttachmentToProduct/{attachment}','Product\DeskTopProductAttachmentController@destroy');

    // Sale
    Route::post('sales/{employee_product}','Sale\SaleController@store');
    Route::delete('sales/{sale}','Sale\SaleController@destroy');

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
    Route::get('AllChargedProcess','Card\CardController@AllChargedProcess');



    // order
    Route::post('/order','Order\OrderController@checkout');





    // Coupons
    Route::resource('coupons','Coupon\CouponController');
    // Group
    Route::resource('groups','Group\GroupController');














});



