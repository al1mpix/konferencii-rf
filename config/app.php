<?php

use Illuminate\Support\Facades\Facade;
use Illuminate\Support\ServiceProvider;

return [
    'name' => env('APP_NAME', 'Конференции.РФ'),
    'env' => env('APP_ENV', 'production'),
    'debug' => (bool) env('APP_DEBUG', false),
    'url' => env('APP_URL', 'http://localhost'),
    'timezone' => env('APP_TIMEZONE', 'Europe/Moscow'),
    'locale' => env('APP_LOCALE', 'ru'),
    'fallback_locale' => env('APP_FALLBACK_LOCALE', 'ru'),
    'faker_locale' => env('APP_FAKER_LOCALE', 'ru_RU'),
    'key' => env('APP_KEY'),
    'cipher' => 'AES-256-CBC',
    'maintenance' => [
        'driver' => env('APP_MAINTENANCE_DRIVER', 'file'),
        'store' => env('APP_MAINTENANCE_STORE', 'database'),
    ],
    'providers' => ServiceProvider::defaultProviders()->merge([
        App\Providers\AppServiceProvider::class,
        App\Providers\AuthServiceProvider::class,
        App\Providers\EventServiceProvider::class,
        App\Providers\RouteServiceProvider::class,
    ])->toArray(),
    'aliases' => Facade::defaultAliases()->toArray(),
];
