<?php

namespace Vormkracht10\FilamentTranslations;

use BladeUI\Icons\Factory;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
use Illuminate\Contracts\Container\Container;
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
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('vormkracht10/filament-translations');
            });

        $configFileName = $package->shortName();

        if (file_exists($package->basePath("/../config/{$configFileName}.php"))) {
            $package->hasConfigFile();
        }

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

        FilamentAsset::register(
            $this->getAssets(),
            $this->getAssetPackageName()
        );

        FilamentAsset::registerScriptData(
            [],
            $this->getAssetPackageName()
        );

        FilamentIcon::register($this->getIcons());

        Testable::mixin(new TestsFilamentTranslations);

        require_once __DIR__ . '/helpers.php';

        Livewire::component('filament-translations::switcher', \Vormkracht10\FilamentTranslations\Components\Switcher::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'vormkracht10/filament-translations';
    }

    protected function getAssets(): array
    {
        return [
            Css::make('filament-progress-column-styles', __DIR__ . '/../resources/dist/filament-progress-column.css'),
        ];
    }

    protected function getIcons(): array
    {
        return [
            Blade::aliasComponent('flag-country-us', 'flag_country_en'),
            Blade::aliasComponent('flag-country-en', 'flag_country_en')];
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade-flags.php', 'blade-flags');
    }
}
