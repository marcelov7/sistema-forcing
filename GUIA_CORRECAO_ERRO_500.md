# GUIA DE CORREÇÃO - ERRO 500 PRODUÇÃO

## Problema
Após subir o arquivo `index.blade.php` com melhorias mobile, o sistema apresenta erro 500.

## Soluções por Ordem de Prioridade

### 1. SOLUÇÃO RÁPIDA (Via CloudPanel)
1. Acesse o CloudPanel
2. Vá em File Manager
3. Navegue até: `/home/devaxis-forcing/htdocs/forcing.devaxis.com.br`
4. Abra o Terminal dentro do CloudPanel
5. Execute:
```bash
php artisan config:clear && php artisan cache:clear && php artisan view:clear
```

### 2. SOLUÇÃO COMPLETA (Via SSH)
Se conseguir conectar via SSH:
```bash
# Fazer upload do script
scp corrigir_erro_500.sh root@31.97.168.137:/tmp/

# Executar no servidor
ssh root@31.97.168.137
cd /tmp
chmod +x corrigir_erro_500.sh
./corrigir_erro_500.sh
```

### 3. SOLUÇÃO MANUAL (CloudPanel File Manager)
1. **Limpar Cache Manualmente:**
   - Delete: `bootstrap/cache/config.php`
   - Delete todos os arquivos em: `storage/framework/cache/`
   - Delete todos os arquivos em: `storage/framework/views/`

2. **Verificar Permissões:**
   - Pasta `storage/` - 775
   - Pasta `bootstrap/cache/` - 775

3. **Verificar Log de Erro:**
   - Acesse: `storage/logs/laravel.log`
   - Veja os últimos erros

### 4. SOLUÇÃO DE EMERGÊNCIA
Se ainda não funcionar, pode ser problema de sintaxe no arquivo:

1. **Fazer backup:**
   - Renomear o arquivo atual: `index.blade.php.bak`

2. **Restaurar versão anterior temporariamente**

3. **Aplicar melhorias gradualmente**

## Comandos Essenciais Laravel
```bash
# Limpeza completa
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Verificar status
php artisan migrate:status

# Ver logs
tail -f storage/logs/laravel.log
```

## Possíveis Causas do Erro 500
1. ✅ **Cache corrompido** (mais provável)
2. ❓ Erro de sintaxe no Blade
3. ❓ Permissões incorretas
4. ❓ Configuração do servidor

## O que fazer AGORA:
1. Tente a **Solução Rápida** pelo CloudPanel
2. Se não funcionar, use a **Solução Manual**
3. Monitore os logs para identificar o erro específico
