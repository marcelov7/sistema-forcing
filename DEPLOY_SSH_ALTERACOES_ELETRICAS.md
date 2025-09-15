# 🚀 Deploy SSH - Alterações Elétricas

## 📋 Informações do Servidor

**Domínio**: `forcing.devaxis.com.br`  
**Usuário**: `devaxis-forcing`  
**IP**: `31.97.168.137`

## 🔧 Passos para Deploy via SSH

### **1. Conectar via SSH**

```bash
ssh devaxis-forcing@31.97.168.137
```

**Ou usando o domínio:**
```bash
ssh devaxis-forcing@forcing.devaxis.com.br
```

### **2. Navegar para o Diretório do Projeto**

```bash
cd /home/devaxis-forcing/forcing
# ou
cd /var/www/html/forcing
# ou
cd /home/devaxis-forcing/public_html/forcing
```

### **3. Verificar Status do Git**

```bash
git status
git log --oneline -5
```

### **4. Fazer Pull das Atualizações**

```bash
git pull origin main
# ou
git pull origin master
```

### **5. Instalar Dependências (se necessário)**

```bash
composer install --no-dev --optimize-autoloader
```

### **6. Executar Migrações**

```bash
php artisan migrate --force
```

### **7. Limpar Cache**

```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### **8. Otimizar Aplicação**

```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### **9. Verificar Permissões**

```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R devaxis-forcing:devaxis-forcing storage/
chown -R devaxis-forcing:devaxis-forcing bootstrap/cache/
```

### **10. Testar Aplicação**

```bash
php artisan serve --host=0.0.0.0 --port=8000
```

## 📁 Arquivos que Serão Atualizados

### **Novos Arquivos:**
- ✅ `app/Models/AlteracaoEletrica.php`
- ✅ `app/Http/Controllers/AlteracaoEletricaController.php`
- ✅ `database/migrations/2025_09_14_220711_create_alteracao_eletricas_table.php`
- ✅ `resources/views/alteracoes/` (todas as views)
- ✅ `resources/views/layouts/auth.blade.php`
- ✅ `resources/views/auth/login.blade.php`
- ✅ `public/css/login-modern.css`
- ✅ `public/css/ferramentas-menu.css`

### **Arquivos Modificados:**
- ✅ `routes/web.php` (novas rotas)
- ✅ `resources/views/layouts/app.blade.php` (menu de ferramentas)
- ✅ `app/Models/User.php` (métodos de permissão)

## 🔍 Verificações Pós-Deploy

### **1. Verificar Migração**

```bash
php artisan migrate:status
```

### **2. Testar Rotas**

```bash
php artisan route:list | grep alteracoes
```

### **3. Verificar Logs**

```bash
tail -f storage/logs/laravel.log
```

### **4. Testar no Navegador**

1. Acesse: `https://forcing.devaxis.com.br`
2. Faça login
3. Verifique se o menu "Alterações Elétricas" aparece
4. Teste criar uma nova alteração
5. Teste o botão PDF

## 🚨 Possíveis Problemas e Soluções

### **Erro de Permissão:**
```bash
sudo chown -R www-data:www-data storage/
sudo chmod -R 775 storage/
```

### **Erro de Migração:**
```bash
php artisan migrate:rollback
php artisan migrate
```

### **Erro de Cache:**
```bash
php artisan cache:clear
php artisan config:clear
php artisan view:clear
```

### **Erro de Composer:**
```bash
composer dump-autoload
composer install --no-dev
```

## 📋 Comandos Rápidos (Copy/Paste)

```bash
# Conectar
ssh devaxis-forcing@forcing.devaxis.com.br

# Navegar e atualizar
cd /home/devaxis-forcing/forcing
git pull origin main
composer install --no-dev --optimize-autoloader

# Migrar e otimizar
php artisan migrate --force
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Permissões
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R devaxis-forcing:devaxis-forcing storage/
chown -R devaxis-forcing:devaxis-forcing bootstrap/cache/

# Verificar
php artisan migrate:status
php artisan route:list | grep alteracoes
```

## ✅ Checklist de Deploy

- [ ] Conectado via SSH
- [ ] Navegado para diretório correto
- [ ] Git pull executado
- [ ] Composer install executado
- [ ] Migrações executadas
- [ ] Cache limpo
- [ ] Aplicação otimizada
- [ ] Permissões ajustadas
- [ ] Teste no navegador
- [ ] Logs verificados

## 🎯 Resultado Esperado

Após o deploy, o sistema deve ter:

1. **Nova página de login** moderna
2. **Menu "Alterações Elétricas"** na navbar
3. **Sistema completo** de CRUD para alterações
4. **Fluxo de aprovação** com 3 níveis
5. **Geração de PDF** funcional
6. **Segurança multitenant** implementada
7. **Menu "Outras Ferramentas"** funcionando

---

**⚠️ Importante**: Sempre faça backup antes de executar migrações em produção!

