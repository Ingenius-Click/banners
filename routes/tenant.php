<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Tenant Routes
|--------------------------------------------------------------------------
|
| Here is where you can register tenant-specific routes for your package.
| These routes are loaded by the RouteServiceProvider within a group which
| contains the tenant middleware for multi-tenancy support.
|
*/

// Route::get('tenant-example', function () {
//     return 'Hello from tenant-specific route! Current tenant: ' . tenant('id');
// });
Route::middleware(['api', 'tenant.user'])
    ->prefix('api')->group(function() {
        Route::prefix('banners')->group(function(){
            Route::get('/', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'groupedByPlacements'])
                ->middleware('tenant.has.feature:list-banners')
                ;
            Route::get('/{banner}', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'show'])
                ->middleware('tenant.has.feature:view-banner')
                ;
            Route::post('/', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'store'])
                ->middleware(['tenant.has.feature:create-banner'])
                ;
            Route::put('/{banner}', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'update'])
                ->middleware(['tenant.has.feature:update-banner'])
                ;
            Route::delete('/{banner}', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'destroy'])
                ->middleware(['tenant.has.feature:delete-banner'])
                ;
            Route::get('/extra/placements', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'getPlacements']);
            Route::get('/extra/content-types', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'getContentTypes']);
            Route::get('/extra/targets', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'getTargets']);
        });

        Route::prefix('banner-edit-view')->group(function(){
            Route::get('/{banner}', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'editView'])
                ->middleware('tenant.has.feature:view-banner')
                ;
        });
    });

Route::middleware(['api'])
    ->prefix('api')->group(function() {
        Route::get('/active-banners/{position}', [\Ingenius\Banners\Http\Controllers\BannersController::class, 'getActiveBannersByPosition']);
    });