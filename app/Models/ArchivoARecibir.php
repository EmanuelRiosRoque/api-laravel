<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ArchivoARecibir extends Model
{
    use HasFactory;

    protected $fillable = [
        'nombreArchivo',
        'hashSha1',
        'hashSha256',
        'tipoDocumento',
        'exhortoOrigenId'
    ];

    public function exhorto()
    {
        return $this->belongsTo(Exhortos::class, 'exhortoOrigenId', 'exhortoOrigenId');
    }
}
