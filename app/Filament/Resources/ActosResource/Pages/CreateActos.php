<?php

namespace App\Filament\Resources\ActosResource\Pages;

use App\Filament\Resources\ActosResource;
use Filament\Resources\Pages\CreateRecord;

class CreateActos extends CreateRecord
{
    protected static string $resource = ActosResource::class;

    protected function mutateFormDataBeforeCreate(array $data): array
    {
        if (array_key_exists('tipo_documento', $data)) {
            if ($data['tipo_documento'] === 'acto') {
                $data['state'] = 'aprobado';
            } elseif ($data['tipo_documento'] === 'oficio') {
                $data['state'] = 'anulado';
            }
        } else {
            $data['state'] = 'pendiente';
        }
    
        return $data;
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

}
