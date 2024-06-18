<?php

Route::group(['prefix' => 'user', 'middleware' => ['auth', 'XssSanitization']], function () {
    Route::get('/', 'UserController@index')->name('user');

    Route::prefix('product')->group(function () {
        Route::get('/', 'ProductController@index')->name('user.product');
        Route::get('p/{id}', 'ProductController@view')->name('user.product.view');
        Route::get('add-to-cart/{id}', 'ProductController@addToCart')->name('user.product.add.to.cart')->middleware('check.status');
    });

    Route::prefix('account')->group(function () {
        Route::get('/', 'AccountController@index')->name('user.account');
        Route::get('preference', 'AccountController@preference')->name('user.preference');
        Route::get('activity', 'AccountController@activity')->name('user.activity');
        Route::post('update/{id}', 'AccountController@updateProfile')->name('user.account.update.profile');
        Route::post('update-preference/{id}', 'AccountController@updatePreference')->name('user.account.update.preference');
        Route::get('cart', 'AccountController@cart')->name('user.account.cart');
        Route::post('checkout', 'AccountController@checkout')->name('user.account.cart.store');
        Route::get('deleteCart/{id}', 'AccountController@deleteCart')->name('user.account.cart.delete');
        Route::post('place-order', 'AccountController@placeOrder')->name('user.account.checkout.place');
    });

    Route::group(['prefix' => 'orders', 'middleware' => ['check.status']], function() {
        Route::get('/', 'TransactionsController@index')->name('user.orders');
        Route::get('v/{invoice}', 'TransactionsController@view')->name('user.orders.view');
        Route::post('cancel/{invoice}', 'TransactionsController@cancelOrder')->name('user.orders.cancel.order');
        Route::get('return', 'TransactionsController@return')->name('user.orders.return');
        Route::post('complete/{invoice}', 'TransactionsController@complete')->name('user.orders.complete');
    });
});
