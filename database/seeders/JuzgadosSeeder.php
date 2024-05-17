<?php

namespace Database\Seeders;

use App\Models\juzgados;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class JuzgadosSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        juzgados::create([
            'juzgado' => 'Juzgado de lo penal Ecatepec',
        ]);
    }
}
