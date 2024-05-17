<?php

namespace Database\Seeders;

use App\Models\estado;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        estado::create([
            'estado' => 'Estado de México',
        ]);
    }
}
