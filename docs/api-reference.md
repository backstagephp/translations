# API Reference

## Plugin

```php
$panel->plugins([
    TranslationsPlugin::make()
        ->languageSwitcherDisabled(false)
        ->userCanManageTranslations(true)
]);
```

## Commands

```bash
php artisan translations:scan

php artisan translations:translate

php artisan translations:languages:add en English

php artisan translations:sync
```

## Models

### Language
- `active()` - Get active languages
- `default()` - Get default language
- `translations()` - Get translations

### Translation
- `language()` - Get language
- `code`, `group`, `key`, `text` - Fields

## Helpers

```php
localized_language_name('es')  // Spanish

country_flag('es')  // 🇪🇸
```

## Configuration

```php
// config/translations.php
'translators' => [
    'default' => env('TRANSLATION_DRIVER', 'google-translate'),

    'drivers' => [
        'google-translate' => [
            // no options
        ],

        'ai' => [
            'provider' => Provider::OpenAI,
            'model' => 'gpt-4.1',
            'system_prompt' => 'You translate Laravel translations strings to the language you have been asked.',
        ],

        'deep-l' => [
            'options' => [
                TranslatorOptions::SERVER_URL => env('DEEPL_SERVER_URL', 'https://api.deepl.com/'),
            ],
        ],
    ],
],
```
