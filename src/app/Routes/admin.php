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

Route::group(['prefix'=>'admin'], function(){
    // Módulo de Contabilidad
    Route::get('check-currencies/{amount}', 'CustomAdminController@checkCurrencies');
    Route::get('pending-payment-register/{type}/{id}', 'CustomAdminController@pendingPaymentRegister');
    Route::post('modal-pending-payment-register', 'CustomAdminController@postPendingPaymentRegister');
    Route::get('pending-payment-unpaid/{type}/{id}', 'CustomAdminController@pendingPaymentUnpaid');

    // Módulo de Productos
    Route::get('search-product/{id?}', 'CustomAdminController@searchProduct');
    Route::get('generate-barcodes-pdf', 'CustomAdminController@generateBarcodesPdf');
    Route::get('check-product/{id}', 'CustomAdminController@getCheckProduct');
    Route::get('purchase-add-product/{purchase_id}/{product_id}', 'CustomAdminController@getPurchaseAddProduct');
    Route::post('modal-purchase-add-product', 'CustomAdminController@postPurchaseAddProduct');
    Route::get('change-purchase-status/{id}/{status}', 'CustomAdminController@getChangePurchaseStatus');
    Route::get('transfer-product-stock/{product_stock_id}', 'CustomAdminController@getTransferProductStock');
    Route::post('transfer-product-stock', 'CustomAdminController@postTransferProductStock');
    Route::get('remove-product-stock/{product_stock_id}', 'CustomAdminController@getRemoveProductStock');
    Route::post('remove-product-stock', 'CustomAdminController@postRemoveProductStock');

    // Módulo de Ventas
    Route::get('calculate-total/{amount}/{currency_id}', 'CustomAdminController@getCalculateTotal');
    Route::get('create-sale', 'CustomAdminController@getCreateSale');
    Route::post('create-sale', 'CustomAdminController@postCreateSale');
    Route::get('create-refund/{sale_id?}', 'CustomAdminController@getCreateRefund');
    Route::post('create-refund', 'CustomAdminController@postCreateRefund');

    // Módulo de Reportes
    Route::get('sales-report', 'ReportController@getSalesReport');
    Route::get('sales-detail-report', 'ReportController@getSalesDetailReport');
    Route::get('products-report', 'ReportController@getProductsReport');
    Route::get('pending-payments-report/{type}', 'ReportController@getPendingPaymentsReport');
    Route::get('partners-report', 'ReportController@getPartnersReport');
    Route::get('account-book', 'ReportController@getAccountBookReport');
    Route::get('account-book-detail', 'ReportController@getAccountBookDetailReport');
    Route::get('account-income-statement', 'ReportController@getAccountIncomeStatement');
    Route::get('balance-sheet', 'ReportController@getBalanceSheet');
    Route::get('statistics-sales', 'ReportController@getStatisticsSales');
});