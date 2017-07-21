<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::group(['prefix'=>'process'], function(){
    Route::get('/calculate-shipping/{shipping_id}/{city_id}/{weight}', 'ProcessController@getCalculateShipping');
    Route::get('/add-cart-item/{id}', 'ProcessController@getAddCartItem');
    Route::get('/delete-cart-item/{id}', 'ProcessController@getDeleteCartItem');
    Route::post('/add-cart-item', 'ProcessController@postAddCartItem');
    Route::get('/confirmar-compra/{type}', 'ProcessController@getCheckCart');
    Route::post('/update-cart', 'ProcessController@postUpdateCart');
    Route::get('/comprar-ahora/{slug}', 'ProcessController@getBuyNow');
    Route::post('/buy-now', 'ProcessController@postBuyNow');
    Route::get('/finalizar-compra/{cart_id?}', 'ProcessController@getFinishSale');
    Route::post('/finish-sale', 'ProcessController@postFinishSale');
    Route::get('/sale/{id}', 'ProcessController@getSale')->middleware('auth');
    Route::post('/payment-receipt', 'ProcessController@postPaymentReceipt');
});

Route::group(['prefix'=>'payments'], function(){
    // Bank Deposit
    Route::post('/bank-deposit/make-payment', 'Payment\BankDepositController@postMakePayment');
    // Pagosnet
    Route::post('/pagosnet/make-payment', 'Payment\PagosnetController@postMakePayment');
});