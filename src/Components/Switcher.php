<?php

namespace Backstage\Translations\Filament\Components;

use Backstage\Translations\Filament\Resources\LanguageResource;
use Backstage\Translations\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Filament\Actions\Concerns\HasForm;
use Filament\Notifications\Notification;
use Livewire\Component;

class Switcher extends Component
{
    use HasForm;

    public array $languages;

    public string $currentLanguage;

    public string $currentLanguageIcon;

    public function render()
    {
        $this->languages = LanguageResource::getModel()::all()->pluck('name', 'code')->toArray();

        if (isset(session()->get('languages')['code'])) {
            $this->currentLanguage = session()->get('languages')['code'];
        } else {
            $this->currentLanguage = app()->getLocale();
        }

        $this->currentLanguageIcon = getCountryFlag($this->currentLanguage);

        return view('backstage-translations::components.switcher');
    }

    public function switchLanguage(string $lang)
    {
        $oldLang = session()->get('languages')['code'];

        if (array_key_exists($oldLang, $this->languages)) {
            if ($this->languages[$oldLang] === $this->languages[$lang]) {
                Notification::make()
                    ->title(__('Language not changed'))
                    ->body(__('The language has not been changed because the selected language is the same as the current language'))
                    ->danger()
                    ->send();

                return;
            }
        }

        $lang = LanguageResource::getModel()::where('code', $lang)->first();

        session()->put('languages.code', $lang->code);
        session()->put('languages.language_code', $lang->languageCode);

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
