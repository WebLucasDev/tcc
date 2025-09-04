<?php

namespace Database\Seeders;

use App\Models\CollaboratorModel;
use App\Models\PositionModel;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Faker\Factory as Faker;

class CollaboratorSeeder extends Seeder
{
    /**
     * Popula a tabela 'collaborators' no banco de dados, com os dados aqui dispostos.
     */
    public function run(): void
    {
        $faker = Faker::create('pt_BR');
        $positions = PositionModel::all();

        if ($positions->isEmpty()) {
            $this->command->error('Nenhum cargo encontrado. Execute o PositionSeeder primeiro.');
            return;
        }

        $collaborators = [
            ['name' => 'João Silva', 'email' => 'joao.silva@empresa.com'],
            ['name' => 'Maria Santos', 'email' => 'maria.santos@empresa.com'],
            ['name' => 'Pedro Oliveira', 'email' => 'pedro.oliveira@empresa.com'],
            ['name' => 'Ana Costa', 'email' => 'ana.costa@empresa.com'],
            ['name' => 'Carlos Souza', 'email' => 'carlos.souza@empresa.com'],
            ['name' => 'Fernanda Lima', 'email' => 'fernanda.lima@empresa.com'],
            ['name' => 'Rafael Alves', 'email' => 'rafael.alves@empresa.com'],
            ['name' => 'Juliana Pereira', 'email' => 'juliana.pereira@empresa.com'],
            ['name' => 'Lucas Rodrigues', 'email' => 'lucas.rodrigues@empresa.com'],
            ['name' => 'Camila Ferreira', 'email' => 'camila.ferreira@empresa.com'],
            ['name' => 'Bruno Nascimento', 'email' => 'bruno.nascimento@empresa.com'],
            ['name' => 'Larissa Barbosa', 'email' => 'larissa.barbosa@empresa.com'],
            ['name' => 'Thiago Martins', 'email' => 'thiago.martins@empresa.com'],
            ['name' => 'Amanda Rocha', 'email' => 'amanda.rocha@empresa.com'],
            ['name' => 'Felipe Dias', 'email' => 'felipe.dias@empresa.com'],
            ['name' => 'Natália Cardoso', 'email' => 'natalia.cardoso@empresa.com'],
            ['name' => 'Rodrigo Mendes', 'email' => 'rodrigo.mendes@empresa.com'],
            ['name' => 'Patrícia Gomes', 'email' => 'patricia.gomes@empresa.com'],
            ['name' => 'Gustavo Torres', 'email' => 'gustavo.torres@empresa.com'],
            ['name' => 'Isabella Ribeiro', 'email' => 'isabella.ribeiro@empresa.com'],
        ];

        foreach ($collaborators as $index => $collaborator) {
            $entryTime1 = $faker->randomElement(['08:00:00', '09:00:00', '07:30:00']);
            $returnTime1 = $faker->randomElement(['12:00:00', '12:30:00']);
            $entryTime2 = $faker->randomElement(['13:00:00', '13:30:00', '14:00:00']);
            $returnTime2 = $faker->randomElement(['17:00:00', '18:00:00', '17:30:00']);

            CollaboratorModel::create([
                'name' => $collaborator['name'],
                'email' => $collaborator['email'],
                'password' => bcrypt('password'),
                'cpf' => $faker->unique()->cpf(false),
                'admission_date' => $faker->dateTimeBetween('-2 years', '-30 days')->format('Y-m-d'),
                'phone' => preg_replace('/\D/', '', $faker->cellphone(false)),
                'zip_code' => preg_replace('/\D/', '', $faker->postcode),
                'street' => $faker->streetName,
                'neighborhood' => $faker->randomElement([
                    'Centro', 'Jardim América', 'Vila Nova', 'Bela Vista', 'Santa Cruz',
                    'Jardim Paulista', 'Vila Madalena', 'Moema', 'Ibirapuera', 'Perdizes'
                ]),
                'number' => $faker->buildingNumber,
                'position_id' => $positions->random()->id,
                'entry_time_1' => $entryTime1,
                'return_time_1' => $returnTime1,
                'entry_time_2' => $entryTime2,
                'return_time_2' => $returnTime2,
                'status' => $faker->randomElement(['ativo', 'ativo', 'ativo', 'inativo']), // 75% ativo, 25% inativo
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}
