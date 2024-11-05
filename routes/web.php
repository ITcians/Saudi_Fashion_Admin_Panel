<?php

use App\Http\Controllers\ApiControl\PaymentGategwayController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ColorController;
use App\Http\Controllers\EventAttendenceController;
use App\Http\Controllers\EventController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SettingController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\FlagController;
use App\Http\Controllers\PaymentGatewayController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductSizeController;
use App\Http\Controllers\SubCategoryController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/privacy-policy', function () {
    return view('polices.privacypolices');
});

Route::get('/terms-conditions', function () {
    return view('polices.terms&conditions');
});





Route::get('/admin', [LoginController::class, 'index'])->name('admin.login-view');
Route::POST('/admin', [LoginController::class, 'adminLogin'])->name('admin.login');




Route::group(['middleware' => ['auth:admin']], function () {


    Route::POST('/admin/logout',[LoginController::class,'Logout'])->name('logout');

    Route::get('/admin/dashboard',[HomeController::class,'index'])->name('home.page');
    Route::get('/admin/profile_update/view', [LoginController::class, 'profile_update_view']);
    Route::post('/admin/update_profile',[LoginController::class , 'update_profile'])->name('update_profile');


   // Setting Routes
    Route::get('/admin/settings', [SettingController::class, 'settings']);
    // Route::post('/admin/update_email_config', [SettingController::class, 'updateEmailConfig'])->name('update_email_config');
    Route::post('/admin/update_setting', [SettingController::class, 'update_setting'])->name('update_setting');

    // User Route
    Route::get('/admin/user/index',[usercontroller::class, 'index'])->name('users.index');
    Route::POST('/admin/user/store',[usercontroller::class, 'store']);
    Route::get('/admin/user/edit/{id}',[usercontroller::class, 'edit'])->name('users.edit');
    Route::put('/admin/user/update{id}',[usercontroller::class, 'update'])->name('users.update');
    Route::get('/admin/user/destroy/{id}',[usercontroller::class,'destroy'])->name('users.destroy');
    Route::get('admin/update-status/{id}', [usercontroller::class, 'updateaccount_status']);


    // Event Route
    Route::get('/admin/event/index',[EventController::class, 'index'])->name('events.index');
    Route::POST('/admin/event/store',[EventController::class, 'store']);
    Route::get('/admin/event/edit/{id}',[EventController::class, 'edit'])->name('events.edit');
    Route::put('/admin/event/update/{id}',[EventController::class, 'update'])->name('events.update');
    Route::get('/admin/event/destroy/{id}',[EventController::class,'destroy'])->name('events.destroy');
    Route::get('/admin/event/update-status/{id}', [EventController::class, 'updateevent_status']);
    Route::get('/admin/event/view/{id}',[EventController::class, 'view']);


    // Post Route
    Route::get('/admin/post/index',[PostController::class, 'index'])->name('posts.index');
    Route::POST('/admin/post/store',[PostController::class, 'store']);
    Route::get('/admin/post/edit/{id}',[PostController::class, 'edit'])->name('posts.edit');
    Route::put('/admin/post/update/{id}',[PostController::class, 'update'])->name('posts.update');
    Route::get('/admin/post/destroy/{id}',[PostController::class,'destroy'])->name('posts.destroy');
    Route::get('/admin/post/update-status/{id}', [PostController::class, 'updateStatus']);
    Route::get('/admin/post/view/{id}',[PostController::class, 'view']);

    // flag Route
    Route::get('/admin/post/flag/edit/{id}',[FlagController::class,'edit']);
    Route::put('/admin/post/flag/store',[FlagController::class,'store']);

    // Category Route
    Route::get('/admin/category/index',[CategoryController::class, 'index'])->name('categorys.index');
    Route::POST('/admin/category/store',[CategoryController::class, 'store']);
    Route::get('/admin/category/edit/{id}',[CategoryController::class, 'edit'])->name('categorys.edit');
    Route::put('/admin/category/update/{id}',[CategoryController::class, 'update'])->name('categorys.update');
    Route::get('/admin/category/destroy/{id}',[CategoryController::class,'destroy'])->name('categorys.destroy');

    // Subcategory Route
    Route::get('/admin/subcategory/index',[SubCategoryController::class, 'index'])->name('subcategorys.index');
    Route::POST('/admin/subcategory/store',[SubCategoryController::class, 'store']);
    Route::get('/admin/subcategory/edit/{id}',[SubCategoryController::class, 'edit'])->name('subcategorys.edit');
    Route::put('/admin/subCategory/update/{id}',[SubCategoryController::class, 'update'])->name('subcategorys.update');
    Route::get('/admin/subcategory/destroy/{id}',[SubCategoryController::class,'destroy'])->name('subcategorys.destroy');

    // Product Route
    Route::get('/admin/product/index',[ProductController::class, 'index'])->name('products.index');
    Route::POST('/admin/product/store',[ProductController::class, 'store']);
    Route::get('/admin/product/edit/{id}',[ProductController::class, 'edit'])->name('products.edit');
    Route::put('/admin/product/update/{id}',[ProductController::class, 'update'])->name('products.update');
    Route::get('/admin/product/destroy/{id}',[ProductController::class,'destroy'])->name('products.destroy');
    Route::get('/admin/product/view/{id}',[ProductController::class, 'view']);

    // Color Route
    Route::get('/admin/color/index',[ColorController::class, 'index'])->name('colors.index');
    Route::POST('/admin/color/store',[ColorController::class, 'store']);
    Route::get('/admin/color/edit/{id}',[ColorController::class, 'edit'])->name('colors.edit');
    Route::put('/admin/color/update/{id}',[ColorController::class, 'update'])->name('colors.update');
    Route::get('/admin/color/destroy/{id}',[ColorController::class,'destroy'])->name('colors.destroy');

    // Event Attendence Route
    Route::get('/admin/eventattendence/index',[EventAttendenceController::class, 'index'])->name('eventattendences.index');
    Route::POST('/admin/eventattendence/store',[EventAttendenceController::class, 'store']);
    Route::get('/admin/eventattendence/edit/{id}',[EventAttendenceController::class, 'edit'])->name('eventattendences.edit');
    Route::put('/admin/eventattendence/update/{id}',[EventAttendenceController::class, 'update'])->name('eventattendences.update');
    Route::get('/admin/eventattendence/destroy/{id}',[EventAttendenceController::class,'destroy'])->name('eventattendences.destroy');

    // Product Size Route
    Route::get('/admin/productsize/index',[ProductSizeController::class, 'index'])->name('productsizes.index');
    Route::POST('/admin/productsize/store',[ProductSizeController::class, 'store']);
    Route::get('/admin/productsize/edit/{id}',[ProductSizeController::class, 'edit'])->name('productsizes.edit');
    Route::put('/admin/productsize/update/{id}',[ProductSizeController::class, 'update'])->name('productsizes.update');
    Route::get('/admin/productsize/destroy/{id}',[ProductSizeController::class,'destroy'])->name('productsizes.destroy');

    // Product Size Route
    Route::get('/admin/paymentgateway/index',[PaymentGatewayController::class, 'index'])->name('paymentgateways.index');
    Route::POST('/admin/paymentgateway/store',[PaymentGatewayController::class, 'store']);
    Route::get('/admin/paymentgateway/edit/{id}',[PaymentGatewayController::class, 'edit'])->name('paymentgateways.edit');
    Route::put('/admin/paymentgateway/update/{id}',[PaymentGatewayController::class, 'update'])->name('paymentgateways.update');
    Route::get('/admin/paymentgateway/destroy/{id}',[PaymentGatewayController::class,'destroy'])->name('paymentgateways.destroy');

    // Promo Code
    Route::get('/admin/promocode/index',[PromoCodeController::class,'index'])->name('promocode.index');
    Route::post('/admin/promocode/store',[PromoCodeController::class,'store']);
    Route::get('/admin/promocode/destroy/{id}',[PromoCodeController::class,'destroy']);


});

Route::get('/payment-declined' , function () {
    return view('paymentdeclained');
});

// Invoice
Route::get('/invoice',function () {
    return view('invoice');
});