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

    public function render()
    {
        $this->languages = LanguageResource::getModel()::active()->get()->pluck('native', 'code')->toArray();

        $this->currentLanguage = session('locale') ?: LanguageResource::getModel()::default()?->languageCode ?: config('app.locale');

        $this->switchLanguage($this->currentLanguage);

        if (! (count($this->languages) > 0)) {
            return view('backstage.translations::components.switcher-empty');
        }

        return view('backstage.translations::components.switcher');
    }

    public function switchLanguage(string $code)
    {
        $oldLang = session('locale');

        $lang = LanguageResource::getModel()::where('code', $code)->first();

        session(['locale' => $lang->code]);

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
