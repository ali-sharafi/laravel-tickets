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

    /**
     * @var
     * 
     * State of ticket
     */
    const STATE_OPEN = 'OPEN', STATE_ANSWERED = 'ANSWERED', STATE_CLOSED = 'CLOSED';
}
