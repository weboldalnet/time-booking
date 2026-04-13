<?php

Route::namespace('Weboldalnet\TimeBooking\Http\Controllers\Site')->domain(getSiteDomain())->middleware('web', 'site_share')->group(function () {
    /** ----- Site oldali funkciók ----- */
    Route::namespace('TimeBooking')->group(function () {
        // Időpontfoglalás
        Route::get('/idopontfoglalas', 'BookingController@index')->name('site.timebooking.index');
        Route::get('/idopontfoglalas/appointment/{appointment}', 'BookingController@show')->name('site.timebooking.appointment.show');
        Route::post('/idopontfoglalas/book', 'BookingController@store')->name('site.timebooking.book');
        Route::get('/idopontfoglalas/koszonjuk/{booking}', 'BookingController@thankYou')->name('site.timebooking.thank-you');

        // AJAX endpoints
        Route::get('/idopontfoglalas/category/{category}/appointments', 'BookingController@getAppointments')->name('site.timebooking.category.appointments');
        Route::get('/idopontfoglalas/appointment/{appointment}/availability', 'BookingController@checkAvailability')->name('site.timebooking.appointment.availability');
    });
});

Route::namespace('Weboldalnet\TimeBooking\Http\Controllers\Admin')->domain(getAdminDomain())->middleware('web', 'admin_share')->group(function () {
    /** ----- Admin oldali funkciók ----- */
    Route::middleware('auth:admin')->group(function () {
        Route::namespace('TimeBooking')->group(function () {
            // Kategóriák kezelése
            Route::resource('timebooking/categories', 'CategoryController', [
                'as' => 'admin.timebooking',
                'names' => [
                    'index' => 'admin.timebooking.categories.index',
                    'create' => 'admin.timebooking.categories.create',
                    'store' => 'admin.timebooking.categories.store',
                    'show' => 'admin.timebooking.categories.show',
                    'edit' => 'admin.timebooking.categories.edit',
                    'update' => 'admin.timebooking.categories.update',
                    'destroy' => 'admin.timebooking.categories.destroy',
                ]
            ]);
            Route::patch('timebooking/categories/{category}/toggle-status', 'CategoryController@toggleStatus')->name('admin.timebooking.categories.toggle-status');
            Route::patch('timebooking/categories/update-order', 'CategoryController@updateOrder')->name('admin.timebooking.categories.update-order');

            // Időpontok kezelése
            Route::resource('timebooking/appointments', 'AppointmentController', [
                'as' => 'admin.timebooking',
                'names' => [
                    'index' => 'admin.timebooking.appointments.index',
                    'create' => 'admin.timebooking.appointments.create',
                    'store' => 'admin.timebooking.appointments.store',
                    'show' => 'admin.timebooking.appointments.show',
                    'edit' => 'admin.timebooking.appointments.edit',
                    'update' => 'admin.timebooking.appointments.update',
                    'destroy' => 'admin.timebooking.appointments.destroy',
                ]
            ]);
            Route::get('timebooking/appointments/{appointment}/bookings', 'AppointmentController@bookings')->name('admin.timebooking.appointments.bookings');

            // Tömeges időpont létrehozás
            Route::get('timebooking/appointments/create/bulk', 'AppointmentController@createBulk')->name('admin.timebooking.appointments.create-bulk');
            Route::post('timebooking/appointments/store/bulk', 'AppointmentController@storeBulk')->name('admin.timebooking.appointments.store-bulk');
        });
    });
});
