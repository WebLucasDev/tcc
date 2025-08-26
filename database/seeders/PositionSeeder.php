<?php

namespace Database\Seeders;

use App\Models\PositionModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = [
            ['name' => 'Desenvolvedor FullStack'],
            ['name' => 'Scrum Master'],
            ['name' => 'Product Owner'],
            ['name' => 'Tester/QA'],
            ['name' => 'Analista de Suporte N1'],
            ['name' => 'Analista de Suporte N2'],
            ['name' => 'Analista de Implantação'],
            ['name' => 'Analista Comercial'],
            ['name' => 'Auxiliar Administrativo'],
        ];

        foreach ($positions as $position) {
            PositionModel::firstOrCreate(['name' => $position['name']], $position);
        }
    }
}
