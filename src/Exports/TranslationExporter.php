<?php

namespace Backstage\Translations\Filament\Exports;

use Backstage\Translations\Laravel\Models\Translation;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class TranslationExporter extends Exporter
{
    protected static ?string $model = Translation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code'),

            ExportColumn::make('group'),

            ExportColumn::make('key'),

            ExportColumn::make('text'),

            ExportColumn::make('namespace'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = __('Your translation export has completed and :successful_rows exported.', ['successful_rows' => number_format($export->successful_rows) . ' ' . str('row')->plural($export->successful_rows)]);

        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . __(':failed_rows failed to export.', ['failed_rows' => number_format($failedRowsCount) . ' ' . str('row')->plural($failedRowsCount)]);
        }

        return $body;
    }

    public function getFormats(): array
    {
        return config('backstage.translations.exporter.formats');
    }
}
