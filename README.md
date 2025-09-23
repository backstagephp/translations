# Backstage Translations for Filament

> **A powerful, developer-friendly translation management solution built for Laravel and Filament**

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)

## Features

- **Language Management** - Add, edit, and manage supported languages with country flags
- **Translation Interface** - Beautiful table-based translation editing with inline updates
- **Language Switcher** - Global language switcher component for easy language switching
- **Import/Export** - CSV and Excel import/export functionality for bulk operations
- **Translation Status** - Track which translations are complete and need attention
- **Bulk Operations** - Efficient bulk translation management and updates
- **Search & Filter** - Advanced filtering and search capabilities
- **Country Flags** - Visual language identification with beautiful flag icons
- **Real-time Updates** - Live translation updates with automatic timestamp tracking
- **Multi-language Editing** - Edit multiple language versions in a single modal

## Quick Start

### Installation

```bash
composer require backstage/translations

php artisan vendor:publish --provider="Backstage\Translations\Laravel\TranslationServiceProvider"
php artisan vendor:publish --provider="Backstage\Translations\Filament\TranslationServiceProvider"
php artisan migrate
```

### Add to Filament Panel

```php
use Backstage\Translations\Filament\TranslationsPlugin;

$panel->plugins([
    TranslationsPlugin::make(),
]);
```

That's it! Your translation management system is ready to go.

## Real-World Examples

### Add Languages

```php
use Backstage\Translations\Laravel\Models\Language;

// Add English
Language::create([
    'code' => 'en',
    'name' => 'English',
    'native' => 'English',
    'active' => true,
    'default' => true,
]);

// Add Dutch
Language::create([
    'code' => 'nl',
    'name' => 'Dutch',
    'native' => 'Nederlands',
    'active' => true,
    'default' => false,
]);
```

### Add Translations

```php
use Backstage\Translations\Laravel\Models\Translation;

$translations = [
    'auth.failed' => [
        'en' => 'These credentials do not match our records.',
        'nl' => 'Deze inloggegevens komen niet overeen met onze gegevens.',
    ],
    'auth.password' => [
        'en' => 'The provided password is incorrect.',
        'nl' => 'Het opgegeven wachtwoord is onjuist.',
    ],
];

foreach ($translations as $key => $langs) {
    foreach ($langs as $code => $text) {
        Translation::create([
            'key' => $key,
            'code' => $code,
            'text' => $text,
            'group' => 'auth',
            'translated_at' => now(),
        ]);
    }
}
```

### Custom Language Switcher

```blade
@livewire('backstage.translations::switcher')
```

## Documentation

- [Installation Guide](docs/installation.md)
- [Configuration](docs/configuration.md)
- [Filament Integration](docs/filament.md)
- [API Reference](docs/api.md)
- [Models & Relationships](docs/models.md)
- [Usage Examples](docs/usage.md)
- [Troubleshooting](docs/troubleshooting.md)

## Testing

Run the test suite using Composer:

```bash
composer test
```

## Use Cases

- **Multi-language Websites** - Manage translations for international websites
- **SaaS Applications** - Localize your application for different markets
- **E-commerce Platforms** - Translate product descriptions and UI elements
- **Content Management** - Manage multilingual content efficiently
- **Team Collaboration** - Allow translators to work on translations independently
- **API Localization** - Provide localized responses for different regions

## Requirements

- PHP 8.2+
- Laravel 10+
- Filament v4
- MySQL/PostgreSQL

## Contributing

We welcome contributions! Please see our [Contributing Guide](CONTRIBUTING.md) for details.

## Contributors

<a href="https://github.com/arduinomaster22">
  <img src="https://github.com/arduinomaster22.png?size=100" width="100" height="100" alt="Manoj Hortulanus" style="border-radius: 50%;">
</a>
<a href="https://github.com/markvaneijk">
  <img src="https://github.com/markvaneijk.png?size=100" width="100" height="100" alt="Mark van Eijk" style="border-radius: 50%;">
</a>

## Support

- Email: manoj@backstagephp.com
- Issues: [GitHub Issues](https://github.com/backstagephp/translations/issues)
- Discussions: [GitHub Discussions](https://github.com/backstagephp/translations/discussions)

---

**Made with ❤️ by [Backstage PHP](https://backstagephp.com)**
