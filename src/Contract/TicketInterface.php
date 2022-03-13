<?php

namespace LaravelTickets\Contract;

interface TicketInterface
{
    /**
     * @var
     * 
     * The type of users that can create ticket message
     */
    const USER = 1, ADMIN = 2;
}
