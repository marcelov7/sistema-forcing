<?php
/**
 * 🔍 VERIFICAÇÃO DE EMAIL - DEPLOY
 * Script para diagnosticar problemas de email em produção
 */

echo "<h1>🔍 VERIFICAÇÃO DE EMAIL - DEPLOY</h1>";

// Verifica se é ambiente Laravel
if (!file_exists('../vendor/autoload.php')) {
    die("❌ Não foi possível encontrar o Laravel. Execute no diretório public/");
}

// Carrega Laravel
require_once '../vendor/autoload.php';
$app = require_once '../bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

echo "<div style='font-family: monospace; background: #f8f9fa; padding: 20px; border-radius: 10px;'>";

// 1. VERIFICAÇÃO DAS CONFIGURAÇÕES
echo "<h2>📋 1. CONFIGURAÇÕES DE EMAIL</h2>";

$mailer = config('mail.default');
$host = config('mail.mailers.smtp.host');
$port = config('mail.mailers.smtp.port');
$encryption = config('mail.mailers.smtp.encryption');
$username = config('mail.mailers.smtp.username');
$password = config('mail.mailers.smtp.password');
$fromAddress = config('mail.from.address');
$fromName = config('mail.from.name');

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Configuração</th><th>Valor</th><th>Status</th></tr>";

// Mailer padrão
$mailerStatus = $mailer === 'smtp' ? '✅' : '❌ Deve ser "smtp"';
echo "<tr><td>MAIL_MAILER</td><td>$mailer</td><td>$mailerStatus</td></tr>";

// Host
$hostStatus = !empty($host) && $host !== '127.0.0.1' ? '✅' : '❌ Host inválido';
echo "<tr><td>MAIL_HOST</td><td>$host</td><td>$hostStatus</td></tr>";

// Porta
$portStatus = !empty($port) && $port != 2525 ? '✅' : '❌ Porta suspeita';
echo "<tr><td>MAIL_PORT</td><td>$port</td><td>$portStatus</td></tr>";

// Encryption
$encryptionStatus = in_array($encryption, ['tls', 'ssl']) ? '✅' : '❌ Deve ser TLS ou SSL';
echo "<tr><td>MAIL_ENCRYPTION</td><td>$encryption</td><td>$encryptionStatus</td></tr>";

// Username
$userStatus = !empty($username) ? '✅' : '❌ Username necessário';
echo "<tr><td>MAIL_USERNAME</td><td>" . (strlen($username) > 3 ? substr($username, 0, 3) . '***' : $username) . "</td><td>$userStatus</td></tr>";

// Password
$passStatus = !empty($password) ? '✅' : '❌ Password necessário';
echo "<tr><td>MAIL_PASSWORD</td><td>" . (!empty($password) ? '***configurado***' : 'VAZIO') . "</td><td>$passStatus</td></tr>";

// From Address
$fromStatus = filter_var($fromAddress, FILTER_VALIDATE_EMAIL) ? '✅' : '❌ Email inválido';
echo "<tr><td>MAIL_FROM_ADDRESS</td><td>$fromAddress</td><td>$fromStatus</td></tr>";

// From Name
$nameStatus = !empty($fromName) ? '✅' : '⚠️ Nome recomendado';
echo "<tr><td>MAIL_FROM_NAME</td><td>$fromName</td><td>$nameStatus</td></tr>";

echo "</table>";

// 2. TESTE DE CONEXÃO SMTP
echo "<h2>🔌 2. TESTE DE CONEXÃO SMTP</h2>";

try {
    $transport = new \Symfony\Component\Mailer\Transport\Smtp\EsmtpTransport($host, $port);
    
    if ($encryption === 'tls') {
        $transport->setEncryption('tls');
    } elseif ($encryption === 'ssl') {
        $transport->setEncryption('ssl');
    }
    
    if ($username && $password) {
        $transport->setUsername($username);
        $transport->setPassword($password);
    }
    
    // Tenta conectar
    $transport->start();
    echo "✅ <strong>Conexão SMTP bem-sucedida!</strong><br>";
    echo "📡 Servidor: $host:$port<br>";
    echo "🔒 Encryption: $encryption<br>";
    
} catch (\Exception $e) {
    echo "❌ <strong>Erro na conexão SMTP:</strong><br>";
    echo "🚨 " . $e->getMessage() . "<br>";
    echo "<small>Verifique host, porta, username e password</small><br>";
}

// 3. TESTE DE ENVIO
echo "<h2>📧 3. TESTE DE ENVIO</h2>";

try {
    // Busca um usuário admin para teste
    $adminUser = \App\Models\User::where('perfil', 'admin')->first();
    
    if ($adminUser) {
        echo "🎯 Enviando email de teste para: {$adminUser->email}<br>";
        
        \Illuminate\Support\Facades\Mail::raw(
            "🧪 TESTE DE EMAIL - DEPLOY\n\nEste é um email de teste do sistema de forcing.\n\nData: " . now()->format('d/m/Y H:i:s') . "\nServidor: " . $_SERVER['SERVER_NAME'] ?? 'N/A',
            function ($message) use ($adminUser) {
                $message->to($adminUser->email)
                        ->subject('🧪 Teste de Email - Sistema de Forcing');
            }
        );
        
        echo "✅ <strong>Email de teste enviado com sucesso!</strong><br>";
        echo "📬 Verifique a caixa de entrada de: {$adminUser->email}<br>";
        
    } else {
        echo "❌ Não foi possível encontrar um usuário admin para teste<br>";
    }
    
} catch (\Exception $e) {
    echo "❌ <strong>Erro ao enviar email de teste:</strong><br>";
    echo "🚨 " . $e->getMessage() . "<br>";
}

