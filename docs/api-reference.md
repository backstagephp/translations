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
    'default' => 'google-translate',
    'drivers' => [
        'google-translate' => [],
        'deep-l' => ['api_key' => env('DEEPL_API_KEY')],
        'ai' => ['api_key' => env('OPENAI_API_KEY')],
    ],
],
```
