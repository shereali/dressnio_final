<?php

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\API\Backend\AuthController as AdminAuthController;
use App\Http\Controllers\API\Backend\DashboardController;
use App\Http\Controllers\API\Backend\PostController;
use App\Http\Controllers\API\Backend\SizeController;
use App\Http\Controllers\API\Backend\ColorController;
use App\Http\Controllers\API\Backend\MediaController;
use App\Http\Controllers\API\Backend\StoryController;
use App\Http\Controllers\API\Backend\SliderController;
use App\Http\Controllers\API\Backend\ProductController;
use App\Http\Controllers\API\Backend\CategoryController;
use App\Http\Controllers\API\Backend\ConfigureController;
use App\Http\Controllers\API\Backend\CustomerController;
use App\Http\Controllers\API\Backend\OrderController as AdminOrderController;
use App\Http\Controllers\API\Backend\PopupController;
use App\Http\Controllers\API\Backend\SettingController;
use App\Http\Controllers\API\Backend\TestimonialController;
use App\Http\Controllers\API\Backend\WatchAndBuyController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Public Admin Auth
Route::post('admin-login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');
Route::post('admin/login', [AdminAuthController::class, 'login'])->middleware('throttle:10,1');

// Authenticated admin routes
Route::middleware(['auth:sanctum'])->group(function () {
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/user-list', function () {
        return User::select('id', 'name', 'email')->get();
    });

    Route::post('create-user', [AdminAuthController::class, 'createUser']);
    Route::post('logout', [AdminAuthController::class, 'logout']);

    Route::get('dashboard/stats', [DashboardController::class, 'stats']);

    Route::apiResources([
        'category'     => CategoryController::class,
        'products'     => ProductController::class,
        'size'         => SizeController::class,
        'color'        => ColorController::class,
        'media'        => MediaController::class,
        'story'        => StoryController::class,
        'post'         => PostController::class,
        'video'        => MediaController::class,
        'watch-n-buy'  => WatchAndBuyController::class,
        'tag'          => MediaController::class,
        'slider'       => SliderController::class,
        'testimonial'  => TestimonialController::class,
        'orders'       => AdminOrderController::class,
        'customers'    => CustomerController::class,
        'popups'       => PopupController::class,
    ]);

    Route::get('orders/{order}/items', [AdminOrderController::class, 'show']);

    Route::get('settings', [SettingController::class, 'index']);
    Route::put('settings', [SettingController::class, 'update']);

    Route::post('facebook', [ConfigureController::class, 'facebook']);
    Route::post('instagram', [ConfigureController::class, 'instagram']);
});

// Public admin login
Route::post('admin-login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1');

/*
|--------------------------------------------------------------------------
| Client (storefront) Routes
|--------------------------------------------------------------------------
*/
use App\Http\Controllers\API\Frontend\HomeController;
use App\Http\Controllers\API\Frontend\ProductController as ClientProductController;
use App\Http\Controllers\API\Frontend\AuthController as ClientAuthController;
use App\Http\Controllers\API\Frontend\CartController as ClientCartController;
use App\Http\Controllers\API\Frontend\WishlistController as ClientWishlistController;
use App\Http\Controllers\API\Frontend\OrderController as ClientOrderController;
use App\Http\Controllers\API\Frontend\NewsletterController;
use App\Http\Controllers\API\Frontend\ContactController;
use App\Http\Controllers\API\Backend\SettingController as PublicSettingController;

// Public storefront
Route::prefix('client')->group(function () {
    Route::get('homepage', [HomeController::class, 'getHomePageData']);
    Route::get('highlight-product', [HomeController::class, 'getHighlightProduct']);
    Route::get('story-products/{slug}', [HomeController::class, 'getStoryProducts']);
    Route::get('category-details/{slug}', [HomeController::class, 'getCategoryDetails']);

    Route::get('product-details/{slug}', [ClientProductController::class, 'getProduct']);
    Route::get('products', [ClientProductController::class, 'catalog']);
    Route::get('best-selling-products', [ClientProductController::class, 'getBestSellingProducts']);
    Route::get('unstitched-products', [ClientProductController::class, 'getUnstitchedProducts']);
    Route::get('search', [ClientProductController::class, 'search']);
    Route::get('related-products/{slug}', [ClientProductController::class, 'related']);

    Route::get('posts', [ClientProductController::class, 'posts']);
    Route::get('posts/{slug}', [ClientProductController::class, 'post']);

    Route::get('popups', [HomeController::class, 'popups']);
    Route::get('settings', [PublicSettingController::class, 'publicSettings']);

    Route::post('newsletter', [NewsletterController::class, 'subscribe']);
    Route::post('contact', [ContactController::class, 'store']);

    // Auth
    Route::post('register', [ClientAuthController::class, 'register'])->middleware('throttle:5,1');
    Route::post('send-otp', [ClientAuthController::class, 'sendOtp'])->middleware('throttle:5,1');
    Route::post('verify-otp', [ClientAuthController::class, 'verifyOtp'])->middleware('throttle:10,1');
    Route::post('login', [ClientAuthController::class, 'login'])->middleware('throttle:10,1');

    // Public & Guest Checkout and Tracking
    Route::post('checkout', [ClientOrderController::class, 'checkout']);
    Route::get('my-orders/{orderNo}', [ClientOrderController::class, 'show']);

    // Authenticated customer routes
    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('me', [ClientAuthController::class, 'me']);
        Route::put('me', [ClientAuthController::class, 'updateProfile']);
        Route::post('logout', [ClientAuthController::class, 'logout']);

        Route::get('cart', [ClientCartController::class, 'index']);
        Route::post('cart', [ClientCartController::class, 'store'])->middleware('throttle:60,1');
        Route::put('cart/{id}', [ClientCartController::class, 'update']);
        Route::delete('cart/{id}', [ClientCartController::class, 'destroy']);
        Route::delete('cart', [ClientCartController::class, 'clear']);

        Route::get('wishlist', [ClientWishlistController::class, 'index']);
        Route::post('wishlist', [ClientWishlistController::class, 'store']);
        Route::delete('wishlist/{productId}', [ClientWishlistController::class, 'destroy']);

        Route::get('my-orders', [ClientOrderController::class, 'index']);
        Route::post('my-orders/{orderNo}/cancel', [ClientOrderController::class, 'cancel']);
    });
});
