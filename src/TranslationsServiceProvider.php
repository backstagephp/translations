<?php

namespace Backstage\Translations\Filament;

use Backstage\Translations\Filament\Base\TranslationLoader;
use Backstage\Translations\Filament\Base\Translator;
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

class TranslationsServiceProvider extends PackageServiceProvider
{
    public static string $name = 'translations';

    public static string $viewNamespace = 'backstage-translations';

    public function configurePackage(Package $package): void
    {
        $package->name(static::$name)
            ->hasInstallCommand(function (InstallCommand $command) {
                $command
                    ->publishConfigFile()
                    ->askToStarRepoOnGitHub('backstagephp/translations');
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
        $this->app->register(\Spatie\TranslationLoader\TranslationServiceProvider::class, true);

        $this->app->singleton('translation.loader', function ($app) {
            return new TranslationLoader($app['files'], $app['path.lang']);
        });

        $this->app->singleton('translator', function ($app) {
            $loader = $app['translation.loader'];

            $locale = $app['config']['app.locale'];

            $trans = new Translator($loader, $locale);
            $trans->setFallback('country.uk');

            return $trans;
        });

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

        Livewire::component('backstage-translations::switcher', \Backstage\Translations\Filament\Components\Switcher::class);
    }

    protected function getAssetPackageName(): ?string
    {
        return 'backstagephp/translations';
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
            Blade::aliasComponent('flag-country-en', 'flag_country_en'),
        ];
    }

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/blade-flags.php', 'blade-flags');
        $this->mergeConfigFrom(__DIR__ . '/../config/filament-translations.php', 'filament-translations');
    }
}
