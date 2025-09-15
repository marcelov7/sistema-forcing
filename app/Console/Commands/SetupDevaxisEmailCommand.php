<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class SetupDevaxisEmailCommand extends Command
{
    protected $signature = 'email:setup-devaxis {--test : Testar após configurar}';
    protected $description = 'Configurar e-mail sistema@devaxis.com.br de forma interativa';

    public function handle()
    {
        $this->info('🚀 CONFIGURAÇÃO DO E-MAIL DEVAXIS');
        $this->info('=====================================');
        $this->newLine();

        $this->info('📧 Conta: sistema@devaxis.com.br');
        $this->info('🏢 Domínio: devaxis.com.br');
        $this->info('🔒 Servidor: smtp.hostinger.com:465 (SSL)');
        $this->newLine();

        // Solicitar senha de forma segura
        $password = $this->secret('🔑 Digite a senha da conta sistema@devaxis.com.br');

        if (empty($password)) {
            $this->error('❌ Senha não pode estar vazia!');
            return 1;
        }

        $this->info('📝 Atualizando configurações...');

        // Atualizar .env
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Configurações específicas para Devaxis
        $mailConfig = [
            'MAIL_MAILER' => 'smtp',
            'MAIL_HOST' => 'smtp.hostinger.com',
            'MAIL_PORT' => '465',
            'MAIL_USERNAME' => 'sistema@devaxis.com.br',
            'MAIL_PASSWORD' => $password,
            'MAIL_ENCRYPTION' => 'ssl',
            'MAIL_FROM_ADDRESS' => '"sistema@devaxis.com.br"',
            'MAIL_FROM_NAME' => '"Sistema de Forcing - Devaxis"',
        ];

        // Atualizar cada configuração
        foreach ($mailConfig as $key => $value) {
            if (preg_match("/^{$key}=.*$/m", $envContent)) {
                $envContent = preg_replace(
                    "/^{$key}=.*$/m",
                    "{$key}={$value}",
                    $envContent
                );
                $this->line("  ✓ {$key}");
            } else {
                $envContent .= "\n{$key}={$value}";
                $this->line("  + {$key}");
            }
        }

        // Salvar arquivo
        file_put_contents($envPath, $envContent);
        $this->info('✅ Configuração salva!');

        // Limpar cache
        $this->call('config:cache');
        $this->info('🔄 Cache atualizado');

        // Testar se solicitado
        if ($this->option('test')) {
            $this->newLine();
            $this->info('🧪 Testando configuração...');

            try {
                // Configurar temporariamente
                Config::set('mail.mailers.smtp.host', 'smtp.hostinger.com');
                Config::set('mail.mailers.smtp.port', 465);
                Config::set('mail.mailers.smtp.encryption', 'ssl');
                Config::set('mail.mailers.smtp.username', 'sistema@devaxis.com.br');
                Config::set('mail.mailers.smtp.password', $password);
                Config::set('mail.from.address', 'sistema@devaxis.com.br');
                Config::set('mail.from.name', 'Sistema de Forcing - Devaxis');

                // Enviar e-mail de teste
                Mail::raw('✅ Configuração da Devaxis realizada com sucesso!', function ($message) {
                    $message->to('sistema@devaxis.com.br')
                            ->subject('🔧 Teste de Configuração - Sistema de Forcing Devaxis');
                });

                $this->info('✅ TESTE REALIZADO COM SUCESSO!');
                $this->info('📧 E-mail enviado para: sistema@devaxis.com.br');
                
            } catch (\Exception $e) {
                $this->error('❌ Erro no teste: ' . $e->getMessage());
                
                if (str_contains($e->getMessage(), 'authentication failed')) {
                    $this->warn('🔐 Erro de autenticação - Verifique:');
                    $this->warn('   • A senha está correta?');
                    $this->warn('   • A conta sistema@devaxis.com.br existe?');
                    $this->warn('   • Teste login em: https://webmail.hostinger.com');
                } else {
                    $this->warn('🌐 Erro de conexão - Verifique:');
                    $this->warn('   • Conexão com internet');
                    $this->warn('   • Firewall liberando porta 465');
                    $this->warn('   • DNS resolvendo smtp.hostinger.com');
                }
                return 1;
            }
        }

        $this->newLine();
        $this->info('🎉 CONFIGURAÇÃO DEVAXIS CONCLUÍDA!');
        $this->newLine();
        
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['📧 E-mail', 'sistema@devaxis.com.br'],
                ['🏢 Empresa', 'Devaxis'],
                ['🔒 Servidor', 'smtp.hostinger.com:465'],
                ['🛡️ Criptografia', 'SSL'],
                ['📤 Remetente', 'Sistema de Forcing - Devaxis'],
            ]
        );

        $this->newLine();
        $this->info('💡 Comandos úteis:');
        $this->info('   php artisan email:status    - Ver status');
        $this->info('   php artisan email:test      - Testar envio');

        return 0;
    }
}
