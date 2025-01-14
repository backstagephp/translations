<?php

namespace Vormkracht10\FilamentTranslations\Tables;

use Filament\Forms\Components\Concerns\CanBeNative;
use Filament\Tables\Columns\SelectColumn as ColumnsSelectColumn;

class SelectColumn extends ColumnsSelectColumn
{
    use CanBeNative;

    protected string $view = 'filament-tables::columns.select-column';
}
