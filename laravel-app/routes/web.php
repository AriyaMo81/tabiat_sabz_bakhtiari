<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ContactUsController;  

Route::get('/', function () {
    return view('home.index');
})->name('home.index');


Route::get('/about-us', function () {
    return view('about_us');
})->name('about.index');

Route::group(['prefix' => 'contact-us'], function () {
    Route::get('/', [ContactUsController::class, 'index'])->name('contact.index');
    Route::post('/', [ContactUsController::class, 'store'])->name('contact.store');
});
