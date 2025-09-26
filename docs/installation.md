# Installation

## Install

```bash
composer require backstage/translations
```

## Setup

```bash
# Publish migrations
php artisan vendor:publish --provider="Backstage\Translations\Laravel\TranslationServiceProvider"

php artisan vendor:publish --provider="Backstage\Translations\Filament\TranslationServiceProvider"

# Run migrations
php artisan migrate

# Publish config
php artisan vendor:publish --tag="translations-config"
php artisan vendor:publish --tag="backstage-translations-config"
```

## Add to Filament

```php
use Backstage\Translations\Filament\TranslationsPlugin;

$panel->plugins([
    TranslationsPlugin::make(),
]);
```

## Quick Start

```bash
# Add languages
php artisan translations:languages:add en English

php artisan translations:languages:add es Spanish

# Scan for translations
php artisan translations:scan

# Translate strings
php artisan translations:translate
```

## Environment

```env
TRANSLATION_DRIVER=google-translate
DEEPL_API_KEY=your_key  # Optional
OPENAI_API_KEY=your_key  # Optional
```
