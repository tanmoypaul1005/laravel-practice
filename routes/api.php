<?php

use App\Http\Controllers\AddressController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\FlightController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReviewController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

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

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::get('/product', [ProductController::class, 'getProduct']);
Route::post('/product/add', [ProductController::class, 'addProduct']);
Route::get('/product/details', [ProductController::class, 'getProductDetails']);
Route::any('/product/search', [ProductController::class, 'searchProduct']);
Route::any('/product/filter', [ProductController::class, 'productPriceFilter']);


Route::any('/review/add', [ReviewController::class, 'addReview']);
Route::any('/review', [ReviewController::class, 'getReviews']);


Route::get('/category', [FlightController::class, 'getCategories']);
Route::post('/address/add', [AddressController::class, 'addAddress']);
Route::post('/register', [UserController::class, 'register']);
Route::post('/login', [UserController::class, 'login']);
Route::post('/send/otp', [UserController::class, 'generateOtp']);
Route::post('/otp/validate', [UserController::class, 'validateOtp']);


Route::group(['prefix' => 'cart'], function () {
    Route::any('add', [CartController::class, 'addCart']);
    Route::any('get', [CartController::class, 'getCart']);
    Route::any('update', [CartController::class, 'updateCartQuantity']);
    // Route::any('get-cards', [PostCardController::class, 'index'])->name('shop_cards');
});

Route::group(['prefix' => 'order'], function () {
    Route::any('add', [OrderController::class, 'addOrder']);
    Route::any('status-change', [OrderController::class, 'changeOrderStatus']);
    Route::any('get', [OrderController::class, 'getOrder']);
    // Route::any('get-cards', [PostCardController::class, 'index'])->name('shop_cards');
});

