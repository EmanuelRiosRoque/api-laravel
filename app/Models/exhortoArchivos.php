<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class exhortoArchivos extends Model
{
    use HasFactory;

    protected $fillable = [
        'exhortoOrigenId',
        "archivo"
    ];

    public function exhorto()
    {
        return $this->belongsTo(Exhortos::class, 'exhortoOrigenId', 'exhortoOrigenId');
    }
}
