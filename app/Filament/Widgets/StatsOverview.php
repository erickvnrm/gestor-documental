<?php

namespace App\Filament\Widgets;

use App\Models\Actos;
use App\Models\TipoActo;
use Carbon\Carbon;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;
use Illuminate\Support\Facades\DB;

class StatsOverview extends BaseWidget
{
    protected static ?string $pollingInterval = '15s';

    protected function getStats(): array
    {
        $stats = [];

        $year = now()->year;

        $tiposDeActo = TipoActo::all();

        foreach ($tiposDeActo as $tipoActo) {
            $consecutiveNumber = Actos::where('tipo_acto_id', $tipoActo->id)
                ->where('year', $year)
                ->max('number');

            $consecutiveNumber = $consecutiveNumber ?? 0;

            $stats[] = Stat::make("Número Consecutivo {$tipoActo->nombre_tipo_acto} - {$year}", $consecutiveNumber + 1)
                ->icon('heroicon-c-hashtag');
        }

        return $stats;
    }

    public function checkAndResetNumbers()
    {
        $year = now()->year;
        $nextResetDate = Carbon::createFromDate($year + 1, 1, 24);

        $controles = DB::table('acto_number_controls')->get();

        foreach ($controles as $control) {
            if (Carbon::now()->greaterThan(Carbon::parse($control->reset_date))) {
                DB::table('acto_number_controls')->where('id', $control->id)->update([
                    'current_number' => 1,
                    'year' => $year + 1,
                    'reset_date' => $nextResetDate->addYear(),
                ]);
            }
        }
    }

}
