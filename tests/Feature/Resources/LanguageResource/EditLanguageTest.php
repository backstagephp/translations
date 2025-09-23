<?php

use Backstage\Translations\Filament\Resources\LanguageResource\Pages\EditLanguage;
use Backstage\Translations\Laravel\Models\Language;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can load edit page', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->assertOk()
        ->assertFormSet([
            'code' => $language->code,
            'name' => $language->name,
            'native' => $language->native,
            'active' => $language->active,
            'default' => $language->default,
        ]);
});

it('can update a language', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $newData = [
        'name' => 'Updated English',
        'native' => 'Updated English',
        'active' => false,
        'default' => false,
    ];

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->fillForm($newData)
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas('languages', [
        'code' => $language->code,
        ...$newData,
    ]);
});

it('validates required fields on update', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->fillForm([
            'name' => '',
            'native' => '',
        ])
        ->call('save')
        ->assertHasFormErrors(['name' => 'required', 'native' => 'required']);
});

it('validates unique code on update', function () {
    $language1 = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $language2 = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language2->code])
        ->fillForm(['code' => 'en'])
        ->call('save')
        ->assertHasFormErrors(['code' => 'unique']);
});

it('allows keeping same code on update', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->fillForm([
            'name' => 'Updated English',
            'native' => 'Updated English',
        ])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();
});

it('can toggle active status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->fillForm(['active' => false])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas('languages', [
        'code' => $language->code,
        'active' => false,
    ]);
});

it('can toggle default status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'default' => false,
        'active' => true,
    ]);

    Livewire::test(EditLanguage::class, ['record' => $language->code])
        ->fillForm(['default' => true])
        ->call('save')
        ->assertHasNoFormErrors()
        ->assertNotified();

    assertDatabaseHas('languages', [
        'code' => $language->code,
        'default' => true,
    ]);
});
