<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::firstOrCreate(
            ['email' => 'lucas@metresistemas.com.br'],
            [
                'name' => 'Administrador',
                'email' => 'admin@metresistemas.com.br',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
            ]
        );

        $this->command->info('✅ Usuário administrador criado com sucesso!');
        $this->command->newLine();
        $this->command->line('🔐 <fg=yellow>CREDENCIAIS DE ACESSO:</fg=yellow>');
        $this->command->line('   Email: admin@metresistemas.com.br');
        $this->command->line('   Senha: password');
        $this->command->newLine();
    }
}
