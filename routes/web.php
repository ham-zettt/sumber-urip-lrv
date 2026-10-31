<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CashierController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\StockController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.store');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/dashboard/chart', [DashboardController::class, 'chart'])->name('dashboard.chart');

    Route::get('/kasir', [CashierController::class, 'index'])->name('kasir.index');
    Route::post('/kasir', [CashierController::class, 'store'])->name('kasir.store');

    Route::get('/transaksi', [TransactionController::class, 'index'])->name('transaksi.index');
    Route::get('/transaksi/{transaction}', [TransactionController::class, 'show'])->name('transaksi.show');
    Route::post('/transaksi/{transaction}/batal', [TransactionController::class, 'cancel'])->name('transaksi.cancel');

    Route::resource('produk', ProductController::class)->names([
        'index' => 'produk.index',
        'create' => 'produk.create',
        'store' => 'produk.store',
        'edit' => 'produk.edit',
        'update' => 'produk.update',
        'destroy' => 'produk.destroy',
    ])->parameters([
        'produk' => 'product',
    ])->except(['show']);

    Route::get('/stok', [StockController::class, 'index'])->name('stok.index');
    Route::get('/stok/tambah', [StockController::class, 'create'])->name('stok.create');
    Route::post('/stok', [StockController::class, 'store'])->name('stok.store');
});