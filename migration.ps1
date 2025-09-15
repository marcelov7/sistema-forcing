# Script de Migração PowerShell para CloudPanel
# Execução: .\migration.ps1

Write-Host "🚀 Iniciando migração do sistema Forcing..." -ForegroundColor Green

# Configurações
$remoteHost = "31.97.168.137"
$remoteUser = "root"
$remotePath = "/home/forcing/htdocs/forcing.digitalinnovation.com.br"
$localPath = "C:\xampp\htdocs\Forcing"

# Função para executar comando SSH
function Invoke-SSHCommand {
    param($command)
    Write-Host "Executando: $command" -ForegroundColor Yellow
    ssh "$remoteUser@$remoteHost" $command
}

# Função para copiar arquivo via SCP
function Copy-ToServer {
    param($localFile, $remoteFile)
    Write-Host "Copiando: $localFile -> $remoteFile" -ForegroundColor Cyan
    scp -r "$localFile" "$remoteUser@${remoteHost}:$remoteFile"
}

try {
    # 1. Criar backup no servidor
    Write-Host "`n📦 Criando backup no servidor..." -ForegroundColor Blue
    $backupDate = Get-Date -Format "yyyyMMdd_HHmmss"
    Invoke-SSHCommand "cd $remotePath && mkdir -p backups/$backupDate && cp -r * backups/$backupDate/ 2>/dev/null || true"

    # 2. Copiar middleware otimizado
    Write-Host "`n🔧 Enviando middleware otimizado..." -ForegroundColor Blue
    Copy-ToServer "$localPath\app\Http\Middleware\CheckProfile.php" "$remotePath/app/Http/Middleware/"
    Copy-ToServer "$localPath\app\Http\Middleware\SuperAdminMiddleware.php" "$remotePath/app/Http/Middleware/"

    # 3. Copiar services otimizados
    Write-Host "`n⚙️ Enviando services otimizados..." -ForegroundColor Blue
    Copy-ToServer "$localPath\app\Services\OptimizedForcingNotificationService.php" "$remotePath/app/Services/"

    # 4. Copiar controllers otimizados
    Write-Host "`n🎮 Enviando controllers otimizados..." -ForegroundColor Blue
    Copy-ToServer "$localPath\app\Http\Controllers\ForcingController.php" "$remotePath/app/Http/Controllers/"
    Copy-ToServer "$localPath\app\Http\Controllers\HomeController.php" "$remotePath/app/Http/Controllers/"

    # 5. Copiar sistema de acessibilidade
    Write-Host "`n🎨 Enviando sistema de acessibilidade..." -ForegroundColor Blue
    Copy-ToServer "$localPath\public\css\colorblind-accessibility.css" "$remotePath/public/css/"
    Copy-ToServer "$localPath\public\js\colorblind-simple.js" "$remotePath/public/js/"

    # 6. Copiar layout atualizado
    Write-Host "`n📱 Enviando layout atualizado..." -ForegroundColor Blue
    Copy-ToServer "$localPath\resources\views\layouts\app.blade.php" "$remotePath/resources/views/layouts/"

    # 7. Copiar configurações
    Write-Host "`n⚙️ Enviando configurações..." -ForegroundColor Blue
    Copy-ToServer "$localPath\.env" "$remotePath/"

    # 8. Executar comandos pós-migração
    Write-Host "`n🔄 Executando comandos pós-migração..." -ForegroundColor Blue
    
    $postMigrationCommands = @"
cd $remotePath
php artisan config:cache
php artisan route:cache  
php artisan view:cache
chown -R forcing:forcing .
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache
systemctl restart nginx
systemctl restart php8.2-fpm
"@

    Invoke-SSHCommand $postMigrationCommands

    # 9. Verificar status
    Write-Host "`n✅ Verificando status do sistema..." -ForegroundColor Blue
    Invoke-SSHCommand "cd $remotePath && php artisan --version && nginx -t"

    Write-Host "`n🎉 Migração concluída com sucesso!" -ForegroundColor Green
    Write-Host "`n📋 Resumo das atualizações:" -ForegroundColor White
    Write-Host "   ✅ Sistema de acessibilidade para daltonismo" -ForegroundColor Green
    Write-Host "   ✅ Middleware CheckProfile otimizado" -ForegroundColor Green
    Write-Host "   ✅ Services de notificação otimizados" -ForegroundColor Green
    Write-Host "   ✅ Controllers refatorados" -ForegroundColor Green
    Write-Host "   ✅ Configurações do Brasil (timezone/locale)" -ForegroundColor Green
    Write-Host "   ✅ Interface moderna e responsiva" -ForegroundColor Green
    Write-Host "`n🌐 Sistema disponível em: https://forcing.digitalinnovation.com.br" -ForegroundColor Cyan

} catch {
    Write-Host "❌ Erro durante a migração: $($_.Exception.Message)" -ForegroundColor Red
    exit 1
}
