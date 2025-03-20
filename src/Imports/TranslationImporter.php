<?php

namespace Backstage\Translations\Filament\Imports;

use Backstage\Translations\Laravel\Models\Language;
use Backstage\Translations\Laravel\Models\Translation;
use Filament\Actions\Imports\ImportColumn;
use Filament\Actions\Imports\Importer;
use Filament\Actions\Imports\Models\Import;

class TranslationImporter extends Importer
{
    protected static ?string $model = Translation::class;

    public static function getColumns(): array
    {
        return [
            ImportColumn::make('code'),
            ImportColumn::make('group'),
            ImportColumn::make('key'),
            ImportColumn::make('text'),
            ImportColumn::make('namespace'),
        ];
    }

    public function resolveRecord(): ?Translation
    {
        $langCode = $this->data['code'];

        $language = Language::where('code', $langCode)->first();

        if (! $language) {
            Language::create([
                'code' => $langCode,
                'name' => localized_language_name($langCode),
                'native' => localized_language_name($langCode, explode('-', $langCode)[0]),
                'active' => true,
                'default' => false,
            ]);
        }

        $this->data = collect($this->data)->mapWithKeys(function ($value, $key) {
            return [strtolower($key) => $value];
        })
            ->unique()
            ->toArray();

        return Translation::updateOrCreate(
            ['code' => $langCode, 'group' => $this->data['group'], 'key' => $this->data['key']],
            $this->data
        );
    }

    public static function getCompletedNotificationBody(Import $import): string
    {
        $body = __('Your translation import has completed and :successful_rows imported.', ['successful_rows' => number_format($import->successful_rows) . ' ' . str('row')->plural($import->successful_rows)]);

        if ($failedRowsCount = $import->getFailedRowsCount()) {
            $body .= ' ' . __(':failed_rows failed to import.', ['failed_rows' => number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount)]);
        }

        return $body;
    }
}
