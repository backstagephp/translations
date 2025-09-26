# Laravel Translations Core

The underlying package that powers the Filament interface.

## What It Does

- Scans your code for translation strings
- Manages languages and translations in database
- Provides automatic translation via multiple providers
- Handles model attribute translation

## Commands

```bash
# Scan for translations
php artisan translations:scan

# Translate strings
php artisan translations:translate

# Add language
php artisan translations:languages:add en English

# Sync translations
php artisan translations:sync
```

## Translation Providers

**Google Translate** (default)
- Free, no API key needed
- Rate limited

**DeepL**
- Better quality translations
- Requires API key

**AI (OpenAI)**
- Custom AI translations
- Requires API key

## Model Translation

Make models translatable:

```php
use HasTranslatableAttributes;

class Post extends Model
{
    use HasTranslatableAttributes;
    
    protected $translatable = ['title', 'content'];
}
```

## Configuration

Edit `config/translations.php`:

```php
'translators' => [
    'default' => env('TRANSLATION_DRIVER', 'google-translate'),
],
```

## Helpers

- `localized_language_name($code)` - Get language name
- `country_flag($code)` - Get flag emoji
