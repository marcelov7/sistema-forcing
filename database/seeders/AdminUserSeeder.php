<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Criar usuário administrador
        User::create([
            'name' => 'Administrador do Sistema',
            'email' => 'admin@forcing.com',
            'username' => 'admin',
            'password' => Hash::make('admin123'),
            'empresa' => 'Sistema',
            'setor' => 'TI',
            'perfil' => 'admin',
        ]);

        // Criar usuário liberador de exemplo
        User::create([
            'name' => 'João Silva Liberador',
            'email' => 'liberador@forcing.com',
            'username' => 'liberador',
            'password' => Hash::make('liberador123'),
            'empresa' => 'Empresa Exemplo',
            'setor' => 'Operações',
            'perfil' => 'liberador',
        ]);

        // Criar usuário executante de exemplo
        User::create([
            'name' => 'Carlos Oliveira Executante',
            'email' => 'executante@forcing.com',
            'username' => 'executante',
            'password' => Hash::make('executante123'),
            'empresa' => 'Empresa Exemplo',
            'setor' => 'Manutenção',
            'perfil' => 'executante',
        ]);

        // Criar usuário comum de exemplo
        User::create([
            'name' => 'Maria Santos',
            'email' => 'usuario@forcing.com',
            'username' => 'usuario',
            'password' => Hash::make('usuario123'),
            'empresa' => 'Empresa Exemplo',
            'setor' => 'Produção',
            'perfil' => 'user',
        ]);
    }
}
