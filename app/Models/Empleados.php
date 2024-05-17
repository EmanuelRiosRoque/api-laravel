<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Empleados extends Model
{
    use HasFactory;

    public $table = 'update_empleados';

    public $fillable = [
        'id',
        'nuM_EMPL',
        'nombres',
        'apellidop',
        'apellidom',
        'estatus',
        'rfc',
        'curp',
        'areA_ADSCRIPCION',
        'descripcioN_AREA_ADSCRIPCION',
        'puesto',
        'descripcioN_PUESTO',
        'ubicacioN_AREA_TRABAJO',
        'nivel',
        'plaza',
        'n_tarjeta',
        'horario',
        'fk_usrCreated'
    ];
}
