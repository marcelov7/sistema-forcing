# 🚨 CORREÇÃO URGENTE - Banco SQLite Somente Leitura

Execute estes comandos no servidor **IMEDIATAMENTE**:

## 1. Corrigir permissões do banco de dados
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# Corrigir permissões do arquivo SQLite
chmod 664 database/database.sqlite
chown devaxis-forcing:devaxis-forcing database/database.sqlite

# Corrigir permissões do diretório database
chmod 775 database/
chown devaxis-forcing:devaxis-forcing database/
```

## 2. Corrigir permissões do Apache/Nginx
```bash
# Se estiver usando Apache
chown -R www-data:www-data database/
chmod 664 database/database.sqlite
chmod 775 database/

# OU se estiver usando Nginx
chown -R nginx:nginx database/
chmod 664 database/database.sqlite
chmod 775 database/
```

## 3. Comando completo de emergência
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br && chmod 777 database/ && chmod 666 database/database.sqlite && chown -R www-data:www-data database/ && php artisan cache:clear
```

## 4. Verificar se funcionou
```bash
# Testar escrita no banco
php artisan tinker --execute="DB::table('users')->count();"
```

---

## ⚠️ IMPORTANTE:
- Execute o comando completo de emergência primeiro
- Se não funcionar, tente com nginx:nginx ao invés de www-data:www-data
- O banco precisa ser gravável pelo servidor web
