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
    });
    
});

Route::get('/', function () {
    return route('login');
});

// Route::get('/login', function () {
//     return view('admin.authentication.login');
// })->name('login');

// Route::get('/members/{member_id}', [ListMembers::class,'editMember'])->name('getById');
