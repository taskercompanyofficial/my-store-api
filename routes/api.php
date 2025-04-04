    <?php

    use App\Http\Controllers\Authenticated\ProductsController;
    use App\Http\Controllers\Authentication\AuthenticatedSessionController;
    use App\Http\Controllers\Authentication\GoogleLoginController;
    use App\Http\Controllers\Authentication\RegisterController;
    use App\Http\Controllers\FilesController;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\Route;

    Route::get('/user', function (Request $request) {
        return $request->user();
    })->middleware('auth:sanctum');

    Route::post('/auth/google', [GoogleLoginController::class, 'handleGoogleLogin']);

    Route::post('/auth/login', [AuthenticatedSessionController::class, 'store']);
    Route::post('/auth/check', [AuthenticatedSessionController::class, 'check']);

    Route::apiResource('/files', FilesController::class);
    Route::post('/auth/register', [RegisterController::class, 'store']);

    // Products routes - public access for fetching products
    Route::get('/products', [ProductsController::class, 'index']);
    Route::get('/products/{product}', [ProductsController::class, 'show']);

    // Protected routes that require authentication
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/products', [ProductsController::class, 'store']);
        Route::put('/products/{product}', [ProductsController::class, 'update']);
        Route::delete('/products/{product}', [ProductsController::class, 'destroy']);
    });