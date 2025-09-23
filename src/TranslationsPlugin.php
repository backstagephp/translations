<?php

namespace Backstage\Translations\Filament;

use Backstage\Translations\Filament\Http\Middleware\SwitchLanguageLocale;
use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\TranslationResource;
use Closure;
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

    protected bool | Closure $languageSwitcherDisabled = false;

    protected bool | Closure $userCanDisableLanguageSwitcher = false;

    protected bool | Closure $userCanManageTranslations = true;

    public function getId(): string
    {
        return 'translations';
    }

    public function register(Panel $panel): void
    {
        $panel->authMiddleware([SwitchLanguageLocale::class]);

        $panel->resources([
            config('backstage.translations.resources.language', LanguageResource::class),
            config('backstage.translations.resources.translation', TranslationResource::class),
        ]);

        if (! $this->isLanguageSwitcherDisabled()) {
            $panel->renderHook(
                PanelsRenderHook::GLOBAL_SEARCH_AFTER,
                fn (): string => Blade::render('@livewire(\'backstage.translations::switcher\')'),
            );
        }

        $this->configure();

        $this->macros();
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

    public function languageSwitcherDisabled(bool | Closure $disabled = true): static
    {
        $this->languageSwitcherDisabled = $disabled;

        return $this;
    }

    public function isLanguageSwitcherDisabled(): bool
    {
        return $this->evaluate($this->languageSwitcherDisabled);
    }

    public function canManageTranslations(bool | Closure $userCanManageTranslations = true): static
    {
        $this->userCanManageTranslations = $userCanManageTranslations;

        return $this;
    }

    public function userCanManageTranslations(): bool
    {
        return $this->evaluate($this->userCanManageTranslations);
    }
}
