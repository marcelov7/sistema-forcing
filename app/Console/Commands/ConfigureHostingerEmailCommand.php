<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;

class ConfigureHostingerEmailCommand extends Command
{
    protected $signature = 'email:configure-hostinger 
                            {email : O endereço de e-mail do sistema} 
                            {password : A senha do e-mail} 
                            {domain : O domínio (ex: exemplo.com)}
                            {--test : Testar a configuração após aplicar}';

    protected $description = 'Configurar e-mail da Hostinger automaticamente';

    public function handle()
    {
        $email = $this->argument('email');
        $password = $this->argument('password');
        $domain = $this->argument('domain');
        $test = $this->option('test');

        $this->info('🔧 Configurando e-mail da Hostinger...');

        // Validar formato do e-mail
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $this->error('❌ E-mail inválido: ' . $email);
            return 1;
        }

        // Validar se o e-mail pertence ao domínio
        $emailDomain = substr(strrchr($email, "@"), 1);
        if ($emailDomain !== $domain) {
            $this->error("❌ E-mail deve pertencer ao domínio: {$domain}");
            return 1;
        }

        // Preparar configurações
        $envPath = base_path('.env');
        $envContent = file_get_contents($envPath);

        // Configurações da Hostinger
        $mailConfig = [
            'MAIL_MAILER=smtp',
            'MAIL_HOST=smtp.hostinger.com',
            'MAIL_PORT=465',
            "MAIL_USERNAME={$email}",
            "MAIL_PASSWORD={$password}",
            'MAIL_ENCRYPTION=ssl',
            "MAIL_FROM_ADDRESS=\"{$email}\"",
            "MAIL_FROM_NAME=\"Sistema de Forcing\"",
        ];

        $this->info('📝 Atualizando arquivo .env...');

        // Atualizar cada configuração
        foreach ($mailConfig as $config) {
            [$key, $value] = explode('=', $config, 2);
            
            // Remover aspas do valor para processamento
            $cleanValue = trim($value, '"');
            
            // Verificar se a chave já existe
            if (preg_match("/^{$key}=.*$/m", $envContent)) {
                // Substituir valor existente
                $envContent = preg_replace(
                    "/^{$key}=.*$/m",
                    "{$key}={$value}",
                    $envContent
                );
                $this->line("  ✓ Atualizado: {$key}");
            } else {
                // Adicionar nova linha
                $envContent .= "\n{$key}={$value}";
                $this->line("  + Adicionado: {$key}");
            }
        }

        // Salvar arquivo .env
        file_put_contents($envPath, $envContent);

        $this->info('✅ Configuração salva em .env');

        // Limpar cache de configuração
        $this->call('config:cache');
        $this->info('🔄 Cache de configuração atualizado');

        // Exibir resumo da configuração
        $this->newLine();
        $this->info('📋 RESUMO DA CONFIGURAÇÃO:');
        $this->table(
            ['Configuração', 'Valor'],
            [
                ['Servidor SMTP', 'smtp.hostinger.com'],
                ['Porta', '465'],
                ['Criptografia', 'SSL'],
                ['E-mail', $email],
                ['Domínio', $domain],
                ['Remetente', "Sistema de Forcing"],
            ]
        );

        // Testar configuração se solicitado
        if ($test) {
            $this->newLine();
            $this->info('🧪 Testando configuração...');
            
            try {
                // Configurar temporariamente
                Config::set('mail.mailers.smtp.host', 'smtp.hostinger.com');
                Config::set('mail.mailers.smtp.port', 465);
                Config::set('mail.mailers.smtp.encryption', 'ssl');
                Config::set('mail.mailers.smtp.username', $email);
                Config::set('mail.mailers.smtp.password', $password);
                Config::set('mail.from.address', $email);
                Config::set('mail.from.name', 'Sistema de Forcing');

                // Tentar enviar e-mail de teste
                Mail::raw('Teste de configuração da Hostinger realizado com sucesso!', function ($message) use ($email) {
                    $message->to($email)
                            ->subject('🔧 Teste de Configuração - Sistema de Forcing');
                });

                $this->info('✅ Teste realizado com sucesso!');
                $this->info("📧 E-mail de teste enviado para: {$email}");
                
            } catch (\Exception $e) {
                $this->error('❌ Erro no teste: ' . $e->getMessage());
                $this->warn('💡 Verifique:');
                $this->warn('   - Se o e-mail e senha estão corretos');
                $this->warn('   - Se a conta de e-mail existe na Hostinger');
                $this->warn('   - Se a porta 465 está liberada no firewall');
                return 1;
            }
        }

        $this->newLine();
        $this->info('🎉 Configuração da Hostinger concluída!');
        $this->info('💡 Use os comandos:');
        $this->info('   php artisan email:status    - Ver status dos e-mails');
        $this->info('   php artisan email:test      - Testar envio');
        
        return 0;
    }
}
