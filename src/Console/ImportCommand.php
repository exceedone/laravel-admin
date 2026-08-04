<?php

namespace Encore\Admin\Console;

use Encore\Admin\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class ImportCommand extends Command
{
    /**
     * The console command name.
     *
     * @var string
     */
    protected $signature = 'admin:import {extension?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Import a Laravel-admin extension';

    /**
     * Execute the console command.
     *
     * @return void
     */
    public function handle()
    {
        $extension = $this->argument('extension');

        /** @phpstan-ignore-next-line */
        if (empty($extension) || !Arr::has(Admin::$extensions, $extension)) {
            $extension = $this->choice('Please choose a extension to import', array_keys(Admin::$extensions));
        }

        /** @phpstan-ignore-next-line */
        $className = Arr::get(Admin::$extensions, $extension);

        // @phpstan-ignore-next-line $class is always string at runtime
        if (!class_exists($className) || !method_exists($className, 'import')) {
            // @phpstan-ignore-next-line $className is always castable to string at runtime
            $this->error("Invalid Extension [$className]");

            return;
        }

        call_user_func([$className, 'import'], $this);

        $this->info("Extension [$className] imported");
    }
}
