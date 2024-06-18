<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LoginController;

use Illuminate\Support\Facades\Storage;
use Aws\S3\S3Client;
use App\Models\Mail as Mailing;
use Aws\Exception\AwsException;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Mail;
use Xendit\Configuration;
use Xendit\PaymentRequest\PaymentRequestApi;
use Illuminate\Support\Str;

use Modules\Seller\Entities\PaymentModel as Payment;
use App\Enums\GlobalEnum as Status;
use Modules\Seller\Entities\AccountModel as User;
use Modules\User\Entities\OrderModel as Order;

Route::prefix('cron')->group(function() {
    Route::get('order.realtime', function() {
        $orders = Order::whereIn('is_status', [Status::isDepositPending, Status::isDepositInquiry])->get(); // Sesuaikan dengan logika pengambilan data
        $currentTimestamp = time(); // Get the current timestamp

        foreach ($orders as $order) {
            $merchantCode = app_info('duitku_merchant');
            $apiKey = app_info('duitku_client');
            $merchantOrderId = $order->invoice_number; // dari anda (merchant), bersifat unik
            $signature = md5($merchantCode . $merchantOrderId . $apiKey);

            $params = array(
                'merchantCode' => $merchantCode,
                'merchantOrderId' => $merchantOrderId,
                'signature' => $signature
            );

            $params_string = json_encode($params);
            if(app_info('duitku_sandbox') == 1) {
                $url = 'https://sandbox.duitku.com/webapi/api/merchant/transactionStatus'; // Sandbox
            } else {
                $url = 'https://passport.duitku.com/webapi/api/merchant/transactionStatus'; // Production
            }

            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $url);
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
            curl_setopt($ch, CURLOPT_POSTFIELDS, $params_string);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($params_string))
            );
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);

            //execute post
            $request = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);

            if($httpCode == 200)
            {
                $results = json_decode($request, true);
                if($results['statusCode'] == 02) {
                    $findOrder = Order::find($order->id);
                    $findOrder->is_status = 4;
                    $findOrder->url_payment = NULL;

                    // save
                    $findOrder->save();
                } elseif($results['statusCode'] == 00) {
                    $findOrder = Order::find($order->id);
                    $findOrder->is_status = 2;
                    $findOrder->url_payment = NULL;
                    // save
                    $findOrder->save();
                }
            }

            echo "Update successfully";
        }
    });
});

Route::middleware(['guest'])->group(function () {
    Route::get('login', 'App\Http\Controllers\LoginController@index')->name('login');
    Route::post('proses_login', 'App\Http\Controllers\LoginController@proses_login')->name('proses_login')->middleware('check.status');
    Route::get('register', 'App\Http\Controllers\LoginController@register')->name('register');
    Route::post('storeRegister', 'App\Http\Controllers\LoginController@storeRegister')->name('storeRegister');
    Route::get('forgot', 'App\Http\Controllers\LoginController@forgot')->name('forgot');
    Route::post('forgotPassword', 'App\Http\Controllers\LoginController@forgotPassword')->name('forgotPassword');
    Route::get('reset/{token}', 'App\Http\Controllers\LoginController@reset')->name('reset');
    Route::post('resetPassword', 'App\Http\Controllers\LoginController@resetPassword')->name('resetPassword');
    Route::get('verify/{token}', 'App\Http\Controllers\LoginController@verify')->name('verify');
});

Route::get('close-guide', 'App\Http\Controllers\HomeController@closeGuide')->name('close.guide');

// page utama
Route::get('/', 'App\Http\Controllers\HomeController@index')->name('landing');
Route::prefix('marketplace')->group(function() {
    Route::get('/', 'App\Http\Controllers\HomeController@market')->name('marketplace');
    Route::get('p/{uuid}', 'App\Http\Controllers\HomeController@productDetail')->name('marketplace.detail');
    Route::get('how-to-sell', 'App\Http\Controllers\HomeController@howToSell')->name('marketplace.how-to-sell');
});

