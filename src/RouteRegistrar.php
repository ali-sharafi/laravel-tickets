<?php

namespace LaravelTickets;

use Illuminate\Contracts\Routing\Registrar as Router;

class RouteRegistrar
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    public function all()
    {
        $this->router->get('/', 'TicketController@index')->name('tickets.index');
        $this->router->post('/', 'TicketController@store')->name('tickets.store')->middleware('throttle:3,120');
        $this->router->get('/create', 'TicketController@create')->name('tickets.create');

        $this->router->group(['prefix' => '{ticket}'], function () {
            $this->router->get('/', 'TicketController@show')->name('tickets.show');
            $this->router->post('/', 'TicketController@close')->name('tickets.close');
            $this->router->post('/message', 'TicketController@message')->name('tickets.message')->middleware('throttle:3,120');

            $this->router->group(['prefix' => '{ticketUpload}'], function () {
                $this->router->get('/download', 'TicketController@download')->name('tickets.download');
            });
        });
    }
}
