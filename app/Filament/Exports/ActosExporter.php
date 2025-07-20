<?php

namespace App\Filament\Exports;

use App\Models\Actos;
use Filament\Actions\Exports\ExportColumn;
use Filament\Actions\Exports\Exporter;
use Filament\Actions\Exports\Models\Export;

class ActosExporter extends Exporter
{
    protected static ?string $model = Actos::class;

    public static function getColumns(): array
    {
        return [
            ExportColumn::make('usuario.name')
                ->label('Nombre'),
            ExportColumn::make('usuario.last_name')
                ->label('Apellido'),
            ExportColumn::make('titulo')
                ->label('Título'),
            ExportColumn::make('ejeTematico.name_eje_tematico'),
            ExportColumn::make('number')
                ->label('Número consecutivo'),
            ExportColumn::make('state')
                ->label('Estado'),
            ExportColumn::make('fecha_sin_hora')
                ->label('Fecha de creación'),
        ];
    }

    public static function getCompletedNotificationBody(Export $export): string
    {
        $body = 'La exportación de actos ha finalizado y se han exportado ' . number_format($export->successful_rows) . ' ' . str('fila')->plural($export->successful_rows) . '.';
    
        if ($failedRowsCount = $export->getFailedRowsCount()) {
            $body .= ' ' . number_format($failedRowsCount) . ' ' . str('fila')->plural($failedRowsCount) . ' no se pudieron exportar.';
        }
    
        return $body;
    }    
}
