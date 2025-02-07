# Filament Translations

[![Latest Version on Packagist](https://img.shields.io/packagist/v/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/backstagephp/translations/fix-php-code-styling.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/backstagephp/translations/actions?query=workflow%3A"Fix+PHP+code+styling"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/backstagephp/translations.svg?style=flat-square)](https://packagist.org/packages/backstagephp/translations)

## Nice to meet you, we're [Backstagephp](https://backstagephp.com)

Hi! We are a web development agency from Nijmegen in the Netherlands and we use Laravel for everything: advanced websites with a lot of bells and whitles and large web applications.

## Installation

You can install the package via composer:

```bash
composer require backstage/translations
```

You can publish and run the migrations with:

```bash
php artisan vendor:publish --provider="Vormkracht10\LaravelTranslations\LaravelTranslationsServiceProvider"
php artisan migrate
```

You can publish the config file with:

```bash
php artisan vendor:publish --tag="backstage-translations-config"
php artisan vendor:publish --tag="blade-flags-config"
```

Optionally, you can publish the views using

```bash
php artisan vendor:publish --tag="backstage-translations-views"
```

Add the TranslationsPlugin to the desired panel provider:

```php
use Backstage\Translations\TranslationsPlugin;

$panel
    ->plugins([
        TranslationsPlugin::make(),
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
