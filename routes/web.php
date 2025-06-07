<?php
use App\Http\Controllers\DashboardController; // Añade esto al inicio del archivo
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TransactionController; // <-- Esta línea es crucial
use App\Http\Controllers\CategoryController; // <-- Añade esta línea
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/dashboard', [DashboardController::class, 'index'])
    ->middleware(['auth', 'verified'])
    ->name('dashboard');
	
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
	// Rutas para la gestión de transacciones (protegidas por autenticación)
    Route::resource('transactions', TransactionController::class);
	// Rutas para Categorías (protegidas por autenticación)
    Route::resource('categories', CategoryController::class);
});



require __DIR__.'/auth.php';
