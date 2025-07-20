<?php

namespace App\Filament\Resources\ActosResource\Pages;

use App\Filament\Resources\ActosResource;
use App\Models\Actos;
use Filament\Actions;
use Illuminate\Support\Facades\Auth;
use Filament\Resources\Components\Tab as ComponentsTab;
use Filament\Resources\Pages\ListRecords;

class ListActos extends ListRecords
{
    protected static string $resource = ActosResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make()->label('Crear Acto')
                ->icon('heroicon-o-plus'),
        ];
    }

    public function getTabs(): array
    {
        $user = Auth::user(); // Obtener el usuario autenticado
        
        // Si el usuario es admin, permitimos ver todos los registros
        $queryBase = Actos::query();
        
        if (!$user->hasRole('admin')) {
            // Si no es admin, filtramos por el user_id del usuario
            $queryBase->where('user_id', $user->id);
        }
    
        // Tab para todos los registros
        $tabs = [
            'all' => ComponentsTab::make('Todos')
                ->badge($queryBase->count())  // Contamos todos los actos del usuario actual o de todos si es admin
                ->modifyQueryUsing(function ($query) use ($user) {
                    // Si el usuario no es admin, filtramos por su user_id
                    if (!$user->hasRole('admin')) {
                        return $query->where('user_id', $user->id);
                    }
                    return $query;
                }),
        ];
    
        // Crear tabs dinámicas basadas en los posibles valores de 'state'
        $estados = ['pendiente', 'aprobado', 'anulado'];
    
        foreach ($estados as $estado) {
            // Clonar la consulta base para evitar modificarla en cada ciclo
            $queryClone = clone $queryBase;
    
            // Convertir el estado en formato de título
            $nombreEstado = ucfirst($estado);
    
            // Agregar una pestaña para cada estado, filtrando por user_id si no es admin
            $tabs[$estado] = ComponentsTab::make($nombreEstado)
                ->badge($queryClone->where('state', $estado)->count())  // Contamos los actos de este estado
                ->modifyQueryUsing(function ($query) use ($estado, $user) {
                    // Si el usuario no es admin, filtramos por user_id
                    if (!$user->hasRole('admin')) {
                        return $query->where('user_id', $user->id)->where('state', $estado);
                    }
                    return $query->where('state', $estado);  // Filtrar solo por estado si es admin
                });
        }

        if ($user->hasRole('admin')) {
            $tabs['archived'] = ComponentsTab::make('Archivados')
                ->badge(Actos::onlyTrashed()->count())
                ->modifyQueryUsing(function ($query){
                    return $query->onlyTrashed();
                });
        }

        return $tabs;
    }
    

}
