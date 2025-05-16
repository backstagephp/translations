<?php

namespace Backstage\Translations\Filament;

use Backstage\Translations\Filament\Testing\TestsFilamentTranslations;
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

class TranslationServiceProvider extends PackageServiceProvider
{
    public static string $name = 'translations';

    public static string $viewNamespace = 'backstage.translations';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('backstagephp/translations');
            });

        $this->mergeConfigFrom(
            __DIR__ . '/../config/backstage/translations.php',
            'backstage.translations',
        );

        if (file_exists($package->basePath('/../resources/views'))) {
            $package->hasViews(static::$viewNamespace);
        }

    }

    public function packageBooted(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/backstage/translations.php' => $this->app->configPath('backstage/translations.php'),
            ], 'backstage-translations-config');
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

        Translations::registerMacro();

        Testable::mixin(new TestsFilamentTranslations);

        require_once __DIR__ . '/helpers.php';

        Livewire::component('backstage.translations::switcher', \Backstage\Translations\Filament\Components\Switcher::class);
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

    protected function getAssetPackageName(): ?string
    {
        return 'backstage/translations';
    }

    protected function getAssets(): array
    {
        return [
            Css::make('backstage-translations-styles', __DIR__ . '/../resources/dist/backstage-translations.css'),
        ];
    }

    protected function getIcons(): array
    {
        return [
            Blade::aliasComponent('flag-country-us', 'flag_country_en'),
            Blade::aliasComponent('flag-country-en', 'flag_country_en'),
        ];
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade-flags.php', 'blade-flags');
        $this->mergeConfigFrom(__DIR__ . '/../config/backstage/translations.php', 'backstage.translations');
    }
}
