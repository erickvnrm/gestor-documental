<?php

namespace App\Filament\Resources\ActosResource\Pages;

use App\Filament\Resources\ActosResource;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditActos extends EditRecord
{
    protected static string $resource = ActosResource::class;

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function mutateFormDataBeforeSave(array $data): array
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

    protected function handleRecordUpdate(Model $record, array $data): Model
    {
        $data['updated_by'] = auth()->id();
        $record->update($data);

        return $record;
    }

}
