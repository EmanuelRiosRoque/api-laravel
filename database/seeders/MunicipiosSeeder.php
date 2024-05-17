<?php

namespace Database\Seeders;

use App\Models\municipio;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class MunicipiosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        municipio::create([
            'municipio' => 'Ecatepec Morelos',
            'estado_id' => 1,
        ]);
    }
}
