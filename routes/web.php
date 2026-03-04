<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\PollController;
use App\Http\Controllers\Frontend\PollController as FrontendPollController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::name('admin.')->prefix('admin')->middleware(['auth', 'role:admin'])->group(function () {
  Route::get('/dashboard',                  [AdminController::class, 'dashboard'])->name('dashboard');

  Route::post('polls/{poll}/toggle-status', [PollController::class, 'toggleStatus'])->name('polls.toggle-status');
  Route::resource('polls',                   PollController::class);
});


Route::prefix('polls')->name('frontend.polls.')->group(function () {
    Route::get('/',                [FrontendPollController::class, 'index'])->name('index');
    Route::post('/{poll}/vote',    [FrontendPollController::class, 'vote'])->name('vote');
});