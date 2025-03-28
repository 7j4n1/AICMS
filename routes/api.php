<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberRecordsController;

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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::group(['prefix' => 'v1', 'middleware' => ['api', 'json.response']], function ($router) {
    // public route
    Route::get('/test', [AuthController::class, 'testOutput']);
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    Route::group(['prefix' => 'account', 'middleware' => 'jwt.verify'], function () {
        Route::get('profile', [AuthController::class, 'profile']);
        Route::get('balance', [MemberRecordsController::class, 'getBalance']);
        Route::get('savings', [MemberRecordsController::class, 'getSavingsRecords']);
        Route::get('shares', [MemberRecordsController::class, 'getSharesRecords']);
        Route::post('download-ledger', [MemberRecordsController::class, 'downloadLedger']);
    });
});
