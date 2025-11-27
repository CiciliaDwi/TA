<?php

use App\Http\Controllers\KategoriController;
use App\Http\Controllers\PredictionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ReportController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\UserController;
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
    return view('auth.login');
});

Route::get('/login', [UserController::class, 'showLogin'])->name('login.index');
Route::post('/login', [UserController::class, 'login'])->name('login');
Route::post('/logout', [UserController::class, 'logout'])->name('logout');
Route::get('/home', [App\Http\Controllers\TransactionController::class, 'dashboard'])->name('home');

Route::get('/users', [UserController::class, 'index'])
    ->middleware(['auth'])->name('users.index');

// pos
Route::get('/transactions', [TransactionController::class, 'index'])->name('transactions.index');
Route::post('/transactions', [TransactionController::class, 'store'])->name('transactions.store');
Route::resource('transactions', TransactionController::class);
Route::resource('users', UserController::class);
Route::get('/get-last-nota-number', [TransactionController::class, 'getLastNotaNumber'])->name('get-last-nota-number');

// produk
Route::resource('products', ProductController::class);
Route::get('/products', [ProductController::class, 'index'])
    ->middleware(['auth'])->name('products.index');

// kategori
Route::resource('categories', KategoriController::class);
Route::get('/categories', [KategoriController::class, 'index'])->name('categories.index');

// report
Route::get('/laporan', [ReportController::class, 'index'])->name('laporan.index');

// prediksi
// Route::get('/prediksi', [ReportController::class, 'index'])->name('laporan.index');
Route::get('/prediction', [PredictionController::class, 'showForm'])->name('prediction.form');
Route::post('/prediction', [PredictionController::class, 'predict'])->name('prediction.process');
