<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('login');
});

Route::get('/dashboard', function () {
    return view('dashboard');
})->name('dashboard');

Route::get('/peringatan', function () {
    return view('peringatan');
})->name('peringatan');

Route::get('/perangkat', function () {
    return view('perangkat');
})->name('perangkat');

Route::get('/riwayat', function () {
    return view('riwayat');
})->name('riwayat');

Route::get('/pengaturan', function () {
    return view('pengaturan');
})->name('pengaturan');
