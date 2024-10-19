<?php

// use App\Http\Controllers\Api\PaymentGategwayController;
use App\Http\Controllers\Api\PaymentGategwayController;
use App\Http\Controllers\ApiController\AddressController;
use App\Http\Controllers\ApiController\FlagController;
use App\Http\Controllers\ApiController\OfferPromotionController;
use App\Http\Controllers\ApiController\PaymentGatewayController;
use App\Http\Controllers\PromoCodeController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ApiController\Auth\Register;
use App\Http\Controllers\ApiController\EventController;
use App\Http\Controllers\ApiController\PostController;
use App\Http\Controllers\ApiController\ProductController;
use App\Http\Controllers\ApiController\ProductOrderController;
use App\Http\Controllers\ApiController\UserController;
use App\Http\Controllers\Apis\CategoryController;
use App\Models\ColorModel;
use App\Models\ProductSizeModel;
use App\Models\SettingModel;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});


// Auth Route
Route::POST('register', [Register::class, 'store']);
Route::POST('login', [Register::class, 'login']);
Route::POST('forget_password_using_email', [Register::class, 'forget_password_using_email']);

Route::middleware('auth:sanctum')->group(function () {

    Route::POST('/email_otp_verified', [Register::class, 'email_otp_verified']);
    Route::POST('/create_password', [Register::class, 'create_password']);
    Route::POST('/images', [Register::class, 'images']);
    # Resend Otp Verification
    Route::get('/resend_otp', [Register::class, 'resendOtp']);

    #User Route
    Route::get('/user_types', [UserController::class, 'user_types']);
    Route::POST('/update_usertypes', [UserController::class, 'update_usertypes']);
    Route::POST('/update_user_profile', [UserController::class, 'update_profile']);
    Route::POST('/update_profile_image', [UserController::class, 'updateProfileImage']);

    #categories
    Route::get('/categories', [CategoryController::class, 'index']);
    Route::get('/colors', function () {
        return ColorModel::select(
            'color_name',
            'color_code',
        )->distinct()
            ->orderBy('color_name')
            ->get();
    });

    # Store Post
    Route::resource('/post', PostController::class);
    Route::post('/post_reaction',[PostController::class,'postReaction']);
    Route::get('/get_post_reaction_count/{id}',[PostController::class,'reaction_count']);
    Route::get('/trending_post',[PostController::class,'TrendingPost']);
    Route::get('/post-according-to-desginer-id/{id}',[PostController::class,'postAccordingToDesginerId']);


    # Product APIs
    Route::POST('/get-product',[ProductController::class,'getProduct']);
    Route::post('/create_product', [ProductController::class, 'store']);
    Route::POST('/store_color_for_product/{productId}', [ProductController::class, 'ColorSizes']); // In this route color added when product create
    Route::get('/size', function () {
        return ProductSizeModel::all();
    });
    Route::POST('/product_price_category_subcategory', [ProductController::class, 'product_price_category_subcategory']); // This is the Api update the product table column categoryid , subcategoryid , price
    Route::post('/upload-product-images', [ProductController::class, 'uploadBase64ImagesProduct']); // in this you can update also the record
    Route::get('/my-products', [ProductController::class, 'myProducts']);
    Route::get('/product-according-to-desginer-id/{id}',[ProductController::class,'productAccordingToDesginerId']);


    // Product ke update ke Api
    Route::POST('/update_product/{id}', [ProductController::class, 'update']);
    Route::post('/update_color_with_sizes/{productId}', [ProductController::class, 'updateColorAndSizes']);
    Route::POST('/update_price_category/{id}', [ProductController::class, 'updateProductPriceAndCategory']);


    #Address Api Route
    Route::resource('/address', AddressController::class);

    # Store Event
    Route::resource('/events', EventController::class);

    Route::post('/update_cover_image/{id}', [EventController::class, 'updateCoverImage']);

    # Search Route
    Route::get('/search_hashtag', [PostController::class, 'searchHashtag']);
    Route::post('/hashtag', [PostController::class, 'storeHashtag']);
    Route::get('/search_user', [UserController::class, 'searchUser']);

    # Fellows Route
    Route::POST('/fellows', [UserController::class, 'fellows']);

    # Get follower and following details
    Route::get('/get_follower_details',[UserController::class,'follower_details']);
    Route::get('/get_following_details',[UserController::class,'following_details']);
    Route::get('/get_following_post',[PostController::class,'getFollowingPost']);

    # Join Events Route
    Route::POST('/join_event', [EventController::class, 'joinEvent']);
    Route::get('/delete_join_event/{id}', [EventController::class, 'deleteJoin']);

    # Product Order Route
    Route::post('/add-to-cart',[ProductOrderController::class,'addToCart']);
    Route::get('/get-add-to-cart',[ProductOrderController::class,'getAddToCart']);
    Route::Post('/place_order', [ProductOrderController::class, 'store']);
    Route::delete('/product-delete/{id}',[ProductOrderController::class,'destroy']);
    Route::get('/order-details/{id}',[ProductOrderController::class,'orderDetails']);


    # Offer Promotion Routes
    Route::get('/get_offer_promotion',[OfferPromotionController::class,'index']);
    Route::post('/offer_promotion',[OfferPromotionController::class,'store']);
    Route::post('/update_offer_promotion/{id}',[OfferPromotionController::class,'update']);

    # Flag Post Routes
    Route::post('/flag_post',[FlagController::class,'postFlagged']);

    # Comments Routes
    Route::post('/flag_comment',[FlagController::class,'commentFlagged']);

    # Promo Code
    Route::get('/get-promo-code',[PromoCodeController::class,'getPromo']);

    # Payment Gateway
    Route::get('/get-payment-gateway',[PaymentGatewayController::class,'index']);
    // Route::post('/payment-store',[PaymentGatewayController::class,'store']);

    # Get Top Desginer
    Route::get('/get-top-desginer',[Register::class,'topDesigner']);


    
});

Route::get('/callback/{invoiceId}', [PaymentGatewayController::class, 'callBack']);


Route::get('/settings', function () {
    $brand_name = SettingModel::where('key', 'brand_name')->first();
    $default_currency = SettingModel::where('key', 'default_currency')->first();
    $service_fee = SettingModel::where('key', 'service_fee')->first();
    $delivery_fee = SettingModel::where('key', 'delivery_fee')->first();
    $tax = SettingModel::where('key', 'tax')->first();
    return response()->json([
        'brand_name' => $brand_name ? $brand_name->value : null,
        'default_currency' => $default_currency ? $default_currency->value : null,
        'service_fee' => $service_fee ? $service_fee->value : null,
        'delivery_fee' => $delivery_fee ? $delivery_fee->value : null,
        'tax' => $tax ? $tax->value : null

    ]);
});



