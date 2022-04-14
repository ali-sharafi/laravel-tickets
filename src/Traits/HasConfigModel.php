<?php


namespace LaravelTickets\Traits;

/**
 * Trait HasConfigModel
 *
 * Is used internal for using configuration elements
 *
 * @package LaravelTickets\Traits
 */
trait HasConfigModel
{

    public function getKeyType()
    {
        return 'string';
    }

    public function isIncrementing()
    {
        return config('tickets.model.incrementing');
    }
}
