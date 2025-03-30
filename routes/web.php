<?php

use Illuminate\Http\Request;
use App\Livewire\Members\ListMembers;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\BusinessController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\DatabaseController;
use App\Http\Controllers\ItemRepayController;
use App\Livewire\Admin\Reports\GeneralLedger;
use App\Http\Controllers\ItemCaptureController;
use App\Livewire\Admin\Reports\IndividualLedger;

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
Route::get('/', function () {
    return redirect()->route('login');
});

Route::middleware('guest:admin')->group(function () {
    Route::get('/login', function () {
        return view('admin.authentication.login');
    })->name('login');
});

// Route::get('/admin/individual-download', function() {
//     return view('admin.reports.report_view');
// })->name('individualReportDownload');
Route::get('/admin/download', function() {
    return view('livewire.admin.reports.individual-ledger-download');
})->name('individualReport1');

Route::middleware(['auth:admin', 'check.member.role'])->group(function () {
    Route::group(['prefix' => 'user'], function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard.index');
        })->name('user.dashboard');

        Route::get('/report/purchase-history', function() {
            return view('business.reports.my-history');
        })->name('user.purchase.individualReport');

        Route::get('/my/report', function() {
            return view('admin.reports.individual_report');
        })->name('user.individualReport');

        Route::get('/report/individual_download/{id}/{beginning_date}/{ending_date}', [IndividualLedger::class, 'downloadLedger'])->name('individualReportDownload');
        Route::get('/report/general_download/{beginning_date}/{ending_date}/{from_number}/{to_number}', [GeneralLedger::class, 'downloadLedger'])->name('generalReportDownload');

        Route::get('/logout', function () {
            auth('admin')->logout();
            return redirect()->route('login');
        })->name('user.logout');
    });
});

