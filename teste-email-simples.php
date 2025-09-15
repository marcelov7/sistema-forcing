<?php
// TESTE DE EMAIL SIMPLES - DEPLOY
echo "🧪 TESTE DE EMAIL - DEPLOY CloudPanel\n";
echo "=====================================\n";

// Incluir Laravel
require_once 'vendor/autoload.php';
$app = require_once 'bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// 1. Verificar configurações
echo "📋 CONFIGURAÇÕES:\n";
echo "MAIL_MAILER: " . config('mail.default') . "\n";
echo "MAIL_HOST: " . config('mail.mailers.smtp.host') . "\n";
echo "MAIL_PORT: " . config('mail.mailers.smtp.port') . "\n";
echo "MAIL_ENCRYPTION: " . config('mail.mailers.smtp.encryption') . "\n";
echo "MAIL_USERNAME: " . config('mail.mailers.smtp.username') . "\n";
echo "MAIL_PASSWORD: " . (config('mail.mailers.smtp.password') ? 'CONFIGURADO' : 'VAZIO') . "\n";
echo "MAIL_FROM: " . config('mail.from.address') . "\n\n";

// 2. Testar envio direto
echo "📧 TENTANDO ENVIAR EMAIL DE TESTE...\n";

try {
    // Buscar um usuário admin
    $admin = \App\Models\User::where('perfil', 'admin')->first();
    
    if (!$admin) {
        echo "❌ Nenhum usuário admin encontrado\n";
        exit;
    }
    
    echo "🎯 Enviando para: {$admin->email}\n";
    
    // Enviar email
    \Illuminate\Support\Facades\Mail::raw(
        "🧪 TESTE DE EMAIL - DEPLOY\n\n" .
        "Este email foi enviado do servidor de produção.\n\n" .
        "Data: " . now()->format('d/m/Y H:i:s') . "\n" .
        "Servidor: CloudPanel\n" .
        "Host: " . gethostname() . "\n" .
        "IP: " . ($_SERVER['SERVER_ADDR'] ?? 'N/A'),
        function ($message) use ($admin) {
            $message->to($admin->email)
                    ->subject('🧪 Teste de Email - Deploy CloudPanel');
        }
    );
    
    echo "✅ EMAIL ENVIADO COM SUCESSO!\n";
    echo "📬 Verifique a caixa de entrada de: {$admin->email}\n";
    echo "📋 Não esqueça de verificar a pasta SPAM\n";
    
} catch (\Exception $e) {
    echo "❌ ERRO AO ENVIAR EMAIL:\n";
    echo "🚨 " . $e->getMessage() . "\n";
    echo "🔍 Linha: " . $e->getLine() . "\n";
    echo "📄 Arquivo: " . $e->getFile() . "\n\n";
    
    echo "💡 POSSÍVEIS CAUSAS:\n";
    echo "• Conta sistema@devaxis.com.br não existe no cPanel\n";
    echo "• Senha incorreta\n";
    echo "• Porta 465 bloqueada (tente 587)\n";
    echo "• Servidor bloqueando SMTP externo\n";
}

echo "\n🔧 PRÓXIMOS PASSOS:\n";
echo "1. Verifique se a conta sistema@devaxis.com.br existe no cPanel\n";
echo "2. Confirme a senha no painel da Hostinger\n";
echo "3. Tente trocar para MAIL_PORT=587 e MAIL_ENCRYPTION=tls\n";
echo "4. Entre em contato com suporte Hostinger se necessário\n";
?> 