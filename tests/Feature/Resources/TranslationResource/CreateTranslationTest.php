<?php

use Backstage\Translations\Filament\Resources\TranslationResource\Pages\CreateTranslation;
use Backstage\Translations\Laravel\Models\Language;
use Livewire\Livewire;

use function Pest\Laravel\assertDatabaseHas;

it('can create a translation', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    
    $translationData = [
        'text' => 'Welcome to our application',
    ];

    Livewire::test(CreateTranslation::class)
        ->fillForm($translationData)
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('translations', [
        'text' => $translationData['text'],
        'code' => $language->code,
    ]);
});

it('validates required text field', function () {
    Livewire::test(CreateTranslation::class)
        ->fillForm([])
        ->call('create')
        ->assertHasNoFormErrors();

    });

it('can create translation with multiline text', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $multilineText = "Line 1\nLine 2\nLine 3";

    Livewire::test(CreateTranslation::class)
        ->fillForm(['text' => $multilineText])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('translations', [
        'text' => $multilineText,
        'code' => $language->code,
    ]);
});

it('can create translation with special characters', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $specialText = 'Hello "world" & welcome! @#$%^&*()';

    Livewire::test(CreateTranslation::class)
        ->fillForm(['text' => $specialText])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('translations', [
        'text' => $specialText,
        'code' => $language->code,
    ]);
});

it('can create translation with HTML content', function () {
    $language = Language::create([
        'code' => 'en',
        'name' => 'English',
        'native' => 'English',
        'active' => true,
        'default' => false,
    ]);
    $htmlText = '<p>Welcome to <strong>our app</strong></p>';

    Livewire::test(CreateTranslation::class)
        ->fillForm(['text' => $htmlText])
        ->call('create')
        ->assertHasNoFormErrors()
        ->assertRedirect();

    assertDatabaseHas('translations', [
        'text' => $htmlText,
        'code' => $language->code,
    ]);
});