Route::middleware(['auth:admin', 'check.admin.role'])->group(function () {
    Route::group(['prefix' => 'admin'], function () {
        Route::get('/dashboard', function () {
            return view('admin.dashboard.index');
        })->name('dashboard');
        Route::get('/members', function() {
            return view('admin.members.index');
        })->name('members');
        Route::get('/loans', function() {
            return view('admin.accounts.loan');
        })->name('loans');
        Route::get('/payments', function() {
            return view('admin.accounts.payment');
        })->name('payments');

        Route::get('/admins', function() {
            return view('admin.members.admin');
        })->name('admins');

        Route::get('/check-guarantor', function() {
            return view('admin.accounts.check');
        })->name('checkGuarantor');

        Route::get('/annual-fees', function() {
            return view('admin.accounts.annual-fee');
        })->name('annualFees');

        Route::get('/report/individual-report', function() {
            return view('admin.reports.individual_report');
        })->name('individualReport');

        Route::get('/report/general-report', function() {
            return view('admin.reports.general_report');
        })->name('generalReport');

        Route::get('/report/monthly-shares-report', function() {
            return view('admin.reports.shares_report');
        })->name('sharesReport');

        Route::get('/report/loan-repayment-report', function() {
            return view('admin.reports.activeloans_report');
        })->name('activeLoansReport');

        Route::get('/report/loan-defaulter-report', function() {
            return view('admin.reports.defaulterloans_report');
        })->name('defaulterLoansReport');

        Route::get('/report/purchase-history', function() {
            return view('business.reports.my-history');
        })->name('purchase.individualReport');

        Route::get('/report/individual_download/{id}/{beginning_date}/{ending_date}', [IndividualLedger::class, 'downloadLedger'])->name('individualReportDownload');
        Route::get('/report/general_download/{beginning_date}/{ending_date}/{from_number}/{to_number}', [GeneralLedger::class, 'downloadLedger'])->name('generalReportDownload');

        Route::get('/import/members', [ImportController::class, 'index'])->name('importMembers');

        Route::post('/import/members', [ImportController::class, 'import'])->name('newImport');
        Route::post('/import/loans', [ImportController::class, 'importLoan'])->name('newImportLoan');
        Route::post('/import/prev_payments', [ImportController::class, 'importLoanFromCsv'])->name('newImportPrevLoan');

        // Route::get('/import/getprev_loans', [ImportController::class, 'loadAllLoan'])->name('getPrevLoans');
        // for uploading the 2023 ledger csv file
        Route::post('/import/ledgers/prev_ledger_2023', [HomeController::class, 'setLastLedger'])->name('lastledger2023');

        Route::get('/import/setdate', [HomeController::class, 'setPaymentDate'])->name('setPaymentDate');

        Route::get('/clearall', [HomeController::class, 'clearLoans'])->name('clearall');

        Route::get('/logout', function () {
            auth('admin')->logout();
            return redirect()->route('login');
        })->name('logout');

        // Import routes for members and loans
        Route::get('/import/allusers', [MemberController::class, 'index'])->name('importallMembers');
        Route::get('/import/activeloans', [ImportController::class, 'indexLoan'])->name('importLoans');
        // post routes for importing members and loans
        Route::post('/import/allmembers', [MemberController::class, 'import'])->name('member_import');
        Route::post('/import/activeloans', [PaymentController::class, 'importActiveLoans'])->name('loan_import');
        Route::post('/import/allledgers', [PaymentController::class, 'import'])->name('payment_import');

        Route::get('/exports', function() {
            return view('export');

        })->name('exports');
        Route::get('/export/members', [MemberController::class, 'export'])->name('exportMembers');

        Route::get('/export/loans', [PaymentController::class, 'exportActiveLoans'])->name('exportLoans');
        Route::get('/export/payments', [PaymentController::class, 'export'])->name('exportLedgers');

        Route::get('/generatelogins', [MemberController::class, 'generateLoginDetails'])->name('generateLogins');

        Route::get('/import/csv/members', function(Request $request) {
            return view('admin.import.import_member_csv');
        })->name('importMembersCsv');

        // Upload Chunk File
        Route::post('/upload-chunk', function(Request $request) {

            if($request->hasFile('chunk')) {
                $chunk = $request->file('chunk');

                // store chunk in temporary directory
                $path = $chunk->store('chunks', 'local');

                return response()->json([
                    'success' => true,
                    'message' => 'Chunk file uploaded successfully',
                    'path' => $path
                ]);
            }

            return response()->json([
                'success' => false,
                'message' => 'No chunk file uploaded'
            ], 400);
        });
    });


    Route::group(['prefix' => 'admin/backup'], function () {
        Route::get('/', [DatabaseController::class, 'index'])->name('backup.index');
        Route::get('/new', [DatabaseController::class, 'backup'])->name('backup.new');
        Route::get('/download/{file}', [DatabaseController::class, 'download'])->name('backup.download');
        Route::get('/clear/{file}', [DatabaseController::class, 'delete'])->name('backup.delete');
    });

    Route::group(['prefix' => 'admin/business'], function () {
        Route::get('/dashboard', function () {
            // return view('business.dashboard.index');
            return "Business Dashboard";
        })->name('business.dashboard');

        Route::get('/categories', function() {
            return view('business.items.category');
        })->name('business.categories');

        Route::get('/items', function() {
            return view('business.items.item');
        })->name('business.items');

        Route::get('/repayments', function() {
            return view('business.items.repay');
        })->name('business.repays');

        Route::get('/report/individual-history', function() {
            return view('business.reports.individual-history');
        })->name('business.individualReport');

        Route::get('/report/annual-history', function() {
            return view('business.reports.monthly-repayment');
        })->name('business.generalReport');

        Route::get('/report/active-purchases', function() {
            return view('business.reports.active-payment');
        })->name('business.activepurchases');

        Route::get('/report/repayment-defaulters', function() {
            return view('business.reports.repayment-default');
        })->name('business.repaymentDefaulters');

        Route::get('/import/business_data', [ItemCaptureController::class, 'index'])->name('importBusinessData');

        Route::post('/import/categories', [CategoryController::class, 'import'])->name('cat_import');
        Route::post('/import/itemcaps', [ItemCaptureController::class, 'import'])->name('itemcap_import');
        Route::post('/import/itemrepays', [ItemRepayController::class, 'import'])->name('repaycap_import');

        Route::get('/exports/business_data', function() {
            return view('business_export');
        })->name('business_exports');

        Route::get('/export/categories', [CategoryController::class, 'export'])->name('cat_export');
        Route::get('/export/itemcaps', [ItemCaptureController::class, 'export'])->name('itemcap_export');
        Route::get('/export/itemrepays', [ItemRepayController::class, 'export'])->name('repaycap_export');

        Route::get('/previous/import', [BusinessController::class, 'index'])->name('prev_repay_import');
        Route::post('/previous/upload', [BusinessController::class, 'importBusinessLoanFromCsv'])->name('prev_repay_upload');
    });
    
});

// Route::get('/login', function () {
//     return view('admin.authentication.login');
// })->name('login');

// Route::get('/members/{member_id}', [ListMembers::class,'editMember'])->name('getById');
