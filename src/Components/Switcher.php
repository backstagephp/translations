<?php

namespace Backstage\Translations\Components;

use Filament\Actions\Concerns\HasForm;
use Livewire\Component;
use Backstage\Translations\Resources\LanguageResource;
use Backstage\Translations\Resources\LanguageResource\Pages\ListLanguages;

class Switcher extends Component
{
    use HasForm;

    public array $languages;

    public string $currentLanguage;

    public string $currentLanguageIcon;

    public function render()
    {
        $this->languages = LanguageResource::getModel()::all()->pluck('label', 'locale')->toArray();

        if (count($this->languages) == 0) {
            LanguageResource::getModel()::create([
                'locale' => 'en',
                'label' => 'English',
            ]);
        }

        if (session('curretLanguage')) {
            $this->currentLanguage = session('curretLanguage');
        } else {
            $this->currentLanguage = app()->getLocale();
        }

        app()->setLocale($this->currentLanguage);

        $this->currentLanguageIcon = getCountryFlag($this->currentLanguage);

        return view('backstage-translations::components.switcher');
    }

    public function switchLanguage(string $lang)
    {
        session()->put('locale', $lang);

        cookie()->queue(cookie()->forever('filament_language_switch_locale', $lang));

        return redirect(request()->header('Referer'));
    }

    public function list()
    {
        return $this->redirect(ListLanguages::getUrl());
    }
}
