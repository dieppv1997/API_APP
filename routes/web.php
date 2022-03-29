<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\AppController;
use App\Http\Controllers\Web\PictureBookController;
use App\Http\Controllers\Web\TermsController;
use App\Http\Controllers\Web\PrivacyController;

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

Route::get('/launch-app', [AppController::class, 'launchApp']);

Route::group([
    'prefix' => 'web-view',
], function () {
    Route::group([
        'prefix' => 'picture-book',
    ], function () {
        Route::get('/', [PictureBookController::class, 'frontPage'])->name('picture-book');
        Route::get('/search-result', [PictureBookController::class, 'searchResult'])->name('picture-book-search');
        Route::get('/detail', [PictureBookController::class, 'flowerDetail'])->name('picture-book-detail');
    });
    Route::get('/terms', [TermsController::class, 'terms'])->name('terms');
    Route::get('/privacy', [PrivacyController::class, 'privacy'])->name('privacy');
});
