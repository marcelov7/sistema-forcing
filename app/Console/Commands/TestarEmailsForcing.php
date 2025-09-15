<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\User;
use App\Models\Forcing;
use App\Services\ForcingNotificationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

class TestarEmailsForcing extends Command
{
    protected $signature = 'forcing:testar-emails {--tipo=all : Tipo de teste (all, config, smtp, envio, fluxo)}';
    protected $description = 'Testa o sistema de emails do forcing';

    public function handle()
    {
        $tipo = $this->option('tipo');
        
        $this->info('🧪 TESTE DE EMAILS - SISTEMA DE FORCING');
        $this->info('==========================================');
        
        switch ($tipo) {
            case 'config':
                $this->testarConfiguracoes();
                break;
            case 'smtp':
                $this->testarConexaoSMTP();
                break;
            case 'envio':
                $this->testarEnvioSimples();
                break;
            case 'fluxo':
                $this->testarFluxoCompleto();
                break;
            default:
                $this->testarTudo();
        }
    }
    
    private function testarTudo()
    {
        $this->testarConfiguracoes();
        $this->line('');
        $this->testarConexaoSMTP();
        $this->line('');
        $this->testarEnvioSimples();
        $this->line('');
        $this->testarFluxoCompleto();
    }
    
    private function testarConfiguracoes()
    {
        $this->info('📋 1. VERIFICANDO CONFIGURAÇÕES');
        
        $configs = [
            'MAIL_MAILER' => config('mail.default'),
            'MAIL_HOST' => config('mail.mailers.smtp.host'),
            'MAIL_PORT' => config('mail.mailers.smtp.port'),
            'MAIL_ENCRYPTION' => config('mail.mailers.smtp.encryption'),
            'MAIL_USERNAME' => config('mail.mailers.smtp.username'),
            'MAIL_PASSWORD' => config('mail.mailers.smtp.password') ? '***configurado***' : 'NÃO CONFIGURADO',
            'MAIL_FROM_ADDRESS' => config('mail.from.address'),
            'MAIL_FROM_NAME' => config('mail.from.name'),
        ];
        
        foreach ($configs as $key => $value) {
            $status = $this->avaliarConfiguracao($key, $value);
            $this->line("  {$status} {$key}: {$value}");
        }
    }
    
    private function avaliarConfiguracao($key, $value)
    {
        switch ($key) {
            case 'MAIL_MAILER':
                return $value === 'smtp' ? '✅' : '❌';
            case 'MAIL_HOST':
                return !empty($value) && $value !== '127.0.0.1' ? '✅' : '❌';
            case 'MAIL_PORT':
                return in_array($value, [587, 465, 25]) ? '✅' : '⚠️';
            case 'MAIL_ENCRYPTION':
                return in_array($value, ['tls', 'ssl']) ? '✅' : '❌';
            case 'MAIL_FROM_ADDRESS':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ? '✅' : '❌';
            default:
                return !empty($value) ? '✅' : '❌';
        }
    }
    
    private function testarConexaoSMTP()
    {
        $this->info('🔌 2. TESTANDO CONEXÃO SMTP');
        
        try {
            $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport(
                config('mail.mailers.smtp.host'),
                config('mail.mailers.smtp.port')
            );
            
            $encryption = config('mail.mailers.smtp.encryption');
            if ($encryption) {
                $transport->setEncryption($encryption);
            }
            
            $username = config('mail.mailers.smtp.username');
            $password = config('mail.mailers.smtp.password');
            
            if ($username && $password) {
                $transport->setUsername($username);
                $transport->setPassword($password);
            }
            
            $transport->start();
            $this->info('  ✅ Conexão SMTP bem-sucedida!');
            
        } catch (\Exception $e) {
            $this->error('  ❌ Erro na conexão SMTP: ' . $e->getMessage());
        }
    }
    
    private function testarEnvioSimples()
    {
        $this->info('📧 3. TESTE DE ENVIO SIMPLES');
        
        $adminUser = User::where('perfil', 'admin')->first();
        
        if (!$adminUser) {
            $this->error('  ❌ Nenhum usuário admin encontrado para teste');
            return;
        }
        
        try {
            Mail::raw(
                "🧪 TESTE DE EMAIL - SISTEMA DE FORCING\n\n" .
                "Este é um email de teste do sistema.\n\n" .
                "Data: " . now()->format('d/m/Y H:i:s') . "\n" .
                "Servidor: " . config('app.url') . "\n" .
                "Ambiente: " . config('app.env'),
                function ($message) use ($adminUser) {
                    $message->to($adminUser->email)
                            ->subject('🧪 Teste de Email - Sistema de Forcing');
                }
            );
            
            $this->info("  ✅ Email de teste enviado para: {$adminUser->email}");
            
        } catch (\Exception $e) {
            $this->error('  ❌ Erro ao enviar email: ' . $e->getMessage());
        }
    }
    
    private function testarFluxoCompleto()
    {
        $this->info('🔄 4. TESTE DO FLUXO COMPLETO');
        
        // Verificar se há forcings para teste
        $forcing = Forcing::first();
        
        if (!$forcing) {
            $this->warn('  ⚠️ Nenhum forcing encontrado para teste do fluxo');
            return;
        }
        
        $this->info("  🎯 Testando com forcing: {$forcing->tag}");
        
        // Instanciar serviço
        $notificationService = app(ForcingNotificationService::class);
        
        // Buscar usuários para teste
        $liberadores = User::where('perfil', 'liberador')->orWhere('perfil', 'admin')->get();
        $executantes = User::where('perfil', 'executante')->orWhere('perfil', 'admin')->get();
        
        $this->info("  👥 Liberadores encontrados: " . $liberadores->count());
        $this->info("  👥 Executantes encontrados: " . $executantes->count());
        
        if ($liberadores->count() === 0) {
            $this->warn('  ⚠️ Nenhum liberador encontrado');
        }
        
        if ($executantes->count() === 0) {
            $this->warn('  ⚠️ Nenhum executante encontrado');
        }
        
        // Testar notificação de forcing criado
        try {
            $notificationService->notificarForcingCriado($forcing);
            $this->info('  ✅ Teste de "Forcing Criado" executado');
        } catch (\Exception $e) {
            $this->error('  ❌ Erro em "Forcing Criado": ' . $e->getMessage());
        }
        
        // Testar notificação de forcing liberado
        if ($forcing->liberador_id) {
            try {
                $notificationService->notificarForcingLiberado($forcing);
                $this->info('  ✅ Teste de "Forcing Liberado" executado');
            } catch (\Exception $e) {
                $this->error('  ❌ Erro em "Forcing Liberado": ' . $e->getMessage());
            }
        }
        
        // Testar notificação de forcing executado
        if ($forcing->executante_id) {
            try {
                $notificationService->notificarForcingExecutado($forcing);
                $this->info('  ✅ Teste de "Forcing Executado" executado');
            } catch (\Exception $e) {
                $this->error('  ❌ Erro em "Forcing Executado": ' . $e->getMessage());
            }
        }
        
        $this->line('');
        $this->info('💡 DICAS:');
        $this->line('  • Verifique as caixas de entrada dos usuários');
        $this->line('  • Confira a pasta de spam/lixo eletrônico');
        $this->line('  • Verifique os logs em storage/logs/laravel.log');
    }
} 