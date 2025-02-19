<?php

namespace Backstage\Translations\Filament\Components;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Filament\Notifications\Notification;
use Livewire\Component;

class Switcher extends Component
{
    public array $languages;

    public string $currentLanguage;

    public string $currentLanguageIcon;

    public function render()
    {
        $this->languages = LanguageResource::getModel()::active()->get()->pluck('native', 'code')->toArray();

        if (isset(session()->get('languages')['code'])) {
            $this->currentLanguage = session()->get('languages')['code'];
        } else {
            $this->currentLanguage = app()->getLocale();
        }

        if (! LanguageResource::getModel()::active()->where('code', $this->currentLanguage)->exists() && LanguageResource::getModel()::active()->exists()) {
            $this->currentLanguage = LanguageResource::getModel()::active()->first()->code;

            $this->switchLanguage($this->currentLanguage);
        }

        $this->currentLanguageIcon = getCountryFlag($this->currentLanguage);

        if (! (count($this->languages) > 0)) {
            return view('backstage-translations::components.switcher-empty');
        }

        return view('backstage-translations::components.switcher');
    }

    public function switchLanguage(string $lang)
    {
        $oldLang = session()->get('languages')['code'] ?? $lang;

        $lang = LanguageResource::getModel()::where('code', $lang)->first();

        session()->put('languages.code', $lang->code);
        session()->put('languages.language_code', $lang->languageCode);

        session()->put('locale', $lang->languageCode);

        cookie()->queue(cookie()->forever('filament_language_switch_locale', $lang->languageCode));

        Notification::make()
            ->title(__('Language changed'))
            ->body(__('The language has been changed from :oldLanguage to :language', ['oldLanguage' => array_key_exists($oldLang, $this->languages) ? $this->languages[$oldLang] : 'UNKNOWN', 'language' => $lang->name], $lang->languageCode))
            ->success()
            ->send();

        return redirect(request()->header('Referer'));
    }

    public function list()
    {
        return $this->redirect(ListLanguages::getUrl());
    }
}
