# Filament Interface

Admin panel for managing translations.

## Languages Overview

The Languages resource provides a complete interface for managing your application's languages.

![Languages Overview](img/filament/resources/languages/languages_overview.png)

### Language Switcher

The language switcher appears in the top-right corner of the admin panel, allowing users to quickly switch between languages.

![Language Switcher Example](img/filament/resources/languages/languages_overview_switcher_example.png)

When a language is switched, the interface updates immediately and shows a confirmation notification.

![Language Switched](img/filament/resources/languages/languages_overview_language_switched.png)

## Translations Overview

The Translations resource displays all your translation strings in a searchable, filterable table.

![Translations Overview](img/filament/resources/translations/translations_overview.png)

### Scanning for Translations

Use the scan functionality to automatically find translation strings in your codebase.

![Translations Scanning](img/filament/resources/translations/translations_overview_scanning.png)

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
