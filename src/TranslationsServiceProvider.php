<?php

namespace Backstage\Translations\Filament;

use Backstage\Translations\Filament\Testing\TestsFilamentTranslations;
use Filament\Support\Assets\Css;
use Filament\Support\Facades\FilamentAsset;
use Filament\Support\Facades\FilamentIcon;
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

    protected function registerConfig(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/backstage/translations.php', 'backstage.translations');
    }
}
