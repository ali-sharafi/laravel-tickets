<?php

namespace LaravelTickets\Presets;

class Vue extends Preset
{
    /**
     * Install the preset.
     *
     * @return void
     */
    public static function install()
    {
        static::ensureComponentDirectoryExists();
        static::updateComponent();
    }


    /**
     * Update the example component.
     *
     * @return void
     */
    public static function updateComponent()
    {
        $origin =  __DIR__ . '/vue-stubs/*';
        $dest = __DIR__ . '/components/';
        shell_exec(" cp -r -a $origin $dest 2>&1 ");
    }
}