Route::prefix('blog')->group(function() {
    Route::get('/', 'App\Http\Controllers\HomeController@blog')->name('blog');
    Route::get('p/{slug}', 'App\Http\Controllers\HomeController@blogDetail')->name('blog.detail');
});
Route::get('about', 'App\Http\Controllers\HomeController@about')->name('about');
Route::get('p/{slug}', 'App\Http\Controllers\HomeController@pages')->name('info-page');
// end page utama

Route::get('logout', 'App\Http\Controllers\LoginController@logout')->name('logout');
Route::group(['middleware' => ['auth','check.admin']], function () {
    Route::get('change-language/{locale}', function ($locale) {
        App::setLocale($locale);
        Config::set('app.locale', $locale);
        return back()->with('swal', swal_alert('success', 'Language Changed'));
    });
    Route::group(['prefix' => 'app'], function () {
        Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
        Route::prefix('account')->group(function () {
            Route::get('/', 'App\Http\Controllers\AdminController@index')->name('account');
            Route::get('activity', 'App\Http\Controllers\AdminController@activity')->name('account.activity');
        });

        Route::prefix('product')->group(function () {
            Route::get('/', 'App\Http\Controllers\ProductController@index')->name('product');
            Route::get('create', 'App\Http\Controllers\ProductController@create')->name('product.create');
            Route::post('store', 'App\Http\Controllers\ProductController@store')->name('product.store');
            Route::get('edit/{id}', 'App\Http\Controllers\ProductController@edit')->name('product.edit');
            Route::post('update/{id}', 'App\Http\Controllers\ProductController@update')->name('product.update');
            Route::get('delete/{id}', 'App\Http\Controllers\ProductController@destroy')->name('product.delete');
            Route::get('detail/{id}', 'App\Http\Controllers\ProductController@detail')->name('product.detail');
        });

        Route::prefix('content')->group(function () {
            // Content
            Route::get('/', 'App\Http\Controllers\ContentController@index')->name('content');
            Route::get('create', 'App\Http\Controllers\ContentController@create')->name('content.create');
            Route::post('store', 'App\Http\Controllers\ContentController@store')->name('content.store');
            Route::get('edit/{id}', 'App\Http\Controllers\ContentController@edit')->name('content.edit');
            Route::post('update/{id}', 'App\Http\Controllers\ContentController@update')->name('content.update');
            Route::get('delete/{id}', 'App\Http\Controllers\ContentController@destroy')->name('content.delete');
            Route::get('p/{slug}', 'App\Http\Controllers\ContentController@show')->name('content.detail');
            Route::post('upload-image', 'App\Http\Controllers\ContentController@uploadImage')->name('content.upload');
            Route::post('delete-image', 'App\Http\Controllers\ContentController@deleteImage')->name('content.deleteImage');

            // Tags
            Route::get('tag', 'App\Http\Controllers\ContentController@tags')->name('content.tag');
            Route::get('tag/create', 'App\Http\Controllers\ContentController@addTags')->name('content.tag.create');
            Route::post('tag/store', 'App\Http\Controllers\ContentController@createTags')->name('content.tag.store');
            Route::get('tag/edit/{id}', 'App\Http\Controllers\ContentController@editTags')->name('content.tag.edit');
            Route::post('tag/update/{id}', 'App\Http\Controllers\ContentController@updateTag')->name('content.tag.update');
            Route::get('tag/delete/{id}', 'App\Http\Controllers\ContentController@deleteTag')->name('content.tag.delete');

            // Categories
            Route::get('categories', 'App\Http\Controllers\ContentController@categories')->name('content.categories');
            Route::get('categories/create', 'App\Http\Controllers\ContentController@addCategories')->name('content.categories.create');
            Route::post('categories/store', 'App\Http\Controllers\ContentController@createCategories')->name('content.categories.store');
            Route::get('categories/edit/{id}', 'App\Http\Controllers\ContentController@editCategories')->name('content.categories.edit');
            Route::post('categories/update/{id}', 'App\Http\Controllers\ContentController@updateCategories')->name('content.categories.update');
            Route::get('categories/delete/{id}', 'App\Http\Controllers\ContentController@deleteCategories')->name('content.categories.delete');
        });

        Route::prefix('users')->group(function () {
            Route::get('/', 'App\Http\Controllers\UsersController@index')->name('users');
            Route::get('create', 'App\Http\Controllers\UsersController@create')->name('users.create');
            Route::post('store', 'App\Http\Controllers\UsersController@store')->name('users.store');
            Route::get('edit/{id}', 'App\Http\Controllers\UsersController@edit')->name('users.edit');
            Route::post('update/{id}', 'App\Http\Controllers\UsersController@update')->name('users.update');
            Route::get('delete/{id}', 'App\Http\Controllers\UsersController@delete')->name('users.delete');
            Route::get('detail/{id}', 'App\Http\Controllers\UsersController@show')->name('users.show');

            Route::prefix('sellers')->group(function () {
                Route::get('/', 'App\Http\Controllers\UsersController@sellers')->name('users.sellers');
                Route::get('create', 'App\Http\Controllers\UsersController@createSeller')->name('users.sellers.create');
                Route::post('store', 'App\Http\Controllers\UsersController@storeSeller')->name('users.sellers.store');
                Route::get('edit/{id}', 'App\Http\Controllers\UsersController@editSeller')->name('users.sellers.edit');
                Route::post('update/{id}', 'App\Http\Controllers\UsersController@updateSeller')->name('users.sellers.update');
                Route::get('delete/{id}', 'App\Http\Controllers\UsersController@deleteSeller')->name('users.sellers.delete');
                Route::get('detail/{id}', 'App\Http\Controllers\UsersController@showSeller')->name('users.sellers.show');
            });
        });

        Route::prefix('orders')->group(function() {
            Route::get('/', 'App\Http\Controllers\OrdersController@index')->name('orders');
            Route::get('v/{invoice}', 'App\Http\Controllers\OrdersController@view')->name('orders.view');
            Route::post('delete/{invoice}', 'App\Http\Controllers\OrdersController@cancel')->name('orders.cancel');
            Route::post('cetak', 'App\Http\Controllers\OrdersController@cetak')->name('orders.cetak');
        });

        Route::prefix('billing')->group(function() {
            Route::get('/', 'App\Http\Controllers\OrdersController@billing')->name('billing');
            Route::prefix('withdrawal')->group(function() {
                Route::get('/', 'App\Http\Controllers\OrdersController@withdrawal')->name('withdrawal');
                Route::post('store', 'App\Http\Controllers\OrdersController@storeRequest')->name('withdrawal.store');
                Route::get('v/{invoice}', 'App\Http\Controllers\OrdersController@viewRequest')->name('withdrawal.view');
                Route::get('c/{invoice}', 'App\Http\Controllers\OrdersController@cancelRequest')->name('withdrawal.cancel');
            });
        });

        Route::prefix('pages')->group(function () {
            Route::get('/', 'App\Http\Controllers\PagesController@index')->name('pages');
            Route::get('create', 'App\Http\Controllers\PagesController@create')->name('pages.create');
            Route::post('store', 'App\Http\Controllers\PagesController@store')->name('pages.store');
            Route::get('edit/{id}', 'App\Http\Controllers\PagesController@edit')->name('pages.edit');
            Route::post('update/{id}', 'App\Http\Controllers\PagesController@update')->name('pages.update');
            Route::get('delete/{id}', 'App\Http\Controllers\PagesController@delete')->name('pages.delete');
            Route::get('detail/{id}', 'App\Http\Controllers\PagesController@detail')->name('pages.detail');
        });

        Route::prefix('settings')->group(function () {
            Route::get('/', 'App\Http\Controllers\MoreController@index')->name('settings');
            Route::get('media', 'App\Http\Controllers\MoreController@media')->name('settings.media');
            Route::get('seo', 'App\Http\Controllers\MoreController@seo')->name('settings.seo');
            Route::get('surel', 'App\Http\Controllers\MoreController@surel')->name('settings.surel');
            Route::get('payment', 'App\Http\Controllers\MoreController@payment')->name('settings.payment');

            // post action
            Route::post('store', 'App\Http\Controllers\MoreController@store')->name('settings.store');
            Route::post('store-seo', 'App\Http\Controllers\MoreController@storeSeo')->name('settings.store.seo');
            Route::post('store-surel', 'App\Http\Controllers\MoreController@storeSurel')->name('settings.store.surel');
            Route::post('store-payment', 'App\Http\Controllers\MoreController@storePayment')->name('settings.store.payment');
            Route::post('store-media', 'App\Http\Controllers\MoreController@storeMedia')->name('settings.store.media');
        });

        Route::prefix('permission')->group(function () {
            Route::get('/', 'App\Http\Controllers\UsersController@permission')->name('permission');
            Route::get('create', 'App\Http\Controllers\UsersController@createPermission')->name('permission.create');
            Route::post('store', 'App\Http\Controllers\UsersController@storePermission')->name('permission.store');
            Route::get('edit/{id}', 'App\Http\Controllers\UsersController@editPermission')->name('permission.edit');
            Route::post('update/{id}', 'App\Http\Controllers\UsersController@updatePermission')->name('permission.update');
            Route::get('delete/{id}', 'App\Http\Controllers\UsersController@deletePermission')->name('permission.delete');
            Route::get('detail/{slug}', 'App\Http\Controllers\UsersController@detailPermission')->name('permission.detail');

            // about child permission
            Route::get('create-permission', 'App\Http\Controllers\UsersController@createChildPermission')->name('permission.create.child');
            Route::post('store-permission', 'App\Http\Controllers\UsersController@storeChildPermission')->name('permission.store.child');
            Route::get('edit-permission/{id}', 'App\Http\Controllers\UsersController@editChildPermission')->name('permission.edit.child');
            Route::post('update-permission/{id}', 'App\Http\Controllers\UsersController@updateChildPermission')->name('permission.update.child');
            Route::get('delete-permission/{id}', 'App\Http\Controllers\UsersController@deleteChildPermission')->name('permission.delete.child');
        });

        Route::prefix('roles')->group(function () {
           Route::get('/', 'App\Http\Controllers\UsersController@roles')->name('roles');
           Route::get('edit/{id}', 'App\Http\Controllers\UsersController@editRoles')->name('roles.edit');
           Route::post('update/{id}', 'App\Http\Controllers\UsersController@updateRoles')->name('roles.update');
        });

        Route::prefix('more')->group(function () {
            Route::get('/', 'App\Http\Controllers\MoreController@index')->name('more');
            Route::get('create', 'App\Http\Controllers\MoreController@create')->name('more.create');
            Route::post('store', 'App\Http\Controllers\MoreController@store')->name('more.store');
            Route::get('edit/{id}', 'App\Http\Controllers\MoreController@edit')->name('more.edit');
            Route::post('update/{id}', 'App\Http\Controllers\MoreController@update')->name('more.update');
            Route::get('delete/{id}', 'App\Http\Controllers\MoreController@delete')->name('more.delete');
            Route::get('detail/{id}', 'App\Http\Controllers\MoreController@detail')->name('more.detail');
        });

        Route::prefix('report')->group(function () {
            Route::get('cashflow', 'App\Http\Controllers\MoreController@cashflow')->name('report.cashflow');
            Route::get('statistics', 'App\Http\Controllers\MoreController@statistics')->name('report.statistics');
        });
    });
});
