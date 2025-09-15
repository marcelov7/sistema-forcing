# 🔧 Comandos para Corrigir Problemas no Servidor

Execute estes comandos no servidor CloudPanel:

## 1. Limpar arquivos de backup problemáticos
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
rm -f app/Models/Forcing_fixed.php
rm -f app/Models/Forcing_backup.php
```

## 2. Recriar banco de dados do zero
```bash
# Apagar banco atual e recriar
rm -f database/database.sqlite
touch database/database.sqlite
chmod 664 database/database.sqlite
chown devaxis-forcing:devaxis-forcing database/database.sqlite
```

## 3. Mover migration problemática e executar migrations
```bash
# Mover migration problemática
mv database/migrations/2025_01_08_000000_add_missing_columns_to_forcing_table.php database/migrations/2025_01_08_000000_add_missing_columns_to_forcing_table.php.bak

# Executar migrations
php artisan migrate --force
php artisan db:seed --force
```

## 4. Verificar se resources/views existe
```bash
ls -la resources/
```

### Se resources/views NÃO existir, retransfira do seu computador:
**Do seu computador local (c:\xampp\htdocs\Forcing):**
```bash
scp -r resources root@31.97.168.137:/home/devaxis-forcing/htdocs/forcing.devaxis.com.br/
```

## 5. Após corrigir resources/views, configurar cache
```bash
# Limpar caches primeiro
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recompilar autoloader
composer dump-autoload --optimize

# Reconfigurar cache
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 6. Configurar permissões finais (CRÍTICO)
```bash
# Configurar permissões completas
chown -R devaxis-forcing:devaxis-forcing /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
chmod -R 755 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
chmod -R 775 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage
chmod -R 775 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/bootstrap/cache

# Permissões específicas para banco SQLite (Nginx como root)
chmod 777 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/database/
chmod 666 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/database/database.sqlite
chown -R root:root /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/database/

# Permissões específicas para views compiladas
chmod -R 777 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage/framework
chmod -R 777 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage/framework/views
chmod -R 777 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage/framework/cache
chmod -R 777 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage/logs
```

## 7. Testar o sistema
```bash
# Verificar se tudo está funcionando
php artisan tinker --execute="echo 'Sistema funcionando!';"
```

---

## ⚠️ IMPORTANTE:
- Execute os comandos na ordem
- Se resources/views não existir, retransfira apenas essa pasta
- O banco será recriado do zero com os usuários padrão
