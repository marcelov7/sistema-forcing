<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Unit;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class UsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Buscar as unidades existentes
        $unit1 = Unit::where('code', 'UND001')->first();
        $unit2 = Unit::where('code', 'UND002')->first();
        $unit3 = Unit::where('code', 'UND003')->first();

        // Criar Super Admin
        $superAdmin = User::create([
            'name' => 'Super Administrador',
            'email' => 'superadmin@smc.com.br',
            'username' => 'superadmin',
            'password' => Hash::make('super123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'TI',
            'perfil' => 'admin',
            'is_super_admin' => true,
            'unit_id' => null, // Super Admin não está vinculado a uma unidade específica
            'email_verified_at' => now(),
        ]);

        // Criar usuários para cada unidade
        $admin1 = User::create([
            'name' => 'Admin Central',
            'email' => 'admin.central@smc.com.br',
            'username' => 'admin.central',
            'password' => Hash::make('admin123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Administração',
            'perfil' => 'admin',
            'is_super_admin' => false,
            'unit_id' => $unit1->id,
            'email_verified_at' => now(),
        ]);

        $user1 = User::create([
            'name' => 'Operador Central',
            'email' => 'operador.central@smc.com.br',
            'username' => 'operador.central',
            'password' => Hash::make('user123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Operação',
            'perfil' => 'user',
            'is_super_admin' => false,
            'unit_id' => $unit1->id,
            'email_verified_at' => now(),
        ]);

        $admin2 = User::create([
            'name' => 'Admin Zona Norte',
            'email' => 'admin.zonanorte@smc.com.br',
            'username' => 'admin.zonanorte',
            'password' => Hash::make('admin123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Administração',
            'perfil' => 'admin',
            'is_super_admin' => false,
            'unit_id' => $unit2->id,
            'email_verified_at' => now(),
        ]);

        $user2 = User::create([
            'name' => 'Operador Zona Norte',
            'email' => 'operador.zonanorte@smc.com.br',
            'username' => 'operador.zonanorte',
            'password' => Hash::make('user123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Operação',
            'perfil' => 'user',
            'is_super_admin' => false,
            'unit_id' => $unit2->id,
            'email_verified_at' => now(),
        ]);

        $admin3 = User::create([
            'name' => 'Admin ABC',
            'email' => 'admin.abc@smc.com.br',
            'username' => 'admin.abc',
            'password' => Hash::make('admin123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Administração',
            'perfil' => 'admin',
            'is_super_admin' => false,
            'unit_id' => $unit3->id,
            'email_verified_at' => now(),
        ]);

        $user3 = User::create([
            'name' => 'Operador ABC',
            'email' => 'operador.abc@smc.com.br',
            'username' => 'operador.abc',
            'password' => Hash::make('user123'),
            'empresa' => 'SMC Corporation',
            'setor' => 'Operação',
            'perfil' => 'user',
            'is_super_admin' => false,
            'unit_id' => $unit3->id,
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuários criados com sucesso!');
        $this->command->info('Super Admin: superadmin@smc.com.br / super123');
        $this->command->info('Admins: admin.{unidade}@smc.com.br / admin123');
        $this->command->info('Operadores: operador.{unidade}@smc.com.br / user123');
    }
}
