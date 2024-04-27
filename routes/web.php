<?php

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
Route::middleware('guest:admin')->group(function () {
    Route::get('/login', function () {
        return view('admin.authentication.login');
    })->name('login');
});

Route::get('/admin/individual-download', function() {
    return view('admin.reports.report_view');
})->name('individualReportDownload');
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

        Route::get('/individual-report', function() {
            return view('admin.reports.individual_report');
        })->name('individualReport');

        // Route::get('/individual-download', function() {
        //     return view('admin.reports.report_view');
        // })->name('individualReportDownload');

        Route::get('/logout', function () {
            auth('admin')->logout();
            return redirect()->route('login');
        })->name('logout');
    });

    
});

Route::get('/', function () {
    return route('login');
});

// Route::get('/login', function () {
//     return view('admin.authentication.login');
// })->name('login');

// Route::get('/members/{member_id}', [ListMembers::class,'editMember'])->name('getById');
