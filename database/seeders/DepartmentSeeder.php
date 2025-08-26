<?php

namespace Database\Seeders;

use App\Models\DepartmentModel;
use App\Models\PositionModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $departments = [
            ['name' => 'Desenvolvimento'],
            ['name' => 'Suporte Técnico'],
            ['name' => 'Comercial'],
            ['name' => 'Administrativo'],
            ['name' => 'Implantação'],
        ];

        foreach ($departments as $department) {
            DepartmentModel::firstOrCreate(['name' => $department['name']], $department);
        }

        $this->assignPositionsToDepartments();
    }

    /**
     * Assign positions to departments
     */
    private function assignPositionsToDepartments(): void
    {
        $assignments = [
            'Desenvolvimento' => ['Desenvolvedor FullStack', 'Scrum Master', 'Product Owner', 'Tester/QA'],
            'Suporte Técnico' => ['Analista de Suporte N1', 'Analista de Suporte N2'],
            'Comercial' => ['Analista Comercial'],
            'Administrativo' => ['Auxiliar Administrativo'],
            'Implantação' => ['Analista de Implantação'],
        ];

        foreach ($assignments as $departmentName => $positionNames) {
            $department = DepartmentModel::where('name', $departmentName)->first();

            if ($department) {
                foreach ($positionNames as $positionName) {
                    $position = PositionModel::where('name', $positionName)->first();
                    if ($position) {
                        $position->update(['department_id' => $department->id]);
                    }
                }
            }
        }
    }
}
