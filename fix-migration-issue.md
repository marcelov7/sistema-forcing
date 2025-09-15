# 🔧 Correção Rápida - Problema de Migration

O problema é que a migration está tentando alterar a tabela `forcing` que não existe.

## SOLUÇÃO IMEDIATA:

### 1. Reverter e limpar migrations problemáticas:
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# Limpar banco novamente
rm -f database/database.sqlite
touch database/database.sqlite
chmod 664 database/database.sqlite
chown devaxis-forcing:devaxis-forcing database/database.sqlite

# Verificar arquivos de migration
ls -la database/migrations/
```

### 2. Criar migration da tabela forcing manualmente:
```bash
php artisan make:migration create_forcing_table --create=forcing
```

### 3. Ou mover migration problemática:
```bash
# Mover migration problemática para backup
mv database/migrations/2025_01_08_000000_add_missing_columns_to_forcing_table.php database/migrations/2025_01_08_000000_add_missing_columns_to_forcing_table.php.bak
```

### 4. Executar migrations sem a problemática:
```bash
php artisan migrate --force
php artisan db:seed --force
```

### 5. Verificar se o sistema funciona:
```bash
php artisan route:list
```

---

## ALTERNATIVA - Transferir migration correta:

**Do seu computador local, transfira apenas as migrations:**
```bash
scp -r database/migrations root@31.97.168.137:/home/devaxis-forcing/htdocs/forcing.devaxis.com.br/database/
```

Execute o passo 3 primeiro (mover a migration problemática) e tente novamente!
