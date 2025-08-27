<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CollaboratorSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $positions = \App\Models\PositionModel::all();

        if ($positions->isEmpty()) {
            $this->command->warn('Nenhum cargo encontrado. Execute primeiro a seeder de cargos.');
            return;
        }

        $collaborators = [
            ['name' => 'João Silva', 'email' => 'joao.silva@empresa.com', 'cpf' => '123.456.789-01'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@empresa.com', 'cpf' => '123.456.789-02'],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@empresa.com', 'cpf' => '123.456.789-03'],
            ['name' => 'Ana Costa', 'email' => 'ana.costa@empresa.com', 'cpf' => '123.456.789-04'],
            ['name' => 'Carlos Souza', 'email' => 'carlos.souza@empresa.com', 'cpf' => '123.456.789-05'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@empresa.com', 'cpf' => '123.456.789-06'],
            ['name' => 'Rafael Alves', 'email' => 'rafael.alves@empresa.com', 'cpf' => '123.456.789-07'],
            ['name' => 'Juliana Pereira', 'email' => 'juliana.pereira@empresa.com', 'cpf' => '123.456.789-08'],
            ['name' => 'Lucas Rodrigues', 'email' => 'lucas.rodrigues@empresa.com', 'cpf' => '123.456.789-09'],
            ['name' => 'Camila Ferreira', 'email' => 'camila.ferreira@empresa.com', 'cpf' => '123.456.789-10'],
            ['name' => 'Bruno Nascimento', 'email' => 'bruno.nascimento@empresa.com', 'cpf' => '123.456.789-11'],
            ['name' => 'Larissa Barbosa', 'email' => 'larissa.barbosa@empresa.com', 'cpf' => '123.456.789-12'],
            ['name' => 'Thiago Martins', 'email' => 'thiago.martins@empresa.com', 'cpf' => '123.456.789-13'],
            ['name' => 'Amanda Rocha', 'email' => 'amanda.rocha@empresa.com', 'cpf' => '123.456.789-14'],
            ['name' => 'Felipe Dias', 'email' => 'felipe.dias@empresa.com', 'cpf' => '123.456.789-15'],
            ['name' => 'Natália Cardoso', 'email' => 'natalia.cardoso@empresa.com', 'cpf' => '123.456.789-16'],
            ['name' => 'Rodrigo Mendes', 'email' => 'rodrigo.mendes@empresa.com', 'cpf' => '123.456.789-17'],
            ['name' => 'Patrícia Gomes', 'email' => 'patricia.gomes@empresa.com', 'cpf' => '123.456.789-18'],
            ['name' => 'Gustavo Torres', 'email' => 'gustavo.torres@empresa.com', 'cpf' => '123.456.789-19'],
            ['name' => 'Isabella Ribeiro', 'email' => 'isabella.ribeiro@empresa.com', 'cpf' => '123.456.789-20'],
        ];

        foreach ($collaborators as $collaborator) {
            \App\Models\CollaboratorModel::create([
                'name' => $collaborator['name'],
                'email' => $collaborator['email'],
                'password' => bcrypt('password'),
                'cpf' => $collaborator['cpf'],
                'admission_date' => now()->subDays(rand(30, 365)),
                'entry_time_1' => '08:00:00',
                'position_id' => $positions->random()->id,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $this->command->info('20 colaboradores criados com sucesso!');
    }
}
