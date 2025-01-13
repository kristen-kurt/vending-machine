<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\TransactionController;
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

// Route::get('/dashboard', function () {
//     return view('proe');
// })->middleware(['auth', 'verified'])->name('dashboard');


Route::middleware('auth')->group(function () {
    // Route::get('/dashboard', [ProductController::class, 'index'])->middleware(['auth', 'verified'])->name('products');
    Route::resource('products', ProductController::class);
    Route::post('products/{product}/purchase', [ProductController::class, 'purchase'])
        ->name('products.purchase');
    Route::get('/transactions', [TransactionController::class, 'index'])
        ->name('transactions.index');
});

require __DIR__.'/auth.php';
