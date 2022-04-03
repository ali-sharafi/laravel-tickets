<?php

namespace LaravelTickets;

use Illuminate\Support\Facades\Route;

class LaravelTickets
{
    public static function routes(array $options = [], $callback = null)
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'tickets',
            'namespace' => '\LaravelTickets\Http\Controllers',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new RouteRegistrar($router));
        });
    }

    public static function api(array $options = [], $callback = null)
    {
        $callback = $callback ?: function ($router) {
            $router->all();
        };

        $defaultOptions = [
            'prefix' => 'tickets',
            'middleware' => 'bindings',
            'namespace' => '\LaravelTickets\Http\Controllers\Admin',
        ];

        $options = array_merge($defaultOptions, $options);

        Route::group($options, function ($router) use ($callback) {
            $callback(new ApiRouteRegistrar($router));
        });
    }
}
