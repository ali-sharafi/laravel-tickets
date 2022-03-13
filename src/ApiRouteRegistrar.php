<?php

namespace LaravelTickets;

use Illuminate\Contracts\Routing\Registrar as Router;

class ApiRouteRegistrar
{
    protected $router;

    public function __construct(Router $router)
    {
        $this->router = $router;
    }
    public function all()
    {
        $this->router->resource('categories', 'CategoryController');
        $this->router->resource('labels', 'LabelController');

        $this->router->get('/', 'TicketController@index');

        $this->router->delete('/comments/{comment}', 'TicketController@deleteComment');

        $this->router->group(['prefix' => '{ticket}'], function () {
            $this->router->get('/', 'TicketController@show');
            $this->router->post('/close', 'TicketController@close');
            $this->router->post('/reopen', 'TicketController@open');
            $this->router->post('/reassign', 'TicketController@reassign');
            $this->router->post('/message', 'TicketController@message');
        });

    }
}
