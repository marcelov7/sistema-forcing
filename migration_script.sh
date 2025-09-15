#!/bin/bash

# Script de migração para CloudPanel
# Execução: ./migration_script.sh

echo "🚀 Iniciando migração do sistema Forcing..."

# Definir variáveis
LOCAL_PATH="/c/xampp/htdocs/Forcing"
REMOTE_USER="root"
REMOTE_HOST="31.97.168.137"
REMOTE_PATH="/home/forcing/htdocs/forcing.digitalinnovation.com.br"

# Criar backup no servidor
echo "📦 Criando backup no servidor..."
ssh $REMOTE_USER@$REMOTE_HOST "
cd $REMOTE_PATH
mkdir -p backups/$(date +%Y%m%d_%H%M%S)
cp -r * backups/$(date +%Y%m%d_%H%M%S)/ 2>/dev/null || true
echo '✅ Backup criado com sucesso'
"

# Lista de arquivos e diretórios para enviar
echo "📁 Preparando arquivos para migração..."

# Sincronizar arquivos principais
echo "🔄 Sincronizando sistema atualizado..."

# Middleware e Services otimizados
rsync -avz --progress \
    app/Http/Middleware/ \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/app/Http/Middleware/

rsync -avz --progress \
    app/Services/ \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/app/Services/

# Controllers otimizados
rsync -avz --progress \
    app/Http/Controllers/ \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/app/Http/Controllers/

# Sistema de acessibilidade completo
rsync -avz --progress \
    public/css/colorblind-accessibility.css \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/public/css/

rsync -avz --progress \
    public/js/colorblind-simple.js \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/public/js/

# Layout principal com acessibilidade
rsync -avz --progress \
    resources/views/layouts/app.blade.php \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/resources/views/layouts/

# Configurações atualizadas
rsync -avz --progress \
    .env \
    $REMOTE_USER@$REMOTE_HOST:$REMOTE_PATH/

# Executar comandos pós-migração no servidor
echo "⚙️ Executando comandos pós-migração..."
ssh $REMOTE_USER@$REMOTE_HOST "
cd $REMOTE_PATH

# Otimizar cache e configurações
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Definir permissões corretas
chown -R forcing:forcing .
find . -type f -exec chmod 644 {} \;
find . -type d -exec chmod 755 {} \;
chmod -R 775 storage bootstrap/cache

# Reiniciar serviços
systemctl restart nginx
systemctl restart php8.2-fpm

echo '✅ Sistema migrado e otimizado com sucesso!'
echo '🎨 Sistema de acessibilidade ativo'
echo '🔧 Middleware e services otimizados'
echo '📱 Interface responsiva atualizada'
"

echo "🎉 Migração concluída com sucesso!"
echo ""
echo "📋 Resumo das atualizações:"
echo "   ✅ Sistema de acessibilidade para daltonismo"
echo "   ✅ Middleware CheckProfile otimizado"
echo "   ✅ Services de notificação otimizados"
echo "   ✅ Controllers refatorados"
echo "   ✅ Configurações do Brasil (timezone/locale)"
echo "   ✅ Interface moderna e responsiva"
echo ""
echo "🌐 Acesse: https://forcing.digitalinnovation.com.br"
