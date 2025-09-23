<?php

use Backstage\Translations\Filament\Resources\TranslationResource\Pages\ListTranslations;
use Backstage\Translations\Laravel\Models\Language;
use Backstage\Translations\Laravel\Models\Translation;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can list translations', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translations = [
        Translation::create([
            'code' => 'en',
            'group' => 'auth',
            'key' => 'login',
            'text' => 'Login',
            'namespace' => '*',
        ]),
        Translation::create([
            'code' => 'en',
            'group' => 'auth',
            'key' => 'register',
            'text' => 'Register',
            'namespace' => '*',
        ]),
        Translation::create([
            'code' => 'en',
            'group' => 'common',
            'key' => 'welcome',
            'text' => 'Welcome',
            'namespace' => '*',
        ]),
    ];

    Livewire::test(ListTranslations::class)
        ->assertOk()
        ->assertCanSeeTableRecords($translations);
});

it('can search translations by key', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation1 = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'auth.login',
        'text' => 'Login',
        'namespace' => '*',
    ]);
    $translation2 = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'auth.register',
        'text' => 'Register',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->searchTable('login')
        ->assertCanSeeTableRecords([$translation1])
        ->assertCanNotSeeTableRecords([$translation2]);
});

it('can search translations by text', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation1 = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'welcome',
        'text' => 'Welcome to our app',
        'namespace' => '*',
    ]);
    $translation2 = Translation::create([
        'code' => 'en',
        'group' => 'common',
        'key' => 'goodbye',
        'text' => 'Goodbye',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->searchTable('Welcome')
        ->assertCanSeeTableRecords([$translation1])
        ->assertCanNotSeeTableRecords([$translation2]);
});

it('can filter translations by language', function () {
    $languageEn = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $languageEs = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);

    $translation1 = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Login',
        'namespace' => '*',
    ]);
    $translation2 = Translation::create([
        'code' => 'es',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Iniciar sesión',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->filterTable('language', ['code' => ['en']])
        ->assertCanSeeTableRecords([$translation1])
        ->assertCanNotSeeTableRecords([$translation2]);
});

it('can filter translations by translated status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translated = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Login',
        'namespace' => '*',
        'translated_at' => now(),
    ]);
    $notTranslated = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'register',
        'text' => 'Register',
        'namespace' => '*',
        'translated_at' => null,
    ]);

    Livewire::test(ListTranslations::class)
        ->filterTable('translated_at', ['translated_at' => 'translated'])
        ->assertCanSeeTableRecords([$translated])
        ->assertCanNotSeeTableRecords([$notTranslated]);
});

it('can filter translations by not translated status', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translated = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Login',
        'namespace' => '*',
        'translated_at' => now(),
    ]);
    $notTranslated = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'register',
        'text' => 'Register',
        'namespace' => '*',
        'translated_at' => null,
    ]);

    Livewire::test(ListTranslations::class)
        ->filterTable('translated_at', ['translated_at' => 'not_translated'])
        ->assertCanSeeTableRecords([$notTranslated])
        ->assertCanNotSeeTableRecords([$translated]);
});

it('can sort translations by key', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translation1 = Translation::create([
        'code' => 'en',
        'group' => 'animals',
        'key' => 'zebra',
        'text' => 'Zebra',
        'namespace' => '*',
    ]);
    $translation2 = Translation::create([
        'code' => 'en',
        'group' => 'fruits',
        'key' => 'apple',
        'text' => 'Apple',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->sortTable('key')
        ->assertCanSeeTableRecordsInOrder([$translation2, $translation1]);
});

it('can sort translations by code', function () {
    $languageEn = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $languageEs = Language::create([
        'code' => 'es',
        'name' => 'Spanish',
        'native' => 'español',
        'active' => true,
        'default' => false,
    ]);

    $translation1 = Translation::create([
        'code' => 'es',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Iniciar sesión',
        'namespace' => '*',
    ]);
    $translation2 = Translation::create([
        'code' => 'en',
        'group' => 'auth',
        'key' => 'login',
        'text' => 'Login',
        'namespace' => '*',
    ]);

    Livewire::test(ListTranslations::class)
        ->sortTable('code')
        ->assertCanSeeTableRecordsInOrder([$translation2, $translation1]);
});

it('can update translation text inline', function () {
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
        ->setTableColumnState('text', $translation, 'New text')
        ->assertNotified();

    assertDatabaseHas('translations', [
        'id' => $translation->id,
        'text' => 'New text',
    ]);
});

it('can bulk delete translations', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);

    $translations = [
        Translation::create([
            'code' => 'en',
            'group' => 'auth',
            'key' => 'login',
            'text' => 'Login',
            'namespace' => '*',
        ]),
        Translation::create([
            'code' => 'en',
            'group' => 'auth',
            'key' => 'register',
            'text' => 'Register',
            'namespace' => '*',
        ]),
        Translation::create([
            'code' => 'en',
            'group' => 'common',
            'key' => 'welcome',
            'text' => 'Welcome',
            'namespace' => '*',
        ]),
    ];

    Livewire::test(ListTranslations::class)
        ->callTableBulkAction('delete', $translations)
        ->assertNotified();

    foreach ($translations as $translation) {
        expect($translation->fresh())->toBeNull();
    }
});
