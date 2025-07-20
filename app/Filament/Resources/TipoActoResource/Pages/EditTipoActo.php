<?php

namespace App\Filament\Resources\TipoActoResource\Pages;

use App\Filament\Resources\TipoActoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditTipoActo extends EditRecord
{
    protected static string $resource = TipoActoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
