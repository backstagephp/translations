<?php

use Backstage\Translations\Filament\Resources\TranslationResource\Pages\ListTranslations;
use Backstage\Translations\Laravel\Models\Language;
use Backstage\Translations\Laravel\Models\Translation;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can edit translation text', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Old text',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->callTableAction('edit', $translation, [
            'text' => 'New text',
        ])
        ->assertNotified();

    assertDatabaseHas('translations', [
        'id' => $translation->id,
        'text' => 'New text',
    ]);
});

it('can edit translation with other translations', function () {
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

    $translation1 = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Welcome',
        'namespace' => '*',
    ]);

    $translation2 = Translation::create([
        'code' => 'es',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Bienvenido',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->callTableAction('edit', $translation1, [
            'text' => 'Updated Welcome',
            'other_translations' => [
                'es' => 'Updated Bienvenido',
            ],
        ])
        ->assertNotified();

    assertDatabaseHas('translations', [
        'id' => $translation1->id,
        'text' => 'Updated Welcome',
    ]);

    assertDatabaseHas('translations', [
        'id' => $translation2->id,
        'text' => 'Updated Bienvenido',
    ]);
});

it('validates required text field on edit', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Welcome',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->callTableAction('edit', $translation, [
            'text' => '',
        ])
        ->assertHasFormErrors(['text' => 'required']);
});

it('can edit translation with multiline text', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Old text',
        'namespace' => '*',
    ]);
    $multilineText = "Updated\nMultiline\nText";

    Livewire::test(ListTranslations::class)
        ->callTableAction('edit', $translation, [
            'text' => $multilineText,
        ])
        ->assertNotified();

    assertDatabaseHas('translations', [
        'id' => $translation->id,
        'text' => $multilineText,
    ]);
});

it('can edit translation with special characters', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Old text',
        'namespace' => '*',
    ]);
    $specialText = 'Updated "text" & symbols! @#$%^&*()';

    Livewire::test(ListTranslations::class)
        ->callTableAction('edit', $translation, [
            'text' => $specialText,
        ])
        ->assertNotified();

    assertDatabaseHas('translations', [
        'id' => $translation->id,
        'text' => $specialText,
    ]);
});
