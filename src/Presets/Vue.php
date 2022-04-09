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
    protected static function updateComponent()
    {
        copy(
            __DIR__ . '/vue-stubs/ExampleComponent.vue',
            resource_path('js/components/ExampleComponent.vue')
        );
    }
}
