<?php

namespace Legodion\Zephyr\Commands;

use Illuminate\Console\Command;
use Illuminate\Filesystem\Filesystem;

class InstallCommand extends Command
{
    protected $signature = 'zephyr:install {--force}';

    public function handle()
    {
        if (!$this->confirmOverwrite()) {
            return 1;
        }

        (new Filesystem)->copyDirectory(__DIR__ . '/../../stubs', base_path());

        $this->call('lucid:model', [
            'name' => 'User',
            '--force' => true,
        ]);

        $this->call('lucid:migrate');

        exec('npm install && npm run dev');

        $this->line('<info>Zephyr installed:</info> ' . url('/'));

        return 0;
    }

    protected function confirmOverwrite()
    {
        if (!file_exists(app_path('Http/Livewire/Auth/Login.php')) || $this->option('force')) {
            return true;
        }

        return $this->confirm('Zephyr already installed, overwrite files?');
    }
}
