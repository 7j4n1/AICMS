<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\MemberController;
use App\Http\Controllers\PaymentController;
use App\Livewire\Admin\Reports\GeneralLedger;
use App\Livewire\Admin\Reports\IndividualLedger;
use App\Livewire\Members\ListMembers;
use Illuminate\Support\Facades\Route;

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



Route::middleware('auth:admin')->group(function () {
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

        Route::get('/report/individual_download/{id}/{beginning_date}/{ending_date}', [IndividualLedger::class, 'downloadLedger'])->name('individualReportDownload');
        Route::get('/report/general_download/{beginning_date}/{ending_date}/{from_number}/{to_number}', [GeneralLedger::class, 'downloadLedger'])->name('generalReportDownload');

        Route::get('/import/members', [ImportController::class, 'index'])->name('importMembers');

        Route::post('/import/members', [ImportController::class, 'import'])->name('newImport');
        Route::post('/import/loans', [ImportController::class, 'importLoan'])->name('newImportLoan');

        Route::get('/import/getprev_loans', [ImportController::class, 'loadAllLoan'])->name('getPrevLoans');
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
    });

    
});

// Route::get('/login', function () {
//     return view('admin.authentication.login');
// })->name('login');

// Route::get('/members/{member_id}', [ListMembers::class,'editMember'])->name('getById');
