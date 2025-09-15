#!/bin/bash

# Script para atualizar a versão do PWA automaticamente
# Este script deve ser executado antes de cada deploy

# Cores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

echo -e "${BLUE}🔄 Atualizando versão do PWA...${NC}"

# Gera uma nova versão baseada na data/hora
NEW_VERSION="forcing-pwa-v$(date +%Y-%m-%d-%H%M)"
OLD_VERSION=""

# Pega a versão atual do sw.js
if [ -f "public/sw.js" ]; then
    OLD_VERSION=$(grep -o "const CACHE_VERSION = '[^']*'" public/sw.js | sed "s/const CACHE_VERSION = '//" | sed "s/'//")
    echo -e "${YELLOW}📋 Versão atual: ${OLD_VERSION}${NC}"
else
    echo -e "${RED}❌ Arquivo sw.js não encontrado!${NC}"
    exit 1
fi

# Atualiza a versão no sw.js
if [ "$OLD_VERSION" != "$NEW_VERSION" ]; then
    sed -i.bak "s/const CACHE_VERSION = '[^']*'/const CACHE_VERSION = '${NEW_VERSION}'/g" public/sw.js
    rm public/sw.js.bak 2>/dev/null || true
    
    echo -e "${GREEN}✅ Versão atualizada para: ${NEW_VERSION}${NC}"
    echo -e "${GREEN}📝 Cache será limpo automaticamente no próximo deploy${NC}"
else
    echo -e "${YELLOW}⚠️  Versão já está atualizada${NC}"
fi

# Gera hash para assets (opcional - para cache busting mais agressivo)
ASSETS_HASH=$(date +%s)
echo -e "${BLUE}🔑 Hash de assets: ${ASSETS_HASH}${NC}"

# Cria um arquivo de versão para referência
echo "PWA_VERSION=${NEW_VERSION}" > .pwa-version
echo "ASSETS_HASH=${ASSETS_HASH}" >> .pwa-version
echo "DEPLOY_DATE=$(date)" >> .pwa-version

echo -e "${GREEN}🎉 Atualização de versão concluída!${NC}"
echo -e "${BLUE}💡 Próximos passos:${NC}"
echo -e "   1. Faça commit das alterações"
echo -e "   2. Deploy para produção"
echo -e "   3. Os usuários receberão a atualização automaticamente"

# Mostra estatísticas do cache
echo -e "\n${BLUE}📊 Informações do Cache:${NC}"
echo -e "   • Nome do cache: forcing-cache-${NEW_VERSION}"
echo -e "   • Estratégia: Network-first para HTML, Cache-first para assets"
echo -e "   • Limpeza automática: Sim"
echo -e "   • Notificação de update: Sim"

