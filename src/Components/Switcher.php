<?php

namespace Vormkracht10\FilamentTranslations\Components;

use Filament\Actions\Concerns\HasForm;
use Livewire\Component;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource;
use Vormkracht10\FilamentTranslations\Resources\LanguageResource\Pages\ListLanguages;

class Switcher extends Component
{
    use HasForm;

    public array $languages;

    public string $currentLanguage;

    public string $currentLanguageIcon;

    public function render()
    {
        $this->languages = LanguageResource::getModel()::all()->pluck('label', 'locale')->toArray();

        if (session('curretLanguage')) {
            $this->currentLanguage = session('curretLanguage');
        } else {
            $this->currentLanguage = app()->getLocale();
        }

        app()->setLocale($this->currentLanguage);

        $this->currentLanguageIcon = getCountryFlag($this->currentLanguage);

        return view('filament-translations::components.switcher');
    }

    public function switchLanguage(string $lang)
    {
        session(['curretLanguage' => $lang]);
    }

    public function configure()
    {
        return $this->redirect(ListLanguages::getUrl());
    }
}
