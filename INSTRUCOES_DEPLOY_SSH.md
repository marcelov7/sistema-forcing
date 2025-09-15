# 🚀 Instruções de Deploy SSH - Alterações Elétricas

## 📋 Informações do Servidor

**🌐 Domínio**: `forcing.devaxis.com.br`  
**👤 Usuário**: `devaxis-forcing`  
**🔢 IP**: `31.97.168.137`

## 🎯 Opções de Deploy

### **Opção 1: Script Automatizado (Recomendado)**

**Windows:**
```cmd
deploy-ssh-alteracoes.bat
```

**Linux/Mac:**
```bash
./deploy-ssh-alteracoes.sh
```

### **Opção 2: Comandos Manuais**

#### **1. Conectar via SSH:**
```bash
ssh devaxis-forcing@forcing.devaxis.com.br
```

#### **2. Navegar e Atualizar:**
```bash
cd /home/devaxis-forcing/forcing
git pull origin main
composer install --no-dev --optimize-autoloader
```

#### **3. Executar Migrações:**
```bash
php artisan migrate --force
```

#### **4. Limpar e Otimizar:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

#### **5. Ajustar Permissões:**
```bash
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R devaxis-forcing:devaxis-forcing storage/
chown -R devaxis-forcing:devaxis-forcing bootstrap/cache/
```

## 📁 Arquivos que Serão Deployados

### **🆕 Novos Arquivos:**
- `app/Models/AlteracaoEletrica.php`
- `app/Http/Controllers/AlteracaoEletricaController.php`
- `database/migrations/2025_09_14_220711_create_alteracao_eletricas_table.php`
- `resources/views/alteracoes/` (todas as views)
- `resources/views/layouts/auth.blade.php`
- `resources/views/auth/login.blade.php`
- `public/css/login-modern.css`
- `public/css/ferramentas-menu.css`

### **✏️ Arquivos Modificados:**
- `routes/web.php` (novas rotas)
- `resources/views/layouts/app.blade.php` (menu de ferramentas)
- `app/Models/User.php` (métodos de permissão)

## 🔍 Verificações Pós-Deploy

### **1. Verificar Migração:**
```bash
php artisan migrate:status
```

### **2. Verificar Rotas:**
```bash
php artisan route:list | grep alteracoes
```

### **3. Verificar Logs:**
```bash
tail -f storage/logs/laravel.log
```

### **4. Testar no Navegador:**
1. Acesse: `https://forcing.devaxis.com.br`
2. Verifique o novo design de login
3. Faça login no sistema
4. Verifique se o menu "Alterações Elétricas" aparece
5. Teste criar uma nova alteração
6. Teste o fluxo de aprovação
7. Teste o botão PDF

## 🎯 Funcionalidades que Serão Ativadas

### **✅ Sistema de Alterações Elétricas:**
- Formulário de solicitação completo
- Fluxo de aprovação em 3 níveis
- Geração de PDF profissional
- Segurança multitenant
- Interface responsiva

### **✅ Nova Página de Login:**
- Design moderno estilo Windows Metro
- Tiles interativos
- Menu "Outras Ferramentas"
- Integração com Sistema de Relatórios

### **✅ Melhorias de Segurança:**
- Validação por setor
- Controle de permissões granular
- Proteção multitenant

## 🚨 Troubleshooting

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

## 📞 Suporte

Se encontrar problemas:

1. **Verifique os logs**: `tail -f storage/logs/laravel.log`
2. **Verifique as permissões**: `ls -la storage/`
3. **Verifique o status das migrações**: `php artisan migrate:status`
4. **Verifique as rotas**: `php artisan route:list | grep alteracoes`

---

**🎉 Após o deploy, o sistema terá todas as funcionalidades de alterações elétricas funcionando!**

