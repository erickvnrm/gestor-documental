<?php

namespace App\Filament\Resources\EjeTematicoResource\Pages;

use App\Filament\Resources\EjeTematicoResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditEjeTematico extends EditRecord
{
    protected static string $resource = EjeTematicoResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
