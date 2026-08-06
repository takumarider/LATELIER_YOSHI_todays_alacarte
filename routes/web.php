<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ReservationController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::get('/alacarte', function () {
    $today = now()->toDateString();

    $products = \App\Models\Product::query()
        ->where('is_active', true)
        ->where(function ($q) use ($today) {
            $q->whereNull('sale_date')->orWhere('sale_date', $today);
        })
        ->with('inventory')
        ->orderBy('created_at')
        ->get();

    $futureProducts = \App\Models\Product::query()
        ->where('is_active', true)
        ->where('sale_date', '>', $today)
        ->orderBy('sale_date')
        ->get();

    return view('dashboard', compact('products', 'futureProducts'));
})->middleware(['auth', 'verified'])->name('alacarte');

Route::get('/dashboard', function () {
    return redirect()->route('alacarte');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/reserve/{product}', [ReservationController::class, 'create'])->name('reservations.create');
Route::post('/reserve/{product}', [ReservationController::class, 'store'])->name('reservations.store');
Route::get('/reserve/complete/{reservation}', [ReservationController::class, 'complete'])->name('reservations.complete');
Route::post('/member/reserve/{product}', [ReservationController::class, 'memberStore'])->middleware(['auth'])->name('reservations.member.store');

Route::middleware('auth')->group(function () {
    Route::get('/mypage', function () {
        $reservations = \App\Models\Reservation::query()
            ->where('user_id', auth()->id())
            ->with('product')
            ->latest('created_at')
            ->get();

        return view('mypage', compact('reservations'));
    })->name('mypage');

    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
