<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;

class CreateExecutanteUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-executante';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Cria um usuário executante de exemplo';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Verifica se já existe
        if (User::where('username', 'executante')->exists()) {
            $this->error('Usuário executante já existe!');
            return;
        }

        User::create([
            'name' => 'Carlos Oliveira Executante',
            'email' => 'executante@forcing.com',
            'username' => 'executante',
            'password' => Hash::make('executante123'),
            'empresa' => 'Empresa Exemplo',
            'setor' => 'Manutenção',
            'perfil' => 'executante',
        ]);

        $this->info('Usuário executante criado com sucesso!');
        $this->info('Username: executante');
        $this->info('Senha: executante123');
    }
}
