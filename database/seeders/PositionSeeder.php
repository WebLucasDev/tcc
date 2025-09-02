<?php

namespace Database\Seeders;

use App\Models\PositionModel;
use App\Models\DepartmentModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PositionSeeder extends Seeder
{
    /**
     * Popula a tabela 'positions' no banco de dados, com os dados aqui dispostos.
     */
    public function run(): void
    {
        $positionsByDepartment = [
            'Desenvolvimento' => [
                'Desenvolvedor FullStack',
                'Scrum Master',
                'Tester/QA',
            ],
            'Suporte Técnico' => [
                'Analista de Suporte N1',
                'Analista de Suporte N2',
            ],
            'Comercial' => [
                'Product Owner',
                'Analista Comercial',
            ],
            'Implantação' => [
                'Analista de Implantação',
            ],
            'Administrativo' => [
                'Auxiliar Administrativo',
            ],
        ];

        foreach ($positionsByDepartment as $departmentName => $positions) {
            $department = DepartmentModel::where('name', $departmentName)->first();

            if ($department) {
                foreach ($positions as $positionName) {
                    PositionModel::firstOrCreate(
                        ['name' => $positionName],
                        [
                            'name' => $positionName,
                            'department_id' => $department->id,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ]
                    );
                }
            }
        }
    }
}
