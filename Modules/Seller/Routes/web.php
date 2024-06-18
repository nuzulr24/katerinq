<?php

Route::group(['prefix' => 'seller', 'middleware' => ['auth', 'check.seller']], function () {
    Route::get('/', 'SellerController@index')->name('seller');

    Route::group(['prefix' => 'product', 'middleware' => ['check.status']], function() {
        Route::get('/', 'ProductController@index')->name('product');
        Route::get('create', 'ProductController@create')->name('product.create');
        Route::post('store', 'ProductController@store')->name('product.store');
        Route::get('edit/{id}', 'ProductController@edit')->name('product.edit');
        Route::post('update/{id}', 'ProductController@update')->name('product.update');
        Route::get('detail/{id}', 'ProductController@detail')->name('product.detail');
        Route::get('delete/{id}', 'ProductController@destroy')->name('product.delete');
    });

    Route::group(['prefix' => 'transaction', 'middleware' => ['check.status']], function() {
        Route::get('/', 'OrdersController@index')->name('orders');
        Route::get('v/{invoice}', 'OrdersController@view')->name('orders.view');
        Route::post('complete/{invoice}', 'OrdersController@complete')->name('orders.complete');
        Route::post('working/{invoice}', 'OrdersController@working')->name('orders.working')->middleware('XssSanitization');
    });

    Route::prefix('account')->group(function () {
        Route::get('/', 'AccountController@index')->name('account');
        Route::get('preference', 'AccountController@preference')->name('user.preference');
        Route::post('update/{id}', 'AccountController@updateProfile')->name('user.account.update.profile');
        Route::post('update-preference/{id}', 'AccountController@updatePreference')->name('user.account.update.preference');

        Route::group(['prefix' => 'rekening', 'middleware' => ['check.status']], function() {
            Route::get('/', 'AccountController@rekening')->name('rekening');
            Route::get('create', 'AccountController@createRekening')->name('rekening.create');
            Route::post('store', 'AccountController@storeRekening')->name('rekening.store')->middleware('XssSanitization');
            Route::get('edit/{id}', 'AccountController@editRekening')->name('rekening.edit');
            Route::post('update/{id}', 'AccountController@updateRekening')->name('rekening.update')->middleware('XssSanitization');
            Route::get('delete/{id}', 'AccountController@deleteRekening')->name('rekening.delete');
        });

        Route::group(['prefix' => 'withdrawal', 'middleware' => ['check.status']], function() {
            Route::get('/', 'AccountController@withdrawal')->name('withdrawal');
            Route::get('create', 'AccountController@createRequest')->name('withdrawal.create');
            Route::post('store', 'AccountController@storeRequest')->name('withdrawal.store')->middleware('XssSanitization');
            Route::get('v/{invoice}', 'AccountController@viewRequest')->name('withdrawal.view');
            Route::get('c/{invoice}', 'AccountController@cancelRequest')->name('withdrawal.cancel');
        });
    });

    Route::group(['prefix' => 'report', 'middleware' => ['check.status']], function() {
       Route::get('/', 'ReportController@index')->name('report');
       Route::get('statistic', 'ReportController@statistic')->name('report.statistic');
    });
});
