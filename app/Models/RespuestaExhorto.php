<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class RespuestaExhorto extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhortoId',
        'respuestaOrigenId',
        'municipioTurnadoId',
        'areaTurnadoId', // Asegurarse de incluir todos los campos, incluso los nullable
        'areaTurnadoNombre',
        'numeroExhorto',
        'tipoDiligenciado',
        'observaciones'
    ];

    public function archivos()
    {
        return $this->hasMany(ArchivoARecibir::class, 'exhortoOrigenId', 'exhortoId');
    }
}
