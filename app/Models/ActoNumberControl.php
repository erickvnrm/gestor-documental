<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ActoNumberControl extends Model
{
    use HasFactory;

    protected $table = 'acto_number_controls';

    protected $fillable = [
        'tipo_acto_id', 
        'current_number',
        'reset_date',
        'year',
        'created_at',
        'updated_at',
    ];
    
}
