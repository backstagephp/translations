# Backstage: Translations for Filament

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)

## Nice to meet you, we're [Vormkracht10](https://vormkracht10.nl)

Hi! We're a digital agency from Nijmegen in the Netherlands and we use Laravel for everything: advanced websites with a lot of bells and whistles and large web applications.

## Before using

Please read this documentation first: [https://github.com/backstagephp/laravel-translations](https://github.com/backstagephp/laravel-translations)

## Installation

You can install the package via composer:

```bash
composer require backstage/translations
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Backstage\Translations\Laravel\TranslationServiceProvider"
php artisan vendor:publish --provider="Backstage\Translations\Filament\TranslationServiceProvider"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="translations-config"
php artisan vendor:publish --tag="backstage-translations-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="backstage-translations-views"
```

Add the TranslationsPlugin to the desired panel provider:

```php
use Backstage\Translations\Filament\TranslationsPlugin;

$panel
    ->plugins([
        TranslationsPlugin::make(),
    ]);
```

Optionally, you can disable the language switcher and rely on the ``default`` language:
 ```php
use Backstage\Translations\Filament\TranslationsPlugin;

$panel
    ->plugins([
        TranslationsPlugin::make()
            ->languageSwitcherDisabled(),
    ]);
```

If you want to show only the language switcher in a panel, you can set the `canManageTranslations` to `false`:

 ```php
use Backstage\Translations\Filament\TranslationsPlugin;

$panel
    ->plugins([
        TranslationsPlugin::make()
            ->canManageTranslations(false),
    ]);
```

## Testing

```bash
composer test
```

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

## Contributing

Please see [CONTRIBUTING](.github/CONTRIBUTING.md) for details.

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

## Credits

- [Manoj Hortulanus](https://github.com/vormkracht10)
- [Mark van Eijk](https://github.com/markvaneijk)
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
