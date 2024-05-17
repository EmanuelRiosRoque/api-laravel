<?php

namespace Database\Seeders;

use App\Models\tipoDocumentos;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipoDocumentoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        tipoDocumentos::create([
            'id' => 1,
            'tipo' => 'Oficio',
        ]);

        tipoDocumentos::create([
            'id' => 2,
            'tipo' => 'Acuerdo',
        ]);

        tipoDocumentos::create([
            'id' => 3,
            'tipo' => 'Anexo',
        ]);
    }
}
