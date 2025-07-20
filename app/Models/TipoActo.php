<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoActo extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'tipo_actos';

    protected $fillable = [
        'nombre_tipo_acto',
        'created_at',
        'updated_at',
    ];

}
