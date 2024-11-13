<?php

namespace Jaldi\SetupPackage\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class SetupCommand extends Command
{
    protected $signature = 'app:setup';
    protected $description = 'Sets up the environment and database for the application.';

    public function handle()
    {
        $this->info('Starting application setup...');

        $this->checkAndCreateEnvFile();
        $this->checkAndHandleDatabase();

        if (config('setup.commands.key_generate', true)) {
            $this->info('Running key generation...');
            Artisan::call('key:generate');
        }

        $this->info('Setup complete.');
    }

    protected function checkAndCreateEnvFile()
    {
        if (!File::exists(base_path('.env'))) {
            $this->info('.env file not found. Creating a new .env file from .env.example...');
            File::copy(base_path('.env.example'), base_path('.env'));
            $this->info('.env file created successfully.');
        } else {
            $this->info('.env file already exists.');
        }
    }

    protected function checkAndHandleDatabase()
    {
        $database = env('DB_DATABASE');

        try {
            DB::connection()->getPdo();
            $this->info("Database '{$database}' is accessible.");

            if (config('setup.drop_database_if_exists') && $this->confirm("Database '{$database}' already exists. Would you like to drop and recreate it?")) {
                $this->dropAndRecreateDatabase($database);
            } else {
                $this->warn("Continuing with the existing database...");
                $this->runMigrationsAndSeed();
            }
        } catch (\Exception $e) {
            $this->error("Database '{$database}' not found.");

            if ($this->confirm("Would you like to create the database '{$database}' now?")) {
                $this->createDatabase($database);
                $this->runMigrationsAndSeed();
            } else {
                $this->warn("Database setup skipped. Make sure to create it before running the application.");
            }
        }
    }
}
