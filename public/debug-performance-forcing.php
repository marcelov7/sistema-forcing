<?php
require __DIR__.'/../vendor/autoload.php';
$app = require_once __DIR__.'/../bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\User;
use App\Models\Forcing;
use App\Services\ForcingNotificationService;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;

echo "<h1>Debug - Performance na Criação de Forcings</h1>";

echo "<h2>1. Configuração de Email Atual:</h2>";
echo "<ul>";
echo "<li><strong>MAIL_MAILER:</strong> " . env('MAIL_MAILER', 'log') . "</li>";
echo "<li><strong>MAIL_HOST:</strong> " . env('MAIL_HOST', '127.0.0.1') . "</li>";
echo "<li><strong>MAIL_PORT:</strong> " . env('MAIL_PORT', 2525) . "</li>";
echo "<li><strong>MAIL_ENCRYPTION:</strong> " . env('MAIL_ENCRYPTION', 'tls') . "</li>";
echo "<li><strong>MAIL_USERNAME:</strong> " . (env('MAIL_USERNAME') ? 'Configurado' : 'Não configurado') . "</li>";
echo "<li><strong>MAIL_PASSWORD:</strong> " . (env('MAIL_PASSWORD') ? 'Configurado' : 'Não configurado') . "</li>";
echo "</ul>";

echo "<h2>2. Contagem de Usuários por Perfil:</h2>";
$liberadores = User::where('perfil', 'liberador')->count();
$admins = User::where('perfil', 'admin')->count();
$executantes = User::where('perfil', 'executante')->count();
$total = $liberadores + $admins;

echo "<ul>";
echo "<li><strong>Liberadores:</strong> {$liberadores}</li>";
echo "<li><strong>Admins:</strong> {$admins}</li>";
echo "<li><strong>Executantes:</strong> {$executantes}</li>";
echo "<li><strong>Total que recebem email de criação:</strong> {$total}</li>";
echo "</ul>";

echo "<h2>3. Teste de Performance - Simulação de Criação:</h2>";

// Simular dados de um forcing
$dadosForcing = [
    'tag' => 'TESTE-PERF-' . time(),
    'situacao_equipamento' => 'desativado',
    'descricao_equipamento' => 'Teste de performance',
    'area' => 'Teste',
    'observacoes' => 'Teste de performance',
    'user_id' => 1,
    'unit_id' => 1,
    'status' => 'pendente',
    'status_execucao' => 'pendente',
    'data_forcing' => now(),
];

echo "<h3>3.1. Teste de Criação do Forcing (sem email):</h3>";
$inicio = microtime(true);

try {
    $forcing = Forcing::create($dadosForcing);
    $tempoCriacao = microtime(true) - $inicio;
    echo "<p style='color: green;'>✅ Forcing criado em " . round($tempoCriacao * 1000, 2) . "ms</p>";
    echo "<p><strong>ID do forcing:</strong> {$forcing->id}</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Erro ao criar forcing: " . $e->getMessage() . "</p>";
    exit;
}

echo "<h3>3.2. Teste de Busca de Liberadores:</h3>";
$inicio = microtime(true);

$liberadores = User::where('perfil', 'liberador')
    ->orWhere('perfil', 'admin')
    ->get();

$tempoBusca = microtime(true) - $inicio;
echo "<p style='color: green;'>✅ Busca de liberadores em " . round($tempoBusca * 1000, 2) . "ms</p>";
echo "<p><strong>Liberadores encontrados:</strong> {$liberadores->count()}</p>";

echo "<h3>3.3. Teste de Envio de Emails (simulado):</h3>";
$inicio = microtime(true);

$notificationService = new ForcingNotificationService();

// Testar apenas o método de notificação
try {
    $notificationService->notificarForcingCriado($forcing);
    $tempoEmail = microtime(true) - $inicio;
    echo "<p style='color: green;'>✅ Notificação enviada em " . round($tempoEmail * 1000, 2) . "ms</p>";
} catch (Exception $e) {
    $tempoEmail = microtime(true) - $inicio;
    echo "<p style='color: red;'>❌ Erro no envio de email em " . round($tempoEmail * 1000, 2) . "ms: " . $e->getMessage() . "</p>";
}

echo "<h3>3.4. Resumo de Performance:</h3>";
echo "<table border='1' style='border-collapse: collapse;'>";
echo "<tr><th>Operação</th><th>Tempo (ms)</th><th>Status</th></tr>";
echo "<tr><td>Criação do Forcing</td><td>" . round($tempoCriacao * 1000, 2) . "</td><td>✅</td></tr>";
echo "<tr><td>Busca de Liberadores</td><td>" . round($tempoBusca * 1000, 2) . "</td><td>✅</td></tr>";
echo "<tr><td>Envio de Emails</td><td>" . round($tempoEmail * 1000, 2) . "</td><td>" . (isset($e) ? '❌' : '✅') . "</td></tr>";
echo "<tr><th>TOTAL</th><th>" . round(($tempoCriacao + $tempoBusca + $tempoEmail) * 1000, 2) . "</th><th>-</th></tr>";
echo "</table>";

echo "<h2>4. Análise de Gargalos:</h2>";
$totalTempo = $tempoCriacao + $tempoBusca + $tempoEmail;

if ($tempoEmail > $tempoCriacao + $tempoBusca) {
    echo "<p style='color: red; font-weight: bold;'>🚨 GARGALO IDENTIFICADO: Envio de emails está causando lentidão!</p>";
    echo "<ul>";
    echo "<li>O envio de emails está demorando " . round($tempoEmail * 1000, 2) . "ms</li>";
    echo "<li>Isso representa " . round(($tempoEmail / $totalTempo) * 100, 1) . "% do tempo total</li>";
    echo "<li>Com {$liberadores->count()} destinatários, cada email demora " . round(($tempoEmail / $liberadores->count()) * 1000, 2) . "ms em média</li>";
    echo "</ul>";
} else {
    echo "<p style='color: green;'>✅ O envio de emails não é o gargalo principal</p>";
}

echo "<h2>5. Recomendações de Otimização:</h2>";
echo "<ul>";
echo "<li><strong>1. Usar Queue para emails:</strong> Mover envio de emails para fila assíncrona</li>";
echo "<li><strong>2. Limitar destinatários:</strong> Enviar apenas para liberadores da mesma unidade</li>";
echo "<li><strong>3. Usar driver 'log':</strong> Para desenvolvimento, usar log em vez de SMTP</li>";
echo "<li><strong>4. Configurar timeout:</strong> Definir timeout menor para SMTP</li>";
echo "<li><strong>5. Usar cache:</strong> Cachear lista de liberadores</li>";
echo "</ul>";

echo "<h2>6. Teste de Configuração de Email:</h2>";
echo "<p>Testando se o email está configurado corretamente...</p>";

try {
    Mail::raw('Teste de configuração de email', function ($message) {
        $message->to('teste@example.com')
                ->subject('Teste de Configuração');
    });
    echo "<p style='color: green;'>✅ Configuração de email OK</p>";
} catch (Exception $e) {
    echo "<p style='color: red;'>❌ Problema na configuração de email: " . $e->getMessage() . "</p>";
}

// Limpar o forcing de teste
$forcing->delete();

echo "<hr>";
echo "<p><strong>Data/Hora:</strong> " . date('d/m/Y H:i:s') . "</p>";
echo "<p><strong>Status:</strong> <span style='color: blue; font-weight: bold;'>ANÁLISE DE PERFORMANCE CONCLUÍDA 📊</span></p>";
?>
