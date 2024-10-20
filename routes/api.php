<?php

use App\Http\Controllers\InvoiceAdviceController;
use App\Http\Controllers\InvoiceAdviceListItemController;
use App\Http\Controllers\LetterTemplateController;
use App\Http\Controllers\NgmlAccountController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\TestController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/


Route::middleware('scope.user')->group(function () {
    Route::get('/protected', function () {
        return response()->json(['message' => 'Access granted']);
    });
    Route::get('invoice-advice', [InvoiceAdviceController::class, 'index']);
    Route::get('invoice-advice/{id}', [InvoiceAdviceController::class, 'show']);
    Route::delete('invoice-advice/{id}', [InvoiceAdviceController::class, 'destroy']);
    Route::get('invoice-advice-list-items', [InvoiceAdviceListItemController::class, 'index']);
    Route::get('invoice-advice-list-items/{id}', [InvoiceAdviceListItemController::class, 'show']);
    Route::delete('invoice-advice-list-items/{id}', [InvoiceAdviceListItemController::class, 'destroy']);
    Route::get('letter-templates', [LetterTemplateController::class, 'index']);
    Route::get('letter-templates/{id}', [LetterTemplateController::class, 'show']);
    Route::delete('letter-templates/{id}', [LetterTemplateController::class, 'destroy']);
    Route::get('ngml-accounts', [NgmlAccountController::class, 'index']);
    Route::get('ngml-accounts/{id}', [NgmlAccountController::class, 'show']);
    Route::delete('ngml-accounts/{id}', [NgmlAccountController::class, 'destroy']);
});



Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
