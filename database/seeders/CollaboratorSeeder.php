<?php

namespace Database\Seeders;

use App\Models\CollaboratorModel;
use App\Models\PositionModel;
use App\Models\WorkHoursModel;
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
        $workHours = WorkHoursModel::active()->get();

        // Garantir que algumas jornadas de 2 turnos sejam usadas
        $twoShiftWorkHours = WorkHoursModel::active()
            ->whereNotNull('monday_entry_2')
            ->whereNotNull('monday_exit_2')
            ->get();
        $oneShiftWorkHours = WorkHoursModel::active()
            ->where(function($query) {
                $query->whereNull('monday_entry_2')->orWhereNull('monday_exit_2');
            })
            ->get();

        if ($positions->isEmpty()) {
            $this->command->error('Nenhum cargo encontrado. Execute o PositionSeeder primeiro.');
            return;
        }

        if ($workHours->isEmpty()) {
            $this->command->error('Nenhum horário de trabalho encontrado. Execute o WorkHoursSeeder primeiro.');
            return;
        }

        $collaborators = [
            // Colaboradores originais (20)
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

            // Novos colaboradores (80 adicionais)
            ['name' => 'Eduardo Machado', 'email' => 'eduardo.machado@empresa.com'],
            ['name' => 'Vanessa Moreira', 'email' => 'vanessa.moreira@empresa.com'],
            ['name' => 'Gabriel Freitas', 'email' => 'gabriel.freitas@empresa.com'],
            ['name' => 'Priscila Castro', 'email' => 'priscila.castro@empresa.com'],
            ['name' => 'Marcelo Vieira', 'email' => 'marcelo.vieira@empresa.com'],
            ['name' => 'Aline Cavalcanti', 'email' => 'aline.cavalcanti@empresa.com'],
            ['name' => 'Diego Santana', 'email' => 'diego.santana@empresa.com'],
            ['name' => 'Bianca Nunes', 'email' => 'bianca.nunes@empresa.com'],
            ['name' => 'Alexandre Correia', 'email' => 'alexandre.correia@empresa.com'],
            ['name' => 'Tatiane Fonseca', 'email' => 'tatiane.fonseca@empresa.com'],
            ['name' => 'Renato Batista', 'email' => 'renato.batista@empresa.com'],
            ['name' => 'Carla Melo', 'email' => 'carla.melo@empresa.com'],
            ['name' => 'Vinícius Araújo', 'email' => 'vinicius.araujo@empresa.com'],
            ['name' => 'Débora Campos', 'email' => 'debora.campos@empresa.com'],
            ['name' => 'André Pinto', 'email' => 'andre.pinto@empresa.com'],
            ['name' => 'Mônica Reis', 'email' => 'monica.reis@empresa.com'],
            ['name' => 'Fábio Teixeira', 'email' => 'fabio.teixeira@empresa.com'],
            ['name' => 'Simone Xavier', 'email' => 'simone.xavier@empresa.com'],
            ['name' => 'Leandro Vargas', 'email' => 'leandro.vargas@empresa.com'],
            ['name' => 'Cristina Lopes', 'email' => 'cristina.lopes@empresa.com'],
            ['name' => 'Márcio Cunha', 'email' => 'marcio.cunha@empresa.com'],
            ['name' => 'Eliane Moraes', 'email' => 'eliane.moraes@empresa.com'],
            ['name' => 'Danilo Ribeiro', 'email' => 'danilo.ribeiro@empresa.com'],
            ['name' => 'Cláudia Silveira', 'email' => 'claudia.silveira@empresa.com'],
            ['name' => 'Roberto Franco', 'email' => 'roberto.franco@empresa.com'],
            ['name' => 'Viviane Duarte', 'email' => 'viviane.duarte@empresa.com'],
            ['name' => 'Henrique Monteiro', 'email' => 'henrique.monteiro@empresa.com'],
            ['name' => 'Luciana Borges', 'email' => 'luciana.borges@empresa.com'],
            ['name' => 'Sérgio Medeiros', 'email' => 'sergio.medeiros@empresa.com'],
            ['name' => 'Raquel Miranda', 'email' => 'raquel.miranda@empresa.com'],
            ['name' => 'Leonardo Barbosa', 'email' => 'leonardo.barbosa@empresa.com'],
            ['name' => 'Mariana Guimarães', 'email' => 'mariana.guimaraes@empresa.com'],
            ['name' => 'Júlio Cesar', 'email' => 'julio.cesar@empresa.com'],
            ['name' => 'Adriana Azevedo', 'email' => 'adriana.azevedo@empresa.com'],
            ['name' => 'Cíntia Ramos', 'email' => 'cintia.ramos@empresa.com'],
            ['name' => 'Paulo César', 'email' => 'paulo.cesar@empresa.com'],
            ['name' => 'Rosana Carvalho', 'email' => 'rosana.carvalho@empresa.com'],
            ['name' => 'Washington Sousa', 'email' => 'washington.sousa@empresa.com'],
            ['name' => 'Karina Nogueira', 'email' => 'karina.nogueira@empresa.com'],
            ['name' => 'Evandro Dias', 'email' => 'evandro.dias@empresa.com'],
            ['name' => 'Sabrina Costa', 'email' => 'sabrina.costa@empresa.com'],
            ['name' => 'Otávio Fernandes', 'email' => 'otavio.fernandes@empresa.com'],
            ['name' => 'Bruna Mendonça', 'email' => 'bruna.mendonca@empresa.com'],
            ['name' => 'Flávio Santos', 'email' => 'flavio.santos@empresa.com'],
            ['name' => 'Alessandra Pereira', 'email' => 'alessandra.pereira@empresa.com'],
            ['name' => 'Ivan Prado', 'email' => 'ivan.prado@empresa.com'],
            ['name' => 'Janaina Almeida', 'email' => 'janaina.almeida@empresa.com'],
            ['name' => 'Rubens Cardoso', 'email' => 'rubens.cardoso@empresa.com'],
            ['name' => 'Denise Ferreira', 'email' => 'denise.ferreira@empresa.com'],
            ['name' => 'Nelson Gomes', 'email' => 'nelson.gomes@empresa.com'],
            ['name' => 'Silvana Rocha', 'email' => 'silvana.rocha@empresa.com'],
            ['name' => 'César Augusto', 'email' => 'cesar.augusto@empresa.com'],
            ['name' => 'Lúcia Helena', 'email' => 'lucia.helena@empresa.com'],
            ['name' => 'Reginaldo Silva', 'email' => 'reginaldo.silva@empresa.com'],
            ['name' => 'Fátima Oliveira', 'email' => 'fatima.oliveira@empresa.com'],
            ['name' => 'Edson Lima', 'email' => 'edson.lima@empresa.com'],
            ['name' => 'Regina Souza', 'email' => 'regina.souza@empresa.com'],
            ['name' => 'Jorge Henrique', 'email' => 'jorge.henrique@empresa.com'],
            ['name' => 'Vera Lúcia', 'email' => 'vera.lucia@empresa.com'],
            ['name' => 'Antônio Carlos', 'email' => 'antonio.carlos@empresa.com'],
            ['name' => 'Marta Silva', 'email' => 'marta.silva@empresa.com'],
            ['name' => 'Francisco José', 'email' => 'francisco.jose@empresa.com'],
            ['name' => 'Célia Regina', 'email' => 'celia.regina@empresa.com'],
            ['name' => 'José Roberto', 'email' => 'jose.roberto@empresa.com'],
            ['name' => 'Solange Maria', 'email' => 'solange.maria@empresa.com'],
            ['name' => 'Gilberto Neto', 'email' => 'gilberto.neto@empresa.com'],
            ['name' => 'Eliana Cristina', 'email' => 'eliana.cristina@empresa.com'],
            ['name' => 'Wanderson Souza', 'email' => 'wanderson.souza@empresa.com'],
            ['name' => 'Cristiane Alves', 'email' => 'cristiane.alves@empresa.com'],
            ['name' => 'Rogério Mendes', 'email' => 'rogerio.mendes@empresa.com'],
            ['name' => 'Luiza Fernanda', 'email' => 'luiza.fernanda@empresa.com'],
            ['name' => 'Everton Costa', 'email' => 'everton.costa@empresa.com'],
            ['name' => 'Jaqueline Santos', 'email' => 'jaqueline.santos@empresa.com'],
            ['name' => 'Valter Oliveira', 'email' => 'valter.oliveira@empresa.com'],
            ['name' => 'Elizabete Lima', 'email' => 'elizabete.lima@empresa.com'],
            ['name' => 'Jeferson Almeida', 'email' => 'jeferson.almeida@empresa.com'],
            ['name' => 'Sônia Maria', 'email' => 'sonia.maria@empresa.com'],
            ['name' => 'Marcos Paulo', 'email' => 'marcos.paulo@empresa.com'],
            ['name' => 'Valéria Cristina', 'email' => 'valeria.cristina@empresa.com'],
            ['name' => 'Hamilton Silva', 'email' => 'hamilton.silva@empresa.com'],
            ['name' => 'Rosângela Pereira', 'email' => 'rosangela.pereira@empresa.com'],
            ['name' => 'Milton César', 'email' => 'milton.cesar@empresa.com'],
            ['name' => 'Marlene Santos', 'email' => 'marlene.santos@empresa.com'],
            ['name' => 'Wilson Roberto', 'email' => 'wilson.roberto@empresa.com'],
            ['name' => 'Aparecida Silva', 'email' => 'aparecida.silva@empresa.com'],
            ['name' => 'Kleber Oliveira', 'email' => 'kleber.oliveira@empresa.com'],
            ['name' => 'Margarete Costa', 'email' => 'margarete.costa@empresa.com'],
            ['name' => 'Giovanni Ferreira', 'email' => 'giovanni.ferreira@empresa.com'],
            ['name' => 'Liliane Rodrigues', 'email' => 'liliane.rodrigues@empresa.com'],
            ['name' => 'Cleber Souza', 'email' => 'cleber.souza@empresa.com'],
            ['name' => 'Patrícia Lúcia', 'email' => 'patricia.lucia@empresa.com'],
            ['name' => 'Anderson Lima', 'email' => 'anderson.lima@empresa.com'],
            ['name' => 'Michele Santos', 'email' => 'michele.santos@empresa.com'],
            ['name' => 'Wellington Alves', 'email' => 'wellington.alves@empresa.com'],
            ['name' => 'Francine Oliveira', 'email' => 'francine.oliveira@empresa.com'],
            ['name' => 'Edimar Costa', 'email' => 'edimar.costa@empresa.com'],
            ['name' => 'Elza Maria', 'email' => 'elza.maria@empresa.com'],
            ['name' => 'Claudio Roberto', 'email' => 'claudio.roberto@empresa.com'],
            ['name' => 'Marilene Silva', 'email' => 'marilene.silva@empresa.com'],
            ['name' => 'Valdir Pereira', 'email' => 'valdir.pereira@empresa.com'],
            ['name' => 'Neusa Santos', 'email' => 'neusa.santos@empresa.com'],
            ['name' => 'João Paulo', 'email' => 'joao.paulo@empresa.com'],
            ['name' => 'Maria José', 'email' => 'maria.jose@empresa.com'],
        ];

        foreach ($collaborators as $index => $collaborator) {
            // Fazer nomes únicos adicionando número se necessário
            $uniqueName = $collaborator['name'];
            if ($index > 0) {
                // Para evitar duplicatas, adicionar número aos nomes após o primeiro de cada nome
                $nameCount = 0;
                foreach (array_slice($collaborators, 0, $index) as $prevCollab) {
                    if (str_contains($prevCollab['name'], explode(' ', $collaborator['name'])[0])) {
                        $nameCount++;
                    }
                }
                if ($nameCount > 0) {
                    $uniqueName = $collaborator['name'] . ' ' . ($nameCount + 1);
                }
            }
            // Formatar CPF corretamente (###.###.###-##)
            $cpfNumbers = $faker->unique()->cpf(false);
            $formattedCpf = substr($cpfNumbers, 0, 3) . '.' .
                           substr($cpfNumbers, 3, 3) . '.' .
                           substr($cpfNumbers, 6, 3) . '-' .
                           substr($cpfNumbers, 9, 2);

            // Formatar telefone (##) #####-####
            $phoneNumbers = $faker->cellphone(false);
            $cleanPhone = preg_replace('/\D/', '', $phoneNumbers);
            $formattedPhone = '(' . substr($cleanPhone, 0, 2) . ') ' .
                             substr($cleanPhone, 2, 5) . '-' .
                             substr($cleanPhone, 7, 4);

            // Formatar CEP #####-###
            $cepNumbers = $faker->postcode;
            $cleanCep = preg_replace('/\D/', '', $cepNumbers);
            $formattedCep = substr($cleanCep, 0, 5) . '-' . substr($cleanCep, 5, 3);

            CollaboratorModel::create([
                'name' => $uniqueName,
                'email' => $collaborator['email'],
                'password' => bcrypt('senha123'),
                'cpf' => $formattedCpf,
                'admission_date' => $faker->dateTimeBetween('-2 years', '-30 days')->format('Y-m-d'),
                'phone' => $formattedPhone,
                'zip_code' => $formattedCep,
                'street' => $faker->streetName,
                'neighborhood' => $faker->randomElement([
                    'Centro', 'Jardim América', 'Vila Nova', 'Bela Vista', 'Santa Cruz',
                    'Jardim Paulista', 'Vila Madalena', 'Moema', 'Ibirapuera', 'Perdizes',
                    'Copacabana', 'Ipanema', 'Leblon', 'Botafogo', 'Flamengo',
                    'Vila Olímpia', 'Itaim Bibi', 'Pinheiros', 'Santana', 'Tijuca'
                ]),
                'number' => $faker->buildingNumber,
                'position_id' => $positions->random()->id,
                'work_hours_id' => $this->selectWorkHours($index, $twoShiftWorkHours, $oneShiftWorkHours, count($collaborators)),
                'status' => $faker->randomElement(['ativo', 'ativo', 'ativo', 'ativo', 'inativo']), // 80% ativo, 20% inativo
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Seleciona jornada de trabalho garantindo diversidade
     * 30% dos colaboradores terão jornadas de 2 turnos se disponíveis
     */
    private function selectWorkHours($index, $twoShiftWorkHours, $oneShiftWorkHours, $totalCount)
    {
        // 30% dos colaboradores devem ter jornadas de 2 turnos
        $shouldHaveTwoShifts = ($index % 10) < 3; // 3 em cada 10 (30%)

        if ($shouldHaveTwoShifts && $twoShiftWorkHours->isNotEmpty()) {
            return $twoShiftWorkHours->random()->id;
        }

        if ($oneShiftWorkHours->isNotEmpty()) {
            return $oneShiftWorkHours->random()->id;
        }

        // Fallback: qualquer jornada disponível
        $allWorkHours = WorkHoursModel::active()->get();
        return $allWorkHours->random()->id;
    }
}
