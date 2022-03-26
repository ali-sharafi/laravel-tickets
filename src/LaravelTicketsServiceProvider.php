<?php

namespace LaravelTickets;

use Illuminate\Support\ServiceProvider;
use LaravelTickets\LaravelTickets;
use LaravelTickets\Models\Ticket;
use LaravelTickets\Models\TicketMessage;
use LaravelTickets\Models\TicketUpload;
use LaravelTickets\Observers\TicketMessageObserver;
use LaravelTickets\Observers\TicketObserver;
use LaravelTickets\Observers\TicketUploadObserver;

class LaravelTicketsServiceProvider extends ServiceProvider
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
    }


    /**
     * Register the application services.
     */
    public function register()
    {
        // Automatically apply the package configuration
        $this->mergeConfigFrom(__DIR__ . '/config/config.php', 'laravel-tickets');

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
