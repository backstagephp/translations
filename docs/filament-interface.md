# Filament Interface

Admin panel for managing translations.

## Features

- **Languages**: Add, edit, manage languages
- **Translations**: Edit translation text
- **Language Switcher**: Switch languages in admin
- **Import/Export**: CSV/XLSX support
- **Auto-translate**: Queue translations

## Configuration

```php
$panel->plugins([
    TranslationsPlugin::make()
        ->languageSwitcherDisabled(false)
        ->userCanManageTranslations(true)
]);
```
