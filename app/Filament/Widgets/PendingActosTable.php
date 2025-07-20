<?php

namespace App\Filament\Widgets;

use Filament\Tables;
use App\Models\Actos;
use Illuminate\Database\Eloquent\Builder;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\Auth;

class PendingActosTable extends BaseWidget
{
    protected function getTableQuery(): Builder
    {
        $query = Actos::query()->where('state', 'pendiente');

        if (!Auth::user()->hasRole('admin')) {
            $query->where('user_id', Auth::id());
        }

        return $query->with('usuario');
    }
    
    protected function getTableColumns(): array
    {
        return [
            Tables\Columns\TextColumn::make('usuario.name')
                ->label('Usuario')
                ->getStateUsing(function ($record) {
                    return wordwrap($record->usuario->name . ' ' . $record->usuario->last_name, 8, "\n", true);
                })
                ->searchable()
                ->wrap(),
            Tables\Columns\TextColumn::make('number')
                ->label('Número')
                ->sortable()
                ->searchable(query: function (Builder $query, string $search): Builder {
                    return $query
                        ->where('number', 'like', "%{$search}%");
                }),
            Tables\Columns\TextColumn::make('tipoActo.nombre_tipo_acto')
                ->label('Tipo de Acto'),
            Tables\Columns\TextColumn::make('formatted_title')
                ->label('Título')
                ->wrap(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Fecha de Creación')
                ->dateTime()
                ->wrap(),
            Tables\Columns\TextColumn::make('state')
                ->label('Estado')
                ->color('warning')
                ->badge(),
        ];
    }

    protected function getTableHeading(): string
    {
        return 'Actos Pendientes';
    }

    public function getColumnSpan(): int | string | array
    {
        return 2;
    }

    
}
