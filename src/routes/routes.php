<?php

Route::group(['middleware' => ['web', 'auth', 'tenant', 'service.accounting']], function() {

	Route::prefix('purchase-orders')->group(function () {

        //Route::get('summary', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderController@summary');
        Route::post('export-to-excel', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderController@exportToExcel');
        Route::post('{id}/approve', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderController@approve');
        //Route::post('contact-estimates', 'Rutatiina\PurchaseOrder\Http\Controllers\Sales\ReceiptController@estimates');
        Route::get('{id}/copy', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderController@copy');

    });

    Route::resource('purchase-orders/settings', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderSettingsController');
    Route::resource('purchase-orders', 'Rutatiina\PurchaseOrder\Http\Controllers\PurchaseOrderController');

});
