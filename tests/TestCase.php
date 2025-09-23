<?php

namespace Backstage\Translations\Filament\Tests;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Filament\FilamentServiceProvider;
use Livewire\LivewireServiceProvider;
use Filament\Forms\FormsServiceProvider;
use Filament\Tables\TablesServiceProvider;
use Filament\Support\SupportServiceProvider;
use Filament\Widgets\WidgetsServiceProvider;
use Orchestra\Testbench\TestCase as Orchestra;
use Filament\Infolists\InfolistsServiceProvider;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Filament\Notifications\NotificationsServiceProvider;
use Backstage\Translations\Filament\TranslationServiceProvider;
use Backstage\Translations\Filament\Tests\Filament\AdminPanelProvider;
use RyanChandler\BladeCaptureDirective\BladeCaptureDirectiveServiceProvider;

class TestCase extends Orchestra
{
    use RefreshDatabase;
    
    protected function setUp(): void
    {
        parent::setUp();

        $this->defineDatabaseMigrations();
    }

    protected function getPackageProviders($app): array
    {
        return [
            BladeCaptureDirectiveServiceProvider::class,
            FilamentServiceProvider::class,
            FormsServiceProvider::class,
            InfolistsServiceProvider::class,
            LivewireServiceProvider::class,
            NotificationsServiceProvider::class,
            SupportServiceProvider::class,
            TablesServiceProvider::class,
            WidgetsServiceProvider::class,
            TranslationServiceProvider::class,
        ];
    }

    public function getEnvironmentSetUp($app): void
    {
        config()->set('database.default', 'testing');

        config()->set('database.connections.testing', [
            'driver' => 'mysql',
            'host' => 'localhost',
            'port' => 3306,
            'username' => 'root',
            'password' => '',
            'database' => 'translations_testing',
            'prefix' => '',
        ]);

        $pdo = new \PDO('mysql:host=localhost;port=3306;dbname=translations_testing', 'root', '');
            $pdo->exec('CREATE DATABASE IF NOT EXISTS translations_testing');

        // Enure DB exists
        $app->register(AdminPanelProvider::class);
    }

    protected function defineDatabaseMigrations(): void
    {
        $migrations = File::glob(__DIR__ . '/../vendor/backstage/laravel-translations/database/migrations/*.stub');

        // Copy the file to a tmp dir and remove the .php.stub with .php
        foreach ($migrations as $migration) {
            File::copy($migration, $this->getTempDir() . '/' . str_replace('.php.stub', '.php', basename($migration)));

            $this->loadMigrationsFrom($this->getTempDir() . '/' . str_replace('.php.stub', '.php', basename($migration)));
        }

        $testBench = File::glob(__DIR__ . '/../vendor/orchestra/testbench-core/laravel/migrations/*.php');

        foreach ($testBench as $migration) {
            $this->loadMigrationsFrom($migration);
        }
    }

    protected function getTempDir(): string
    {
        return sys_get_temp_dir();
    }

    protected function getPackageAliases($app): array
    {
        return [
            'Filament' => \Filament\Facades\Filament::class,
        ];
    }
}
