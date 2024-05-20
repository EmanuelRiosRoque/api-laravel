<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RegistroActualizacion extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhortoId',
        'actualizacionOrigenId',
        'tipoActualizacion',
        'fechaHora',
        'descripcion'
    ];
}
