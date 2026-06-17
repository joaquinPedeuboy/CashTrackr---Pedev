<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\BudgetController;
use App\Http\Controllers\LogoutController;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/auth/register', [RegisterController::class, 'index'])->name('register');
Route::post('/auth/register', [RegisterController::class, 'store'])->name('register.store');

Route::get('/auth/login', [LoginController::class, 'index'])->name('login');
Route::post('/auth/login', [LoginController::class, 'store'])->name('login.store');

Route::post('/auth/logout', [LogoutController::class, 'store'])->name('logout.store');

Route::get('/email/verify/{id}/{hash}', function (EmailVerificationRequest $request) {
    $request->fulfill();
    return redirect()->route('dashboard')->with('success', 'Tu correo fue verificado correctamente. Ya puedes crear Presupuestos y Gastos.');
})->middleware(['auth', 'signed'])->name('verification.verify');

Route::get('/email/verify', function() {
    return view('auth.verify-email');
})->middleware(['auth'])->name('verification.notice');

Route::post('/email/verification-notification', function(Request $request) {
    $request->user()->sendEmailVerificationNotification();
    return back()->with('success', 'Correo de verificación reenviado correctamente');
})->middleware(['auth', 'throttle:1,1'])->name('verification.send'); // throttle para limitar el número de solicitudes de reenvío

Route::prefix('dashboard')->group(Function() {
    Route::get('/', [BudgetController::class, 'index'])->name('dashboard');
    Route::get('/budgets/create', [BudgetController::class, 'create'])->name('budgets.create');
    Route::post('/budgets', [BudgetController::class, 'store'])->name('budgets.store');

    Route::get('/budgets/{budget}/edit', [BudgetController::class, 'edit'])->name('budgets.edit');
    Route::put('/budgets/{budget}', [BudgetController::class, 'update'])->name('budgets.update');
    Route::delete('/budgets/{budget}', [BudgetController::class, 'destroy'])->name('budgets.destroy');
});