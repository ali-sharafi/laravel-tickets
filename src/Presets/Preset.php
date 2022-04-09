<?php

namespace LaravelTickets\Presets;

use Illuminate\Filesystem\Filesystem;

class Preset
{
    /**
     * Ensure the component directories we need exist.
     *
     * @return void
     */
    protected static function ensureComponentDirectoryExists()
    {
        $filesystem = new Filesystem;

        if (!$filesystem->isDirectory($directory = resource_path('js/components'))) {
            $filesystem->makeDirectory($directory, 0755, true);
        }
    }
}
