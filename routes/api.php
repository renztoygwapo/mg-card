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

// Route::get('/auth/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['namespace' => 'Api', 'prefix' => 'settings'], function () {
    Route::post('database/set/connection', 'DatabaseController@setConnection');
    Route::post('database/connection', 'DatabaseController@updateOrCreateConnection');
});

Route::group(['namespace' => 'Api'], function () {
    Route::get('auth/user', function (Request $request) {
        return fractal()
            ->item($request->user(), new \App\Transformers\UserTransformer())
            ->toArray();
    })->middleware('auth:api');
    // vehicle
    Route::apiResource('vehicle', 'VehicleController');
    // user
    Route::apiResource('users', 'UserController');
    Route::post('password-update/{id}', 'UserController@updatePassword');
    //customer
    Route::apiResource('customer', 'CustomerController');
    Route::post('customer-barcode', 'CustomerController@showByBarcode');
    // customer group
    Route::apiResource('customer-group', 'CustomerGroupController');
    Route::get('show-customer-group', 'CustomerGroupController@showByCustomerGroup');
    //product management
    Route::apiResource('product', 'ProductController');
    //price management
    Route::apiResource('price', 'PriceController');
    //transaction
    Route::apiResource('transaction', 'TransactionController');
    Route::post('customer-id', 'TransactionController@showByCustomerId');
    //customer current points
    Route::post('customer-current-points', 'CustomerController@showCustomerCurrentPoints');
    //ppoint system
    Route::get('role', 'RoleController@index');
    Route::get('view-accumulated', 'TransactionController@showTransaction');
    //roles
    Route::apiResource('point-system', 'PointSystemController');
    // show transaction by customer
    Route::get('transaction-by-customer','TransactionController@showTransactionbyCustomer');
    // redeem points
    Route::apiResource('points-redeem','RedeemController');
    Route::post('admin/authorize', 'UserController@adminAuthorize');
    // daily points per product api
    Route::get('daily-points', 'TransactionController@dailyProductPoints');

    Route::post('export-database', 'DatabaseController@dumpDatabase');
    Route::post('import-customer', 'CustomerController@importCustomerData');
    Route::apiResource('export-price-summary', 'PriceSummaryController');
    Route::apiResource('export-point-system-summary', 'PointSystemSummaryController');
    //database management
    Route::apiResource('database-management', 'DatabaseManagementController');
    Route::post('settings', 'DatabaseManagementController@setRfid');

    //export redeem logs
    Route::apiResource('redeem-logs', 'RedeemLogController');

    //export transaction logs
    Route::apiResource('transaction-logs', 'TransactionLogController');

    //export customer logs
    Route::apiResource('customer-logs', 'CustomerLogController');
    Route::get('card-volume', 'CustomerLogController@cardVolume');
    Route::get('earn-point', 'CustomerLogController@earnedPoints');
    Route::get('total-redeem', 'CustomerLogController@totalRedeem');
    Route::get('unredeem', 'CustomerLogController@unredeemedPoints');

    Route::get('get-transaction-with-redeem', 'TransactionController@transactionWithRedeem');
    //total daily liters
    Route::apiResource('export-daily', 'GenerateTemplateController');
    //promo items
    Route::apiResource('promo-item', 'PromoItemController');
    Route::get('export-promo-items-summary', 'PromoItemController@exportPromoItems');
    //customer high
    Route::get('customer-high', 'CustomerController@customerHighPoints');
    //promo customer avail
    Route::apiResource('promo-customer-avail', 'PromoCustomerAvailController');
    //days
    Route::get('days', 'GenerateTemplateController@getDays');
    Route::post('subtract-point', 'TransactionController@subtractPoints');
    Route::get('tag-customer', 'PromoCustomerAvailController@getTagCustomer');
    //fleet cards
    Route::apiResource('fleet-card', 'FleetCardController');
    //pos printer
    Route::get('print', 'TransactionController@testPrint');    
    Route::get('redeem-print', 'RedeemController@testPrint');  
    Route::post('report-generate', 'DatabaseController@reportTemplate');  
});
