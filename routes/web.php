<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\GeoIPController;


Route::get('/', [GeoIPController::class, 'index'])->name('geoip.index');
Route::post('/query', [GeoIPController::class, 'query'])->name('geoip.query');
Route::resource('geoip', GeoIPController::class)->only(['store']);


