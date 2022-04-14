<?php

namespace LaravelTickets;

use Illuminate\Support\ServiceProvider as BaseServiceProvider;
use LaravelTickets\LaravelTickets;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Models\TicketUpload;
use LaravelTickets\Observers\TicketMessageObserver;
use LaravelTickets\Observers\TicketObserver;
use LaravelTickets\Observers\TicketUploadObserver;

class ServiceProvider extends BaseServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        /*
         * Optional methods to load your package assets
         */
        $this->loadViewsFrom(__DIR__ . '/views', 'laravel-tickets');
        $this->loadMigrationsFrom(__DIR__ . '/migrations');
        $this->loadFactoriesFrom(__DIR__ . '/factories');

        $this->observers();

        $this->publishes([
            __DIR__ . '/config/config.php' => config_path('tickets.php'),
        ], 'laravel-tickets:config');

        // Publishing the migrations.
        $this->publishes([
            __DIR__ . '/migrations' => database_path('migrations')
        ], 'laravel-tickets:migrations');

        // Publishing the views.
        $this->publishes([
            __DIR__ . '/views' => resource_path('views/vendor/laravel-tickets'),
        ], 'laravel-tickets:views');
    }


    /**
     * Register the application services.
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                AdminUiCommand::class
            ]);
        }

        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'tickets');

        // Register the main class to use with the facade
        $this->app->singleton('laravel-tickets', function () {
            return new LaravelTickets;
        });
    }

    private function observers()
    {
        Ticket::observe(TicketObserver::class);
        TicketMessage::observe(TicketMessageObserver::class);
        TicketUpload::observe(TicketUploadObserver::class);
    }
}
