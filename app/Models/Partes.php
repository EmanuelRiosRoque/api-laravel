<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Partes extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombre',
        'apellidoPaterno',
        'apellidoMaterno',
        'genero',
        'esPersonaMoral',
        'tipoParte',
        'tipoParteNombre',
        'exhortoOrigenId'
    ];

    public function exhorto()
    {
        return $this->belongsTo(Exhortos::class, 'exhortoOrigenId', 'exhortoOrigenId');
    }
}
