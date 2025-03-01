<?php

namespace Backstage\Translations\Filament\Exporters;

use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Models\Export;
use Filament\Actions\Exports\Enums\ExportFormat;
use Backstage\Translations\Laravel\Models\Translation;

class TranslationExporter extends Exporter
{
    protected static ?string $model = Translation::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('code')
                ->label(__('Code')),

            ExportColumn::make('group')
                ->label(__('Group')),

            ExportColumn::make('key')
                ->label(__('Key')),

            ExportColumn::make('text')
                ->label(__('Text')),

            ExportColumn::make('metadata')
                ->label(__('Metadata')),

            ExportColumn::make('namespace')
                ->label(__('Namespace')),
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
        return [
            ExportFormat::Xlsx
        ];
    }
}
