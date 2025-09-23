<?php

use Backstage\Translations\Filament\Resources\LanguageResource\Pages\CreateLanguage;
use Backstage\Translations\Laravel\Models\Language;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can create a language', function () {
    $languageData = [
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ];

    Livewire::test(CreateLanguage::class)
        ->fillForm($languageData)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('languages', $languageData);
});

it('can create a language with country code', function () {
    $languageData = [
        'code' => 'en-US',
        'name' => 'English (United States)',
        'native' => 'English (United States)',
        'active' => true,
        'default' => false,
    ];

    Livewire::test(CreateLanguage::class)
        ->fillForm($languageData)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('languages', $languageData);
});

it('validates required fields', function () {
    Livewire::test(CreateLanguage::class)
        ->fillForm([])
        ->call('create')
        ->assertHasFormErrors(['code' => 'required', 'name' => 'required', 'native' => 'required']);
});

it('validates unique code', function () {
    Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(CreateLanguage::class)
        ->fillForm([
            'code' => 'en',
            'name' => 'English',
            'native' => 'English',
            'active' => true,
            'default' => false,
        ])
        ->call('create')
        ->assertHasFormErrors(['code' => 'unique']);
});

it('auto-fills name and native when code is entered', function () {
    Livewire::test(CreateLanguage::class)
        ->fillForm(['code' => 'es'])
        ->assertFormSet([
            'name' => 'Spanish',
            'native' => 'español',
        ]);
});

it('can set language as default', function () {
    $languageData = [
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => true,
    ];

    Livewire::test(CreateLanguage::class)
        ->fillForm($languageData)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('languages', $languageData);
});
