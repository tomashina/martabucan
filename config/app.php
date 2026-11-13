<?php
return [
    'name' => env('APP_NAME', 'Marta Bućan'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => 'Europe/Zagreb',
    'locale' => 'hr',
    'fallback_locale' => 'en',
    'faker_locale' => 'hr_HR',
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'previous_keys' => [],
    'maintenance' => ['driver' => 'file'],
];
