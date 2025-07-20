<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\Actos;
use Illuminate\Support\Facades\Auth;

class ActosStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        $totalActos = Actos::count();

        $userActos = Actos::where('user_id', Auth::id())->count();

        $actosPendientesQuery = Actos::where('state', 'pendiente');

        if (!Auth::user()->hasRole('admin')) {
            $actosPendientesQuery->where('user_id', Auth::id());
        }
        
        $actosPendientes = $actosPendientesQuery->count();

        // Crear las estadísticas
        return [
            Stat::make('Total Actos', number_format($totalActos))
                ->description('Actos generados en el sistema')
                ->descriptionIcon('heroicon-o-document')
                ->icon('heroicon-o-document')
                ->color('primary'),
                
            Stat::make('Mis Actos', number_format($userActos))
                ->description('Tus actos generados')
                ->descriptionIcon('heroicon-o-user')
                ->icon('heroicon-o-user')
                ->color('success'),
                
            Stat::make('Actos Pendientes', number_format($actosPendientes))
                ->description('Actos aún pendientes de aprobación')
                ->descriptionIcon('heroicon-o-clock')
                ->icon('heroicon-o-clock')
                ->color('warning'),
        ];
    }
}
