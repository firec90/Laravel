<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Cross-Origin Resource Sharing (CORS) Configuration
    |--------------------------------------------------------------------------
    |
    | Kamu bisa menentukan domain mana saja yang diizinkan untuk mengakses API.
    | Ini penting agar backend Laravel hanya bisa diakses oleh frontend tertentu.
    |
    */
    'paths' => ['api/*', 'sanctum/csrf-cookie'],
    'allowed_methods' => ['*'],
    // Ganti URL berikut dengan alamat frontend VueJS kamu
    'allowed_origins' => ['http://localhost:5173'],
    'allowed_origins_patterns' => [],
    'allowed_headers' => ['*'],
    'exposed_headers' => [],
    'max_age' => 0,
    'supports_credentials' => true,
];