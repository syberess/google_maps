<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\CompanySearchController;

Route::get('/', function () {
    return view('search');
})->name('home');

Route::get('/search', function () {
    return redirect()->route('home');
});

Route::post('/search', [CompanySearchController::class, 'search'])->name('company.search');
Route::get('/export', [CompanySearchController::class, 'export'])->name('company.export');
