<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\MainController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::group(['prefix'=>'v1','namespace'=>'API'],function(){
    Route::post('register',[AuthController::class,'register']);
    Route::post('login',[AuthController::class,'login']);
    Route::post('reset-password',[AuthController::class,'resetPassword']);
    Route::post('password',[AuthController::class,'password']);
    Route::post('contact',[MainController::class,'contact']);
    Route::get('product-reviews',[MainController::class,'getReviewsByProduct']);
    Route::get('reviews',[MainController::class,'getReviews']);
    Route::get('get-product',[MainController::class,'getProduct']);
    Route::get('top-seles-products',[MainController::class,'productsTopSeles']);
    Route::get('popular-products',[MainController::class,'getProductsPopular']);
    Route::get('category-products',[MainController::class,'getProductsByCategory']);
    Route::group(['middleware'=>'auth:api'],function(){
        Route::get('profile',[AuthController::class,'profile']);
        Route::get('update-password',[AuthController::class,'updatePassword']);
        Route::post('add-address',[AuthController::class,'addAddress']);
        Route::get('my-address',[AuthController::class,'myAddresses']);
        Route::get('my-messages',[MainController::class,'myMessges']);
        Route::get('my-gifts',[MainController::class,'myGifts']);
        Route::post('add-order',[MainController::class,'newOrder']);
        Route::get('my-orders',[MainController::class,'myOrders']);
        Route::get('my-cart',[MainController::class,'myCart']);
        Route::post('register-token',[AuthController::class,'registerToken' ]);
        Route::post('remove-token',[AuthController::class ,'removeToken']);
        Route::get('my-reviews',[MainController::class,'getReviews']);
        Route::post('add-review',[MainController::class,'addReviews']);
        Route::post('payment-method',[MainController::class,'paymentMethod']);
        Route::post('payment-confirm',[MainController::class,'capture']);
    });

});

