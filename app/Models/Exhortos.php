<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Exhortos extends Model
{
    use HasFactory;

    protected $table = "exhortos";
    protected $primaryKey = 'exhortoOrigenId';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $fillable = [
        'municipioDestinoId',
        'materiaClave',
        'estadoOrigenId',
        'municipioOrigenId',
        'juzgadoOrigenId',
        'juzgadoOrigenNombre',
        'numeroExpedienteOrigen',
        'numeroOficioOrigen',
        'tipoJuicioAsuntoDelitos',
        'juezExhortante',
        'fojas',
        'diasResponder',
        'tipoDiligenciacionNombre',
        'fechaOrigen',
        'observaciones'
    ];

    protected $dates = ['fechaOrigen'];

    public function partes()
    {
        return $this->hasMany(Partes::class, 'exhortoOrigenId', 'exhortoOrigenId');
    }

    public function archivos()
    {
        return $this->hasMany(ArchivoARecibir::class, 'exhortoOrigenId', 'exhortoOrigenId');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->exhortoOrigenId = (string) Str::uuid();
        });
    }
}
