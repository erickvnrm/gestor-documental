<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Models\EjeTematico;
use Illuminate\Database\Eloquent\SoftDeletes;

class Actos extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'actos';

    protected $fillable = [
        'user_id',
        'tipo_acto_id',
        'eje_tematico_id',
        'number',
        'year',
        'archivo_url', 
        'fecha', 
        'titulo',
        'observacion',
        'tipo_documento',
        'created_at',
        'updated_at',
        'state',
        'updated_by',
    ];

    protected $casts = [
        'fecha' => 'datetime', // objeto de fecha/hora
    ];

    public static function boot()
    {
        parent::boot();
    
        // Antes de crear un acto, asigna el año actual si no está ya asignado
        static::creating(function ($acto) {
            $acto->year = $acto->year ?? now()->year; // Establece el año actual si no está ya asignado
            $acto->number = $acto->number ?? static::generateNextNumber($acto->tipo_acto_id); // Genera el siguiente número si no está asignado
        });
    }   

    public static function generateNextNumber($tipo_acto_id)
    {
        return DB::transaction(function () use ($tipo_acto_id) {
            $year = now()->year;
            $nextResetDate = static::getNextResetDate($year);
    
            // Obtenemos el control del número
            $control = DB::table('acto_number_controls')
                ->where('tipo_acto_id', $tipo_acto_id)
                ->where('year', $year)
                ->lockForUpdate()
                ->first();
    
            if (!$control) {
                // Si no existe el control, lo creamos e inicializamos
                DB::table('acto_number_controls')->insert([
                    'tipo_acto_id' => $tipo_acto_id,
                    'current_number' => 1,
                    'year' => $year,
                    'reset_date' => $nextResetDate,
                ]);
                return 1;
            }
    
            // Si la fecha actual es posterior a la de reset, reiniciamos el número
            if (static::shouldReset($control->reset_date)) {
                DB::table('acto_number_controls')->where('id', $control->id)->update([
                    'current_number' => 1,
                    'year' => $year + 1,
                    'reset_date' => static::getNextResetDate($year + 1),
                ]);
                return 1;
            }
    
            // Incrementamos el número actual
            $nextNumber = $control->current_number + 1;
            DB::table('acto_number_controls')->where('id', $control->id)->update(['current_number' => $nextNumber]);
    
            return $nextNumber;
        });
    }
    
    /**
     * Determina la fecha de reset, que es el 24 de enero del siguiente año.
     */
    protected static function getNextResetDate($year)
    {
        return Carbon::createFromDate($year + 1, 1, 24);
    }
    
    /**
     * Verifica si la fecha actual es posterior a la fecha de reset.
     */
    protected static function shouldReset($resetDate)
    {
        return Carbon::now()->greaterThan(Carbon::parse($resetDate));
    }   

    public function usuario()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function updatedByUser()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }

    public function tipoActo()
    {
        return $this->belongsTo(TipoActo::class)->withTrashed();
    }

    public function ejeTematico()
    {
        return $this->belongsTo(EjeTematico::class, 'eje_tematico_id');
    }

    public function getFechaSinHoraAttribute()
    {
        return $this->fecha->format('d-m-Y');
    }

    public function getFormattedNumberAttribute()
    {
        return str_pad($this->number, 3, '0', STR_PAD_LEFT);
    }

    public function getFormattedTitleAttribute()
    {
        $tipoActo = $this->tipoActo->nombre_tipo_acto;
        $numero = $this->formatted_number;
        $fecha = Carbon::parse($this->fecha)->locale('es')->isoFormat('LL');

        return "{$tipoActo} {$numero} del {$fecha}";
    }

    public function getDownloadFileName(): string
    {
        // Obtén el tipo de documento (por ejemplo, "acto" o "Decreto")
        $tipoDocumento = strtolower($this->tipo_documento) === 'acto' ? ucfirst($this->tipoActo->nombre_tipo_acto) : 'Oficio';
        
        // Formatea la fecha (d-m-Y)
        $fecha = $this->fecha->format('d-m-Y');
        
        // Formatea el número (con ceros a la izquierda si es necesario)
        $numero = str_pad($this->number, 3, '0', STR_PAD_LEFT);
        
        // Construir el nombre del archivo: ejemplo "Decreto-20-08-2024-001.pdf" o "acto-20-08-2024-001.pdf"
        return "{$tipoDocumento}-{$fecha}-{$numero}.pdf";
    }
    
}

