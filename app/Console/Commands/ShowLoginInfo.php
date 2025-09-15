<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ShowLoginInfo extends Command
{
    protected $signature = 'show:login-info';
    protected $description = 'Mostra informações de login para todos os perfis';

    public function handle()
    {
        $this->info('🔑 CREDENCIAIS DE LOGIN - SISTEMA MULTI-TENANT');
        $this->info('=====================================');
        
        $this->info('');
        $this->comment('🌟 SUPER ADMINISTRADOR (Gerencia TODAS as unidades):');
        $this->line('   Username: superadmin');
        $this->line('   Senha: super123');
        $this->line('   Funcionalidades: Menu "Unidades", vê ALL forcings');
        
        $this->info('');
        $this->comment('👨‍💼 ADMINISTRADOR REGULAR (Gerencia SUA unidade):');
        $this->line('   Username: admin');
        $this->line('   Senha: admin123');
        $this->line('   Funcionalidades: Menu "Usuários", vê forcings da unidade');
        
        $this->info('');
        $this->comment('✅ LIBERADOR:');
        $this->line('   Username: liberador');
        $this->line('   Senha: liberador123');
        
        $this->info('');
        $this->comment('🔧 EXECUTANTE:');
        $this->line('   Username: executante');
        $this->line('   Senha: executante123');
        
        $this->info('');
        $this->comment('👤 USUÁRIO COMUM:');
        $this->line('   Username: usuario');
        $this->line('   Senha: usuario123');
        
        $this->info('');
        $this->warn('🚨 DIFERENÇAS IMPORTANTES:');
        $this->line('• SUPER ADMIN (superadmin): Vê menu "Unidades" + todos os forcings');
        $this->line('• ADMIN REGULAR (admin): Vê menu "Usuários" + forcings da unidade');
        
        $this->info('');
        $this->info('🌐 URL: http://localhost:8000/login');
        
        return 0;
    }
}
