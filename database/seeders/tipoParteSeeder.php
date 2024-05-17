<?php

namespace Database\Seeders;

use App\Models\tiposPartes;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class tipoParteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        tiposPartes::create([
            'id' => 0,
            'parte' => 'No definido',
        ]);

        tiposPartes::create([
            'id' => 1,
            'parte' => 'Actor, Promovente, Ofendido',
        ]);

        tiposPartes::create([
            'id' => 2,
            'parte' => 'Demandado, Inculpado, Imputado',
        ]);
    }
}
