<?php

use Backstage\Translations\Filament\Resources\LanguageResource\Pages\ListLanguages;
use Backstage\Translations\Laravel\Models\Language;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can list languages', function () {
    $languages = [
        Language::create([
            'code' => 'en',
            'name' => 'English',
            'native' => 'English',
            'active' => true,
            'default' => true,
        ]),
        Language::create([
            'code' => 'es',
            'name' => 'Spanish',
            'native' => 'español',
            'active' => true,
            'default' => false,
        ]),
        Language::create([
            'code' => 'fr',
            'name' => 'French',
            'native' => 'français',
            'active' => false,
            'default' => false,
        ]),
    ];

    Livewire::test(ListLanguages::class)
        ->assertOk()
        ->assertCanSeeTableRecords($languages);
});

it('can search languages by name', function () {
    $english = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $spanish = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->searchTable('English')
        ->assertCanSeeTableRecords([$english])
        ->assertCanNotSeeTableRecords([$spanish]);
});

it('can search languages by code', function () {
    $english = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $spanish = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->searchTable('en')
        ->assertCanSeeTableRecords([$english])
        ->assertCanNotSeeTableRecords([$spanish]);
});

it('can sort languages by name', function () {
    $spanish = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);
    $english = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->sortTable('name')
        ->assertCanSeeTableRecordsInOrder([$english, $spanish]);
});

it('can sort languages by code', function () {
    $spanish = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);
    $english = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->sortTable('code')
        ->assertCanSeeTableRecordsInOrder([$english, $spanish]);
});

it('can toggle language active status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => false,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->callTableAction('toggle', $language)
        ->assertNotified();

    assertDatabaseHas('languages', [
        'code' => $language->code,
        'active' => true,
    ]);
});

it('can toggle language default status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'default' => false,
        'active' => true,
    ]);

    Livewire::test(ListLanguages::class)
        ->callTableAction('toggle_default', $language)
        ->assertNotified();

    assertDatabaseHas('languages', [
        'code' => $language->code,
        'default' => true,
    ]);
});

it('cannot set inactive language as default', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => false,
        'default' => false,
    ]);

    Livewire::test(ListLanguages::class)
        ->callTableAction('toggle_default', $language)
        ->assertNotified('Language not active');
});
