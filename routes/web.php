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

Route::get('/', function () {
    return view('welcome');
});
Route::get('/members', function() {
    return view('admin.members.index');
})->name('members');
Route::get('/login', function () {
    // return view('livewire.auth.login');
    return "Login here!!!";
})->name('login');

Route::get('/members/{member_id}', [ListMembers::class,'editMember'])->name('getById');
