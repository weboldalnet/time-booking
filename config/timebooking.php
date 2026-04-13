<?php

return [
    /*
    |--------------------------------------------------------------------------
    | TimeBooking Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration options for the TimeBooking package.
    | You can publish this config file to your application's config directory
    | and modify the values as needed.
    |
    */

    /*
    |--------------------------------------------------------------------------
    | Database Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the database table names used by the TimeBooking package.
    |
    */
    'database' => [
        'tables' => [
            'categories' => 'timebooking_categories',
            'appointments' => 'timebooking_appointments',
            'bookings' => 'timebooking_bookings',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Route Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the routes used by the TimeBooking package.
    |
    */
    'routes' => [
        'admin' => [
            'prefix' => 'timebooking',
            'middleware' => ['web', 'admin_share', 'auth:admin'],
        ],
        'site' => [
            'prefix' => 'idopontfoglalas',
            'middleware' => ['web', 'site_share'],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | View Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the views used by the TimeBooking package.
    |
    */
    'views' => [
        'admin' => [
            'layout' => 'admin.layouts.layout',
            'path' => 'admin.timebooking',
        ],
        'site' => [
            'layout' => 'site.layouts.layout',
            'path' => 'site.elements.timebooking',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Asset Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the assets used by the TimeBooking package.
    |
    */
    'assets' => [
        'css_path' => 'timebooking/css',
        'js_path' => 'timebooking/js',
        'admin_css' => 'admin-timebooking.css',
        'admin_js' => 'admin-timebooking.js',
        'site_css' => 'site-timebooking.css',
        'site_js' => 'site-timebooking.js',
    ],

    /*
    |--------------------------------------------------------------------------
    | Booking Configuration
    |--------------------------------------------------------------------------
    |
    | Configure the booking behavior and limits.
    |
    */
    'booking' => [
        'default_capacity' => 1,
        'max_capacity' => 100,
        'auto_confirm' => true,
        'allow_past_bookings' => false,
        'booking_deadline_hours' => 2, // Hours before appointment when booking closes
    ],

    /*
    |--------------------------------------------------------------------------
    | Email Configuration
    |--------------------------------------------------------------------------
    |
    | Configure email notifications for bookings.
    |
    */
    'email' => [
        'enabled' => true,
        'from' => [
            'address' => env('MAIL_FROM_ADDRESS', 'hello@example.com'),
            'name' => env('MAIL_FROM_NAME', 'Example'),
        ],
        'templates' => [
            'booking_confirmation' => 'timebooking::emails.booking-confirmation',
            'booking_cancelled' => 'timebooking::emails.booking-cancelled',
            'admin_notification' => 'timebooking::emails.admin-notification',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Pagination Configuration
    |--------------------------------------------------------------------------
    |
    | Configure pagination settings for admin lists.
    |
    */
    'pagination' => [
        'categories_per_page' => 20,
        'appointments_per_page' => 20,
        'bookings_per_page' => 50,
    ],

    /*
    |--------------------------------------------------------------------------
    | Cache Configuration
    |--------------------------------------------------------------------------
    |
    | Configure caching for better performance.
    |
    */
    'cache' => [
        'enabled' => true,
        'ttl' => 3600, // Cache TTL in seconds (1 hour)
        'keys' => [
            'categories' => 'timebooking.categories',
            'appointments' => 'timebooking.appointments',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Validation Rules
    |--------------------------------------------------------------------------
    |
    | Configure validation rules for forms.
    |
    */
    'validation' => [
        'category' => [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'sort_order' => 'nullable|integer|min:0',
        ],
        'appointment' => [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'capacity' => 'required|integer|min:1|max:100',
        ],
        'booking' => [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
            'message' => 'nullable|string|max:1000',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Feature Flags
    |--------------------------------------------------------------------------
    |
    | Enable or disable specific features of the package.
    |
    */
    'features' => [
        'bulk_appointment_creation' => true,
        'appointment_categories' => true,
        'booking_messages' => true,
        'email_notifications' => true,
        'admin_dashboard' => true,
        'export_functionality' => true,
        'real_time_updates' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Localization
    |--------------------------------------------------------------------------
    |
    | Configure localization settings.
    |
    */
    'localization' => [
        'default_locale' => 'hu',
        'date_format' => 'Y. m. d.',
        'time_format' => 'H:i',
        'datetime_format' => 'Y. m. d. H:i',
    ],
];