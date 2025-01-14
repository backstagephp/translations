<?php

namespace Vormkracht10\FilamentTranslations;

use Filament\Panel;
use Filament\Contracts\Plugin;
use Filament\View\PanelsRenderHook;
use Filament\Forms\Components\Select;
use Illuminate\Support\Facades\Blade;

class FilamentTranslationsPlugin implements Plugin
{
    public function getId(): string
    {
        return 'filament-translations';
    }

    public function register(Panel $panel): void
    {
        $panel->discoverResources(in: base_path('vendor/vormkracht10/filament-translations/src/Resources'), for: 'Vormkracht10\\FilamentTranslations\\Resources');

        $panel->renderHook(        
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn (): string => Blade::render('@livewire(\'filament-translations::switcher\')'),
        );

        $this->configure();
    }

    public function boot(Panel $panel): void {}

    public static function make(): static
    {
        return app(static::class);
    }

    public static function get(): static
    {
        /** @var static $plugin */
        $plugin = filament(app(static::class)->getId());

        return $plugin;
    }

    protected function configure()
    {
        Select::configureUsing(function (Select $select) {
            $select->native(false);
        });
    }
}
