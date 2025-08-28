<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    */

    'paths' => [
        'api/*',
        'login',
        'logout',
        'register',
        'user/confirm-password',
        'user/confirmed-password-status',
        'user/password',
        'forgot-password',
        'reset-password',
        'email/verification-notification',
        'sanctum/csrf-cookie',
    ], // These paths will get CORS headers in reponses.

    'allowed_methods' => ['*'], // Allow all HTTP methods

    'allowed_origins' => [
        // '*',
        'http://localhost:3000', // Your SPA origin
    ],

    'allowed_origins_patterns' => [],

    'allowed_headers' => ['*'], // Allow all headers

    'exposed_headers' => [
        'XSRF-TOKEN', // Expose CSRF token header to SPA
    ],

    'max_age' => 0,

    'supports_credentials' => true, // Very important for session cookies

];

