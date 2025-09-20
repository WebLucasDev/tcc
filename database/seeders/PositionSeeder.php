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
                'Desenvolvedor Frontend',
                'Desenvolvedor Backend',
                'Desenvolvedor Mobile',
                'Arquiteto de Software',
                'Tech Lead',
                'Scrum Master',
                'Tester/QA',
                'Analista de Sistemas',
                'Desenvolvedor Junior',
                'Desenvolvedor Pleno',
                'Desenvolvedor Senior',
            ],
            'Suporte Técnico' => [
                'Analista de Suporte N1',
                'Analista de Suporte N2',
                'Analista de Suporte N3',
                'Coordenador de Suporte',
                'Especialista em Suporte',
            ],
            'Comercial' => [
                'Product Owner',
                'Analista Comercial',
                'Gerente Comercial',
                'Vendedor',
                'Pré-vendas',
                'Account Manager',
                'Business Development',
            ],
            'Implantação' => [
                'Analista de Implantação',
                'Coordenador de Implantação',
                'Consultor de Implantação',
                'Especialista em Integração',
            ],
            'Administrativo' => [
                'Auxiliar Administrativo',
                'Analista Administrativo',
                'Assistente de RH',
                'Analista de RH',
                'Gerente Administrativo',
                'Contador',
                'Auxiliar Financeiro',
                'Analista Financeiro',
                'Recepcionista',
            ],
            'DevOps e Infraestrutura' => [
                'DevOps Engineer',
                'SRE (Site Reliability Engineer)',
                'Administrador de Sistemas',
                'Analista de Infraestrutura',
                'Especialista em Cloud',
                'Arquiteto de Infraestrutura',
                'Engenheiro de Redes',
            ],
            'Segurança da Informação' => [
                'Analista de Segurança',
                'Especialista em Cybersecurity',
                'Auditor de Segurança',
                'Coordenador de Segurança',
                'Pentester',
                'Analista SOC',
            ],
            'Dados e Analytics' => [
                'Cientista de Dados',
                'Engenheiro de Dados',
                'Analista de Dados',
                'Data Engineer',
                'Machine Learning Engineer',
                'Analista de BI',
                'Especialista em Big Data',
            ],
            'UX/UI Design' => [
                'UX Designer',
                'UI Designer',
                'Product Designer',
                'UX Researcher',
                'Design System Specialist',
                'Coordenador de Design',
            ],
            'Produto e Inovação' => [
                'Product Manager',
                'Product Owner',
                'Analista de Produto',
                'Gerente de Produto',
                'Innovation Manager',
                'Business Analyst',
                'Product Marketing Manager',
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
