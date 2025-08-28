<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// routes/web.php
use App\Http\Controllers\catalog;

Route::get('/katalog', [catalog::class, 'index'])->name('catalog.index');

// Halaman kasir (dummy, tanpa backend)
Route::get('/kasir', function () {
    return view('cashier.index');
})->name('cashier.index');

// Jika mau batasi untuk admin saja, pakai ini:
// Route::middleware(['auth','admin'])->get('/kasir', fn() => view('cashier.index'))->name('cashier.index');