// 4. VERIFICAÇÃO DE LOGS
echo "<h2>📜 4. ÚLTIMOS LOGS DE EMAIL</h2>";

$logPath = storage_path('logs/laravel.log');
if (file_exists($logPath)) {
    $logContent = file_get_contents($logPath);
    $emailLogs = [];
    
    // Procura por logs relacionados a email
    $lines = explode("\n", $logContent);
    $emailKeywords = ['mail', 'email', 'smtp', 'notification', 'forcing'];
    
    foreach (array_reverse($lines) as $line) {
        foreach ($emailKeywords as $keyword) {
            if (stripos($line, $keyword) !== false && count($emailLogs) < 10) {
                $emailLogs[] = $line;
                break;
            }
        }
    }
    
    if (!empty($emailLogs)) {
        echo "<div style='background: #000; color: #0f0; padding: 10px; border-radius: 5px; max-height: 300px; overflow-y: auto;'>";
        foreach (array_slice($emailLogs, 0, 10) as $log) {
            echo htmlspecialchars($log) . "<br>";
        }
        echo "</div>";
    } else {
        echo "ℹ️ Nenhum log relacionado a email encontrado nos últimos registros<br>";
    }
} else {
    echo "❌ Arquivo de log não encontrado: $logPath<br>";
}

// 5. VARIÁVEIS DE AMBIENTE
echo "<h2>🌍 5. VARIÁVEIS DE AMBIENTE (.env)</h2>";

$envVars = [
    'MAIL_MAILER',
    'MAIL_HOST', 
    'MAIL_PORT',
    'MAIL_USERNAME',
    'MAIL_PASSWORD',
    'MAIL_ENCRYPTION',
    'MAIL_FROM_ADDRESS',
    'MAIL_FROM_NAME'
];

echo "<table border='1' style='border-collapse: collapse; width: 100%;'>";
echo "<tr><th>Variável</th><th>Definida</th><th>Valor</th></tr>";

foreach ($envVars as $var) {
    $value = env($var);
    $defined = $value !== null ? '✅' : '❌';
    $displayValue = $value;
    
    // Mascarar senha
    if ($var === 'MAIL_PASSWORD' && $value) {
        $displayValue = '***configurado***';
    } elseif (in_array($var, ['MAIL_USERNAME']) && $value) {
        $displayValue = strlen($value) > 3 ? substr($value, 0, 3) . '***' : $value;
    }
    
    echo "<tr><td>$var</td><td>$defined</td><td>$displayValue</td></tr>";
}

echo "</table>";

// 6. RECOMENDAÇÕES
echo "<h2>💡 6. RECOMENDAÇÕES</h2>";

$issues = [];

if ($mailer !== 'smtp') {
    $issues[] = "Alterar MAIL_MAILER para 'smtp' no .env";
}

if (empty($host) || $host === '127.0.0.1') {
    $issues[] = "Configurar MAIL_HOST com servidor SMTP válido";
}

if (empty($username) || empty($password)) {
    $issues[] = "Configurar MAIL_USERNAME e MAIL_PASSWORD";
}

if (!in_array($encryption, ['tls', 'ssl'])) {
    $issues[] = "Configurar MAIL_ENCRYPTION como 'tls' ou 'ssl'";
}

if (!filter_var($fromAddress, FILTER_VALIDATE_EMAIL)) {
    $issues[] = "Configurar MAIL_FROM_ADDRESS com email válido";
}

if (!empty($issues)) {
    echo "<div style='background: #fff3cd; padding: 15px; border: 1px solid #ffc107; border-radius: 5px;'>";
    echo "<strong>⚠️ Ações necessárias:</strong><br>";
    foreach ($issues as $issue) {
        echo "• " . $issue . "<br>";
    }
    echo "</div>";
} else {
    echo "<div style='background: #d4edda; padding: 15px; border: 1px solid #28a745; border-radius: 5px;'>";
    echo "✅ <strong>Configurações parecem corretas!</strong><br>";
    echo "Se ainda há problemas, verifique logs do servidor ou entre em contato com o provedor de hospedagem.";
    echo "</div>";
}

echo "<hr>";
echo "<p><strong>📅 Verificação realizada em:</strong> " . now()->format('d/m/Y H:i:s') . "</p>";
echo "<p><strong>🌐 Servidor:</strong> " . ($_SERVER['SERVER_NAME'] ?? 'N/A') . "</p>";
echo "<p><strong>🐘 PHP:</strong> " . PHP_VERSION . "</p>";
echo "<p><strong>🔧 Laravel:</strong> " . app()->version() . "</p>";

echo "</div>";
?>

<style>
body { font-family: Arial, sans-serif; margin: 20px; background: #f8f9fa; }
h1, h2 { color: #333; }
table { margin: 10px 0; }
th { background: #007bff; color: white; padding: 8px; }
td { padding: 8px; background: white; }
</style> 