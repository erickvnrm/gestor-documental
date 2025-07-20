<?php

namespace App\Filament\Widgets;

use App\Models\Actos;
use Filament\Widgets\TableWidget as BaseWidget;
use Filament\Tables;
use Illuminate\Database\Eloquent\Builder;

class LatestActosUsers extends BaseWidget
{
    protected static ?string $heading = 'Últimos Usuarios que Crearon Actos';

    protected function getTableQuery(): Builder
    {
        return Actos::query()
            ->with(['usuario.area'])
            ->latest()
            ->limit(5);
    }

    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('usuario_full_name')
                ->label('Nombre')
                ->color('info')
                ->getStateUsing(fn (Actos $record) => wordwrap("{$record->usuario->name} {$record->usuario->last_name}", 8, "\n", true))
                ->wrap(),
            Tables\Columns\TextColumn::make('usuario.area.name_area')
                ->label('Área')
                ->wrap(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->wrap()
                ->dateTime(),
        ];
    }

    protected function isTablePaginationEnabled(): bool
    {
        // Desactivar la paginación
        return false;
    }
}
