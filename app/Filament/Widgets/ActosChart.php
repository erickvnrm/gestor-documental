<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use App\Models\Actos;
use Illuminate\Support\Carbon;

class ActosChart extends ChartWidget
{
    protected static ?string $heading = 'Estadísticas de Actos';

    public ?string $filter = 'today';
    
    // Agregar un selector de filtro
    protected function getFilters(): ?array
    {
        return [
            'today' => 'Hoy',
            'week' => 'Última semana',
            'month' => 'Último mes',
        ];
    }
    
    // Obtener los datos del gráfico
    protected function getData(): array
    {
        $query = Actos::query();
    
        // Ajustar la consulta según el filtro
        if ($this->filter === 'week') {
            // Para la última semana, agrupar por día
            $query->where('created_at', '>=', Carbon::now()->subWeek());
            $actos = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get();
            $labels = $actos->pluck('date');
            $data = $actos->pluck('count');
        } elseif ($this->filter === 'month') {
            // Para el último mes, agrupar por día
            $query->where('created_at', '>=', Carbon::now()->subMonth());
            $actos = $query->selectRaw('DATE(created_at) as date, COUNT(*) as count')
                ->groupBy('date')
                ->get();
            $labels = $actos->pluck('date');
            $data = $actos->pluck('count');
        } else {
            // Para hoy, agrupar por hora
            $query->where('created_at', '>=', Carbon::now()->startOfDay());
            $actos = $query->selectRaw('HOUR(created_at) as hour, COUNT(*) as count')
                ->groupBy('hour')
                ->get();
            $labels = $actos->pluck('hour')->map(fn($hour) => $hour . ':00');
            $data = $actos->pluck('count');
        }
    
        return [
            'datasets' => [
                [
                    'label' => 'Actos generados',
                    'data' => $data,
                ],
            ],
            'labels' => $labels,
        ];
    }
    
    protected function getType(): string
    {
        return 'bar';
    }
}
