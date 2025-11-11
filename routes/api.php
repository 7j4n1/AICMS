<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\MemberRecordsController;
use App\Http\Controllers\Api\PaymentNotificationController;
use App\Http\Controllers\Api\V1\MemberController;
use App\Http\Controllers\Api\V1\LoanController;
use App\Http\Controllers\Api\V1\PaymentController;
use App\Http\Controllers\Api\V1\AnnualFeeController;
use App\Http\Controllers\Api\V1\AdminController;
use App\Http\Controllers\Api\V1\ItemCategoryController;
use App\Http\Controllers\Api\V1\ItemCaptureController;
use App\Http\Controllers\Api\V1\SystemConfigurationController;
use App\Http\Controllers\Api\V1\PaymentGatewayController;
use App\Http\Controllers\Api\V1\SupportTicketController;
use App\Http\Controllers\Api\V1\SavingsTypeController;
use App\Http\Controllers\Api\V1\LoanEligibilityController;

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
    
    // Authentication routes
    Route::prefix('auth')->group(function () {
        Route::post('/register', [AuthController::class, 'register']);
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/reset-password', [AuthController::class, 'resetPassword']);
    });

    // Protected routes requiring JWT authentication
    Route::group(['middleware' => ['jwt.verify']], function () {
        
        // Account/Profile routes
        Route::prefix('account')->group(function () {
            Route::get('profile', [AuthController::class, 'profile']);
            Route::get('balance', [MemberRecordsController::class, 'getBalance']);
            Route::get('savings', [MemberRecordsController::class, 'getSavingsRecords']);
            Route::get('shares', [MemberRecordsController::class, 'getSharesRecords']);
            Route::post('download-ledger', [MemberRecordsController::class, 'downloadLedger']);
        });

        Route::post('loan-eligibility/calculate', [LoanEligibilityController::class, 'calculate']);

        //Admin-only routes
        Route::middleware(['check.api.permission:can view,api'])->group(function () {
            Route::apiResource('members', MemberController::class);
            Route::get('members/coop/{coopId}', [MemberController::class, 'getByCoopId']);

            Route::apiResource('loans', LoanController::class);
            Route::get('active-loans', [LoanController::class, 'activeLoans']);

            Route::apiResource('payments', PaymentController::class);
            Route::apiResource('annual-fees', AnnualFeeController::class);

            // Business/Items Management
            Route::apiResource('categories', ItemCategoryController::class);
            Route::apiResource('items', ItemCaptureController::class);
        });
        // Routes requiring 'can edit' permission
        Route::middleware(['check.api.permission:can edit,api'])->group(function () {
            Route::post('loans/{id}/complete', [LoanController::class, 'complete']);

            Route::get('loan-eligibility/active', [LoanEligibilityController::class, 'getActive']);
        });

        // Super Admin Routes protected by 'configure-system' permission
        Route::middleware(['check.api.permission:configure-system,api'])->group(function () {
            // Admin Management
            Route::apiResource('admins', AdminController::class);
            
            // System Configurations
            Route::prefix('configurations')->group(function () {
                Route::get('/', [SystemConfigurationController::class, 'index']);
                Route::post('/', [SystemConfigurationController::class, 'update']);
                Route::post('/logo', [SystemConfigurationController::class, 'uploadLogo']);
                Route::get('/logo', [SystemConfigurationController::class, 'getLogo']);
            });

            // Payment Gateways
            Route::apiResource('payment-gateways', PaymentGatewayController::class);
            // Savings Types
            Route::apiResource('savings-types', SavingsTypeController::class);
            // Loan Eligibility Settings
            Route::apiResource('loan-eligibility-settings', LoanEligibilityController::class);
        });
        
        // Support Tickets (All authenticated users)
        Route::apiResource('support-tickets', SupportTicketController::class);
        Route::post('support-tickets/{id}/messages', [SupportTicketController::class, 'addMessage']);

        // Payment Notifications (Members can create, Admins can approve/reject)
        Route::prefix('payment-notifications')->group(function () {
            Route::get('/', [PaymentNotificationController::class, 'index']);
            Route::post('/', [PaymentNotificationController::class, 'store']);
            Route::get('/{id}', [PaymentNotificationController::class, 'show']);
            
            // Admin-only routes
            Route::middleware(['check.api.permission:can edit,api'])->group(function () {
                Route::post('/{id}/approve', [PaymentNotificationController::class, 'approve']);
                Route::post('/{id}/reject', [PaymentNotificationController::class, 'reject']);
            });
        });
    });
});
