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
        static::updatePackages();
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
        $dest = resource_path('js/components');
        shell_exec(" cp -r -a $origin $dest 2>&1 ");
    }

    /**
     * Update the given package array.
     *
     * @param  array  $packages
     * @return array
     */
    protected static function updatePackageArray(array $packages)
    {
        return [
            "element-ui" => "^2.4.1",
        ] + $packages;
    }
}
