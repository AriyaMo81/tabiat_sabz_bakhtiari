<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home.index');
})->name('home.index');


Route::get('about-us', function () {
    return view('about_us');
})->name('about.index');
