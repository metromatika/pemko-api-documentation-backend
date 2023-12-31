<?php


use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\Collection\CollectionController;
use App\Http\Controllers\Api\Collection\CollectionSourceCodeController;
use App\Http\Controllers\Api\SourceCode\SourceCodeController;
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

Route::prefix('auth')->group(function () {
    Route::post('login', [AuthController::class, 'login']);
    Route::post('register', [App\Http\Controllers\Api\Auth\AuthController::class, 'register']);
    Route::post('logout', [AuthController::class, 'logout']);
    Route::post('refresh', [AuthController::class, 'refresh']);
    Route::get('me', [AuthController::class, 'me']);
    Route::post('verify-email', [AuthController::class, 'verifyEmail'])->name('email-verification.verify');
});

Route::apiResource('collection', CollectionController::class);

Route::middleware('auth')->group(function () {
    Route::get('collection/{collection}/source-code', [CollectionSourceCodeController::class, 'getSourceCodeByCollection']);

    Route::get('source-code/{source_code}/download', [SourceCodeController::class, 'download'])->name('source-code.download');
    Route::apiResource('source-code', SourceCodeController::class);
});
