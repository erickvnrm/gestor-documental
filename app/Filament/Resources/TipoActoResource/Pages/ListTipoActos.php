<?php

namespace App\Filament\Resources\TipoActoResource\Pages;

use App\Filament\Resources\TipoActoResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListTipoActos extends ListRecords
{
    protected static string $resource = TipoActoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear Tipo de Acto')
                ->icon('heroicon-o-plus'),
        ];
    }
}
