# 🪟 Guia de Deploy - Windows + CloudPanel

## 🎯 Para Usuários Windows

Como você está usando Windows, aqui estão as instruções específicas para seu ambiente:

## 🚀 **Passo 1: Preparar o Projeto**

### 1.1 Verificar Pré-requisitos

```powershell
# Verificar se PHP está instalado
php --version

# Verificar se Composer está instalado
composer --version

# Verificar se Node.js está instalado
node --version
npm --version
```

### 1.2 Configurar Ambiente

```powershell
# Copiar arquivo de configuração
copy env.cloudpanel.example .env

# Editar o arquivo .env (use o editor de sua preferência)
notepad .env
```

**Configure as seguintes linhas no .env:**
```env
APP_URL=https://seu-dominio.com
DB_DATABASE=forcing_sistema
DB_USERNAME=forcing_user
DB_PASSWORD=sua_senha_mysql
```

## 🚀 **Passo 2: Deploy da API**

### 2.1 Usar Script Windows

```powershell
# Execute o script de deploy para Windows
.\deploy-cloudpanel.bat
```

**O script irá:**
- ✅ Instalar dependências do Composer
- ✅ Configurar JWT automaticamente
- ✅ Executar migrations do banco
- ✅ Otimizar para produção
- ✅ Configurar permissões (Windows)
- ✅ Testar a API

### 2.2 Deploy Manual (Alternativo)

Se preferir fazer manualmente:

```powershell
# Instalar dependências
composer install --no-dev --optimize-autoloader

# Configurar aplicação
php artisan key:generate
php artisan jwt:secret --force

# Executar migrations
php artisan migrate --force

# Otimizar para produção
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Testar
php artisan serve
```

## 📱 **Passo 3: Configurar Aplicação Mobile**

### 3.1 Instalar Dependências

```powershell
# Navegar para pasta mobile
cd mobile-app

# Instalar dependências
npm install

# Instalar Expo CLI globalmente (se não tiver)
npm install -g @expo/cli
```

### 3.2 Configurar URL da API

**Edite o arquivo `mobile-app/src/services/api.ts`:**

```typescript
// Substitua pela URL do seu servidor
const API_BASE_URL = 'https://seu-dominio.com/api/v1';
```

### 3.3 Iniciar Aplicação

```powershell
# Iniciar Expo
expo start

# Ou usar npm
npm start
```

## 🔧 **Passo 4: Testar Sistema**

### 4.1 Testar API

```powershell
# Testar health check
curl https://seu-dominio.com/api/health

# Ou usar PowerShell
Invoke-WebRequest -Uri "https://seu-dominio.com/api/health"
```

### 4.2 Testar Mobile

1. **Instale Expo Go** no seu celular
2. **Escaneie o QR Code** que aparece no terminal
3. **Faça login** com usuários existentes

## 🛠️ **Comandos Úteis Windows**

### PowerShell

```powershell
# Ver logs em tempo real
Get-Content storage\logs\laravel.log -Wait

# Verificar processos PHP
Get-Process php

# Verificar portas em uso
netstat -an | findstr :8000

# Limpar cache do Laravel
php artisan cache:clear
php artisan config:clear
php artisan route:clear
```

### CMD (Command Prompt)

```cmd
# Navegar entre pastas
cd /d C:\xampp\htdocs\Forcing

# Listar arquivos
dir

# Copiar arquivos
copy arquivo.txt destino\

# Ver logs
type storage\logs\laravel.log
```

## 🚨 **Solução de Problemas Windows**

### Problema 1: Comando não encontrado

```powershell
# Se php não for reconhecido, adicione ao PATH
$env:PATH += ";C:\xampp\php"

# Ou use o caminho completo
C:\xampp\php\php.exe artisan serve
```

### Problema 2: Permissões negadas

```powershell
# Executar PowerShell como Administrador
# Ou dar permissões específicas
icacls storage /grant Everyone:F /T
```

### Problema 3: Porta em uso

```powershell
# Verificar o que está usando a porta 8000
netstat -ano | findstr :8000

# Usar outra porta
php artisan serve --port=8080
```

### Problema 4: Expo não funciona

```powershell
# Limpar cache do Expo
expo start -c

# Reinstalar dependências
Remove-Item -Recurse -Force node_modules
npm install
```

## 📋 **Checklist Windows**

### ✅ Ambiente
- [ ] PHP 8.2+ instalado
- [ ] Composer instalado
- [ ] Node.js 18+ instalado
- [ ] Expo CLI instalado

### ✅ API
- [ ] Arquivo .env configurado
- [ ] Banco MySQL configurado
- [ ] Script deploy-cloudpanel.bat executado
- [ ] API respondendo em /api/health

### ✅ Mobile
- [ ] Dependências instaladas (npm install)
- [ ] URL da API configurada
- [ ] Expo Go instalado no celular
- [ ] Login funcionando

## 🎯 **Próximos Passos**

1. **Testar todas as funcionalidades**
2. **Configurar SSL no CloudPanel**
3. **Fazer backup do banco**
4. **Treinar a equipe**
5. **Publicar nas stores**

## 📞 **Suporte Windows**

Se tiver problemas específicos do Windows:

- 📧 **Email:** suporte@seu-dominio.com
- 💬 **Discord:** [Servidor de Suporte](https://discord.gg/seu-servidor)
- 📚 **Wiki:** [Documentação Windows](https://wiki.seu-dominio.com/windows)

---

**🎉 Agora você tem um sistema completo funcionando no Windows!**

