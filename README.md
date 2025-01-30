# This is the official Vormkracht10 Filament Translations package!

[![Latest Version on Packagist](https://img.shields.io/packagist/v/vormkracht10/filament-translations.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/filament-translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/vormkracht10/filament-translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/vormkracht10/filament-translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/vormkracht10/filament-translations/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/vormkracht10/filament-translations/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/vormkracht10/filament-translations.svg?style=flat-square)](https://packagist.org/packages/vormkracht10/filament-translations)

A [Laravel Translations](https://github.com/vormkracht10/laravel-translations) package build for Filament.

## Installation

You can install the package via composer:

```bash
composer require vormkracht10/filament-translations
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Vormkracht10\LaravelTranslations\LaravelTranslationsServiceProvider"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="filament-translations-config"
php artisan vendor:publish --tag="blade-flags-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="filament-translations-views"
```

Add the FilamentTranslationsPlugin to the desired panel provider:

```php
use Vormkracht10\FilamentTranslations\FilamentTranslationsPlugin;

$panel
    ->plugins([
        FilamentTranslationsPlugin::make(),
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
- [All Contributors](../../contributors)

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
