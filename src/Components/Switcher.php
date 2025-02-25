<?php

namespace Backstage\Translations\Filament\Components;

use Backstage\Translations\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Filament\Notifications\Notification;
use Livewire\Component;

class Switcher extends Component
{
    public $languages;

    public $currentLanguage;

    public function render()
    {
        $this->languages = config('backstage.translations.resources.language')::getModel()::active()->get();

        $this->currentLanguage = config('backstage.translations.resources.language')::getModel()::where('code', session('language')['code'])->first() ?:
            config('backstage.translations.resources.language')::getModel()::default() ?:
            config('backstage.translations.resources.language')::getModel()::where('code', config('app.locale'))->first();

        if (! (count($this->languages) > 0)) {
            return view('backstage.translations::components.switcher-empty');
        }

        return view('backstage.translations::components.switcher');
    }

    public function switchLanguage($language)
    {
        $previousLanguage = config('backstage.translations.resources.language')::getModel()::where('code', session('language')['code'])->first();
        $newLanguage = config('backstage.translations.resources.language')::getModel()::where('code', $language->code)->first();

        if ($previousLanguage->code !== $newLanguage->code) {
            session(['locale' => $newLanguage->only('code', 'name', 'native', 'localizedLanguageName', 'localizedCountryName')]);

            Notification::make()
                ->title(__('Language changed'))
                ->body(__('The language has been changed from :previousLanguage to :newLanguage', ['previousLanguage' => $previousLanguage->localizedLanguageName, 'newLanguage' => $newLanguage->localizedLanguageName]))
                ->success()
                ->send();

            return redirect()->back();
        }
    }

    public function list()
    {
        return $this->redirect(ListLanguages::getUrl());
    }
}
