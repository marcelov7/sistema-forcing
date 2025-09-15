#!/bin/bash

echo "=== DIAGNÓSTICO AVANÇADO DO NGINX ==="
echo "Data: $(date)"
echo ""

# Corrigir PATH
export PATH="/usr/local/sbin:/usr/local/bin:/usr/sbin:/usr/bin:/sbin:/bin"

echo "1. TESTANDO RELOAD DO NGINX:"
sudo systemctl reload nginx 2>&1 || echo "Erro: Sem permissão para reload do nginx"
echo ""

echo "2. VERIFICANDO STATUS DO NGINX:"
sudo systemctl status nginx --no-pager -l 2>&1 || systemctl status nginx --no-pager -l 2>&1 || echo "Sem acesso ao status"
echo ""

echo "3. VERIFICANDO CONFIGURAÇÃO ATIVA DO SITE:"
# Procurar por arquivos de configuração do site
find /etc/nginx -name "*forcing*" 2>/dev/null || echo "Sem acesso aos arquivos de config do nginx"
echo ""

echo "4. TESTANDO DIFERENTES CAMINHOS:"
echo "4a. Testando index.php diretamente:"
curl -I "https://forcing.devaxis.com.br/index.php" 2>/dev/null
echo ""

echo "4b. Testando com public/index.php:"
curl -I "https://forcing.devaxis.com.br/public/index.php" 2>/dev/null
echo ""

echo "5. VERIFICANDO ARQUIVO DE TESTE PHP:"
echo "5a. Conteúdo do arquivo teste:"
cat public/teste-documentroot.php
echo ""

echo "5b. Testando arquivo teste via curl:"
curl "https://forcing.devaxis.com.br/teste-documentroot.php" 2>/dev/null
echo ""

echo "5c. Testando arquivo teste com public/:"
curl "https://forcing.devaxis.com.br/public/teste-documentroot.php" 2>/dev/null
echo ""

echo "6. VERIFICANDO PERMISSÕES DO DIRETÓRIO PUBLIC:"
ls -la public/ | head -10
echo ""

echo "7. VERIFICANDO SE HÁ OUTROS ARQUIVOS INDEX:"
find . -name "index.*" -type f 2>/dev/null
echo ""

echo "8. TESTANDO ACESSO A ARQUIVO ESTÁTICO:"
echo "Criando arquivo HTML simples para teste..."
echo "<h1>TESTE STATIC HTML - $(date)</h1>" > public/teste-static.html
echo "8a. Testando arquivo HTML estático:"
curl "https://forcing.devaxis.com.br/teste-static.html" 2>/dev/null
echo ""

echo "8b. Testando arquivo HTML com public/:"
curl "https://forcing.devaxis.com.br/public/teste-static.html" 2>/dev/null
echo ""

echo "9. VERIFICANDO LOGS DE ERRO DO NGINX (se acessível):"
sudo tail -20 /var/log/nginx/error.log 2>/dev/null || echo "Sem acesso aos logs do nginx"
echo ""

echo "10. VERIFICANDO CONFIGURAÇÃO DO CLOUDPANEL:"
# Verificar se há arquivos de config específicos do CloudPanel
find /etc/nginx -name "*devaxis*" -o -name "*forcing*" 2>/dev/null | head -5
echo ""

echo "=== FIM DO DIAGNÓSTICO ==="

