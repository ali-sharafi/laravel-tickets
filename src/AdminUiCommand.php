<?php

namespace LaravelTickets;

use Illuminate\Console\Command;
use InvalidArgumentException;

class AdminUiCommand extends Command
{
    /**
     * The console command signature.
     *
     * @var string
     */
    protected $signature = 'tickets:admin-ui
                    { type : The preset type (vue) }';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Swap the admin front-end scaffolding for the laravel tickets';

    /**
     * Execute the console command.
     *
     * @return void
     *
     * @throws \InvalidArgumentException
     */
    public function handle()
    {
        if (!in_array($this->argument('type'), ['vue'])) {
            throw new InvalidArgumentException('Invalid preset.');
        }

        $this->{$this->argument('type')}();
    }

    /**
     * Install the "vue" preset.
     *
     * @return void
     */
    protected function vue()
    {
        Presets\Vue::install();

        $this->info('Vue scaffolding installed successfully.');
        $this->comment('Please run "npm run dev" to compile your fresh scaffolding.');
    }
}
