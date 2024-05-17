<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Acuse extends Model
{
    use HasFactory;
    protected $fillable = [
        'exhortoOrigenId',
        'municipioAreaRecibeId',
        'areaRecibeId',
        'urlInfo',
        'areaRecibeNombre',
        'folioSeguimiento'
    ];
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->folioSeguimiento = (string) Str::uuid();
        });
    }
}
