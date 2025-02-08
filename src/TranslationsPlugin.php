<?php

namespace Backstage\Translations;

use Backstage\Translations\Http\Middleware\SwitchLanguageLocale;
use Filament\Contracts\Plugin;
use Filament\Forms\Components\Select;
use Filament\Panel;
use Filament\Support\Concerns\EvaluatesClosures;
use Filament\Tables\Columns\TextInputColumn;
use Filament\View\PanelsRenderHook;
use Illuminate\Support\Facades\Blade;

class TranslationsPlugin implements Plugin
{
    use EvaluatesClosures;

    public function getId(): string
    {
        return 'translations';
    }

    public function register(Panel $panel): void
    {
        $panel->resources([
            Resources\LanguageResource::class,
            Resources\TranslationResource::class,
        ]);

        $panel->renderHook(
            PanelsRenderHook::GLOBAL_SEARCH_AFTER,
            fn (): string => Blade::render('@livewire(\'backstage-translations::switcher\')'),
        );

        $this->configure();

        $this->macros();

        $panel->middleware([SwitchLanguageLocale::class]);
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

    protected function macros()
    {
        TextInputColumn::macro('translated', function () {
            return $this->afterStateUpdated(function ($record) {
                $record->update(['translated_at' => now()]);
            });
        });
    }
}
