<?php

namespace Vormkracht10\FilamentTranslations;

use BladeUI\Icons\Factory;
use Filament\Support\Assets\AlpineComponent;
use Filament\Support\Assets\Asset;
use Filament\Support\Assets\Css;
use Filament\Support\Assets\Js;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Container\Container;
use Illuminate\Filesystem\Filesystem;
use Illuminate\Support\Facades\Blade;
use Livewire\Features\SupportTesting\Testable;
use Livewire\Livewire;
use Spatie\LaravelPackageTools\Commands\InstallCommand;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Vormkracht10\FilamentTranslations\Testing\TestsFilamentTranslations;

class FilamentTranslationsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'filament-translations';

    public static string $viewNamespace = 'filament-translations';

    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package->name(static::$name)
            ->hasCommands($this->getCommands())
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('vormkracht10/filament-translations');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

        // if (file_exists($package->basePath('/../resources/lang'))) {
        //     $package->hasTranslations();
        // }

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }
    }

    public function packageRegistered()
    {
        $this->callAfterResolving(Factory::class, function (Factory $factory, Container $container) {
            $config = $container->make('config')->get('blade-flags', [
                'prefix' => 'flag',
            ]);

            $factory->add('blade-flags', array_merge(['path' => __DIR__ . '/../resources/svg'], $config));
        });
    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../resources/svg' => public_path('vendor/blade-flags'),
            ], 'blade-flags');

            $this->publishes([
                __DIR__ . '/../config/blade-flags.php' => $this->app->configPath('blade-flags.php'),
            ], 'blade-flags-config');
        }

        // Asset Registration
        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            $this->getScriptData(),
            $this->getAssetPackageName()
        );

        // Icon Registration
        FilamentIcon::register($this->getIcons());

        // Testing
        Testable::mixin(new TestsFilamentTranslations);

        require_once __DIR__ . '/helpers.php';

        Livewire::component('filament-translations::switcher', \Vormkracht10\FilamentTranslations\Components\Switcher::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'vormkracht10/filament-translations';
    }

    /**
     * @return array<Asset>
     */
    protected function getAssets(): array
    {
        return [
            // AlpineComponent::make('filament-translations', __DIR__ . '/../resources/dist/components/filament-translations.js'),
            Css::make('filament-progress-column-styles', __DIR__ . '/../vendor/ryangjchandler/filament-progress-column/resources/dist/progress.css'),
            // Js::make('filament-translations-scripts', __DIR__ . '/../resources/dist/filament-translations.js'),
        ];
    }

    /**
     * @return array<class-string>
     */
    protected function getCommands(): array
    {
        return [
        ];
    }

    /**
     * @return array<string>
     */
    protected function getIcons(): array
    {
        return [
            Blade::aliasComponent('flag-country-us', 'flag_country_en'),
            Blade::aliasComponent('flag-country-en', 'flag_country_en')];
    }

    /**
     * @return array<string>
     */
    protected function getRoutes(): array
    {
        return [];
    }

    /**
     * @return array<string, mixed>
     */
    protected function getScriptData(): array
    {
        return [];
    }

    /**
     * @return array<string>
     */
    protected function getMigrations(): array
    {
        return [
            'create_filament-translations_table',
            'create_filament-languages',
        ];
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade-flags.php', 'blade-flags');
    }
}
