<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use App\Models\Actos;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ActosController extends Controller
{
    public function getNextNumber(Request $request)
    {
        $tipoActoId = $request->tipo_acto_id;
        $year = $request->year;

        return response()->json([
            'next_number' => $this->getNextNumberSimulado($tipoActoId, $year)
        ]);
    }

    public function getNextNumberSimulado($tipoActoId, $year)
    {
        $count = Actos::where('tipo_acto_id', $tipoActoId)
                    ->whereYear('created_at', $year)
                    ->count();

        return $count + 1;
    }

    public static function updateFileName(Actos $record, string $fileUrl)
    {
        if (is_null($record->archivo_url)) {
            return;
        }
    
        // Verificar si el acto está archivado y ajustar el nombre si es necesario
        $newFileName = $record->trashed()
        ? 'archived-' . $record->getDownloadFileName()
        : $record->getDownloadFileName();
            
        $currentFilePath = str_replace(asset('storage/'), '', $fileUrl); 
        
        $newFilePath = 'archivos/' . $newFileName; 
        
        if (Storage::disk('public')->exists($currentFilePath)) {
            Storage::disk('public')->move($currentFilePath, $newFilePath);
            $record->archivo_url = $newFilePath;
            $record->save();
        }
    }
    
}
