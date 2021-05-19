<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::middleware('auth')->group(function () {
    Route::resource('product-variant',\App\Http\Controllers\VariantController::class);
    Route::post('product/images',[\App\Http\Controllers\ProductController::class,'storeImages']);
    Route::post('product/removeImage',[\App\Http\Controllers\ProductController::class,'removeImage']);
    Route::get('product/search',[\App\Http\Controllers\ProductController::class,'search'])->name('search');
    Route::resource('product', \App\Http\Controllers\ProductController::class);
});