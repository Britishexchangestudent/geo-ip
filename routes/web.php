<?php

use Illuminate\Support\Facades\Route;


use App\Http\Controllers\GeoIPController;

Route::get('/', [GeoIPController::class, 'showForm'])->name('geoip.show');
Route::post('/query', [GeoIPController::class, 'query'])->name('geoip.query');

