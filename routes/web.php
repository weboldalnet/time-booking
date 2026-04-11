<?php

use App\Helpers\CustomPageHelper;

use App\Http\Controllers\Admin\Article\ArticleController;

Route::namespace('App\Http\Controllers\Site')->domain(getSiteDomain())->middleware('web', 'site_share')->group(function () {
    /** ----- Site oldali funkciók ----- */
    Route::namespace('TimeBooking')->group(function () {
    });
});

Route::namespace('App\Http\Controllers\Admin')->domain(getAdminDomain())->middleware('web', 'admin_share')->group(function () {
    /** ----- Admin oldali funkciók ----- */
    Route::middleware('auth:admin')->group(function () {
        Route::namespace('TimeBooking')->group(function () {
        });
    });
});
