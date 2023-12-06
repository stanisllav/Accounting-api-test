<?php

use App\Http\Controllers\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/
Route::middleware('auth:sanctum')->group(function () {

    Route::get('/user', function (Request $request) {
        return $request->user();
    });


    Route::get('transactions/income-sum', [TransactionController::class, 'incomeSum'])->name('transactions.incomeSum');
    Route::get('transactions/outcome-sum', [TransactionController::class, 'outcomeSum'])->name('transactions.outcomeSum');
    Route::get('transactions/total-sum', [TransactionController::class, 'totalSum'])->name('transactions.totalSum');
    Route::get('transactions/balance', [TransactionController::class, 'balance'])->name('transactions.balance');

    Route::apiResource('transactions', TransactionController::class)->only([
        'index', 'store', 'show', 'destroy'
    ]);


});


require __DIR__.'/auth.php';
