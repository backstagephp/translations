<?php

namespace Backstage\Translations\Components;

use Backstage\Translations\Resources\LanguageResource;
use Backstage\Translations\Resources\LanguageResource\Pages\ListLanguages;
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

        if (count($this->languages) == 0) {
            LanguageResource::getModel()::create([
                'code' => 'en',
                'name' => 'English',
                'active' => true,
                'default' => true,
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
        $oldLang = request()->get('code') ?:
            session()->get('code') ?:
            request()->cookie('filament_language_code') ?:
            LanguageResource::getModel()::where('default', true)->first()?->languageCode ?:
            request()->getPreferredLanguage() ?:
            app()->getLocale() ?: 'en';

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

        session()->put('code', $lang);

        cookie()->queue(cookie()->forever('filament_language_switch_locale', $lang));

        Notification::make()
            ->title(__('Language changed'))
            ->body(__('The language has been changed from :oldLanguage to :language', ['oldLanguage' => $this->languages[$oldLang] ?? '', 'language' => $this->languages[$lang]], $lang))
            ->success()
            ->send();

        return redirect(request()->header('Referer'));
    }

    public function list()
    {
        return $this->redirect(ListLanguages::getUrl());
    }
}
