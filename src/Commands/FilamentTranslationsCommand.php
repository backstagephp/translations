<?php

namespace Vormkracht10\FilamentTranslations\Commands;

use Illuminate\Console\Command;

use function Laravel\Prompts\error;
use function Laravel\Prompts\info;
use function Laravel\Prompts\text;
class FilamentTranslationsCommand extends Command
{
    public $signature = 'filament-translations-dev:import';

    public $description = 'Import translations from Filament';

    public function handle()
    {
         $functions = [
            'trans',
            'trans_choice',
            'Lang::get',
            'Lang::choice',
            'Lang::trans',
            'Lang::transChoice',
            '@lang',
            '@choice',
            '__',
        ];

            
    }
}
