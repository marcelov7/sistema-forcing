#!/bin/bash

# 🚀 Script de Deploy CloudPanel - Sistema de Forcing
# Este script automatiza o processo de deploy no CloudPanel

echo "🚀 Iniciando deploy do Sistema de Forcing no CloudPanel..."

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Função para log colorido
log() {
    echo -e "${GREEN}[$(date +'%Y-%m-%d %H:%M:%S')] $1${NC}"
}

error() {
    echo -e "${RED}[ERROR] $1${NC}"
}

warning() {
    echo -e "${YELLOW}[WARNING] $1${NC}"
}

info() {
    echo -e "${BLUE}[INFO] $1${NC}"
}

# Verificar se está no diretório correto
if [ ! -f "composer.json" ]; then
    error "Execute este script no diretório raiz do projeto Laravel"
    exit 1
fi

log "📋 Verificando pré-requisitos..."

# Verificar se o arquivo .env existe
if [ ! -f ".env" ]; then
    error "Arquivo .env não encontrado. Configure o ambiente primeiro."
    exit 1
fi

# Verificar se as dependências estão instaladas
if [ ! -d "vendor" ]; then
    log "📦 Instalando dependências do Composer..."
    composer install --no-dev --optimize-autoloader
    if [ $? -ne 0 ]; then
        error "Falha ao instalar dependências"
        exit 1
    fi
fi

log "🔧 Configurando aplicação..."

# Gerar chave da aplicação se não existir
if ! grep -q "APP_KEY=" .env || grep -q "APP_KEY=$" .env; then
    log "🔑 Gerando chave da aplicação..."
    php artisan key:generate
fi

# Limpar cache
log "🧹 Limpando cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan cache:clear

# Otimizar para produção
log "⚡ Otimizando para produção..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Verificar banco de dados
log "🗄️ Verificando banco de dados..."
php artisan migrate --force

# Verificar se as tabelas foram criadas
if ! php artisan tinker --execute="echo 'DB OK'; exit;" 2>/dev/null; then
    error "Erro na conexão com o banco de dados. Verifique as configurações no .env"
    exit 1
fi

# Verificar permissões
log "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# Verificar se o JWT está configurado
if ! grep -q "JWT_SECRET=" .env; then
    log "🔐 Configurando JWT..."
    php artisan jwt:secret --force
fi

# Testar API
log "🧪 Testando API..."
if curl -s -o /dev/null -w "%{http_code}" http://localhost/api/health | grep -q "200"; then
    log "✅ API funcionando corretamente"
else
    warning "⚠️ API não está respondendo. Verifique a configuração do servidor web."
fi

# Verificar se as rotas da API estão registradas
log "🛣️ Verificando rotas da API..."
php artisan route:list --path=api

log "✅ Deploy concluído com sucesso!"
log ""
log "📱 Para configurar a aplicação mobile:"
log "1. Atualize a URL da API no arquivo mobile-app/src/services/api.ts"
log "2. Execute 'npm install' na pasta mobile-app/"
log "3. Execute 'expo start' para iniciar o app mobile"
log ""
log "🌐 URLs importantes:"
log "- API Health: https://seu-dominio.com/api/health"
log "- API Login: https://seu-dominio.com/api/v1/auth/login"
log "- Dashboard: https://seu-dominio.com"
log ""
log "📊 Próximos passos:"
log "1. Configure SSL no CloudPanel"
log "2. Teste todas as funcionalidades"
log "3. Configure backup automático"
log "4. Monitore logs em storage/logs/laravel.log"

echo ""
info "🎉 Sistema de Forcing pronto para uso!"