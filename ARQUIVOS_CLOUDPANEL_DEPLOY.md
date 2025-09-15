# 📁 Arquivos para Deploy CloudPanel - Versão Mobile Nativa

## 🎯 **Arquivos que DEVEM ser subidos para o CloudPanel**

### **✅ 1. ESTRUTURA BÁSICA DO LARAVEL**
```
Forcing/
├── app/                          # ✅ SUBIR TUDO
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/              # ✅ NOVO - Controllers da API
│   │   │   │   ├── AuthController.php
│   │   │   │   └── ForcingController.php
│   │   │   └── WebController.php # ✅ NOVO - Controller web
│   │   ├── Middleware/
│   │   │   └── DetectDevice.php  # ✅ NOVO - Detecção de dispositivo
│   │   └── Resources/
│   │       └── ForcingResource.php # ✅ NOVO - Resource da API
│   ├── Models/                   # ✅ SUBIR TUDO (incluindo User.php atualizado)
│   └── Services/                 # ✅ SUBIR TUDO
├── bootstrap/
│   └── app.php                   # ✅ ATUALIZADO - Middleware configurado
├── config/                       # ✅ SUBIR TUDO
├── database/
│   ├── migrations/               # ✅ SUBIR TUDO
│   └── seeders/                  # ✅ SUBIR TUDO
├── public/                       # ✅ SUBIR TUDO + NOVOS ARQUIVOS
│   ├── manifest.json             # ✅ NOVO - PWA Manifest
│   ├── sw.js                     # ✅ NOVO - Service Worker
│   ├── offline.html              # ✅ NOVO - Página offline
│   └── icons/                    # ✅ NOVO - Ícones PWA (criar pasta)
├── resources/
│   ├── views/
│   │   ├── forcing/
│   │   │   └── index.blade.php   # ✅ ATUALIZADO
│   │   └── mobile-suggestion.blade.php # ✅ NOVO - Página mobile
│   └── css/                      # ✅ SUBIR TUDO
├── routes/
│   ├── api.php                   # ✅ ATUALIZADO - Rotas da API
│   └── web.php                   # ✅ ATUALIZADO - Rotas web
├── storage/                      # ✅ SUBIR TUDO (com permissões)
├── vendor/                       # ✅ SUBIR TUDO (ou instalar via composer)
├── .env                          # ✅ CONFIGURAR - Variáveis de ambiente
├── .env.cloudpanel.example       # ✅ NOVO - Template de configuração
├── composer.json                 # ✅ ATUALIZADO - Dependências JWT
├── composer.lock                 # ✅ ATUALIZADO
├── deploy-cloudpanel.bat         # ✅ NOVO - Script de deploy Windows
├── deploy-cloudpanel.sh          # ✅ NOVO - Script de deploy Linux
└── artisan                       # ✅ SUBIR
```

### **✅ 2. ARQUIVOS NOVOS CRIADOS**
```
# API Controllers
app/Http/Controllers/Api/AuthController.php
app/Http/Controllers/Api/ForcingController.php
app/Http/Controllers/WebController.php

# Middleware
app/Http/Middleware/DetectDevice.php

# Resources
app/Http/Resources/ForcingResource.php

# Views
resources/views/mobile-suggestion.blade.php

# PWA Files
public/manifest.json
public/sw.js
public/offline.html

# Configuração
.env.cloudpanel.example
deploy-cloudpanel.bat
deploy-cloudpanel.sh

# Documentação
GUIA_DEPLOY_MOBILE.md
SISTEMA_MULTI_PLATAFORMA.md
GUIA_PWA_INSTALACAO.md
```

### **✅ 3. ARQUIVOS ATUALIZADOS**
```
# Models
app/Models/User.php (JWT methods adicionados)

# Routes
routes/api.php (API routes adicionadas)
routes/web.php (Web routes atualizadas)

# Bootstrap
bootstrap/app.php (Middleware configurado)

# Views
resources/views/forcing/index.blade.php (Interface atualizada)

# Config
config/auth.php (JWT guard configurado)
```

## 🚫 **Arquivos que NÃO devem ser subidos**

### **❌ 1. ARQUIVOS DE DESENVOLVIMENTO**
```
# Mobile App (React Native)
mobile-app/                       # ❌ NÃO SUBIR - App separado
├── src/
├── node_modules/
├── package.json
└── App.tsx

# Arquivos temporários
*.log
*.tmp
*.cache
.DS_Store
Thumbs.db

# IDE
.vscode/
.idea/
*.swp
*.swo

# Git
.git/
.gitignore
```

### **❌ 2. ARQUIVOS DE TESTE**
```
tests/
phpunit.xml
.env.testing
```

## 📋 **CHECKLIST DE DEPLOY**

### **✅ Fase 1: Preparação**
- [ ] Backup do banco atual
- [ ] Backup dos arquivos atuais
- [ ] Testar localmente
- [ ] Verificar dependências

### **✅ Fase 2: Upload dos Arquivos**
- [ ] Upload da estrutura Laravel completa
- [ ] Upload dos novos arquivos da API
- [ ] Upload dos arquivos PWA
- [ ] Upload das views atualizadas
- [ ] Upload dos scripts de deploy

### **✅ Fase 3: Configuração**
- [ ] Configurar .env com credenciais do CloudPanel
- [ ] Executar migrations
- [ ] Configurar JWT
- [ ] Configurar permissões
- [ ] Testar API

### **✅ Fase 4: Teste**
- [ ] Testar acesso web
- [ ] Testar detecção mobile
- [ ] Testar API endpoints
- [ ] Testar PWA
- [ ] Verificar logs

## 🛠️ **Comandos de Deploy**

### **📁 Upload Manual (FTP/SFTP)**
```bash
# Upload todos os arquivos listados acima
# Manter estrutura de pastas
# Configurar permissões
```

### **🚀 Deploy Automatizado**
```bash
# No servidor CloudPanel
./deploy-cloudpanel.sh

# Ou no Windows
deploy-cloudpanel.bat
```

### **⚙️ Configuração Manual**
```bash
# Copiar template
cp .env.cloudpanel.example .env

# Editar configurações
nano .env

# Instalar dependências
composer install --no-dev --optimize-autoloader

# Configurar JWT
php artisan jwt:secret

# Executar migrations
php artisan migrate --force

# Otimizar
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 📱 **Configuração da Aplicação Mobile**

### **📱 Mobile App (Separado)**
```bash
# NÃO subir para CloudPanel
# Manter em repositório separado
# Configurar URL da API

# mobile-app/src/services/api.ts
const API_BASE_URL = 'https://seu-dominio.com/api/v1';
```

### **🌐 URLs Importantes**
```bash
# API Health Check
https://seu-dominio.com/api/health

# API Login
https://seu-dominio.com/api/v1/auth/login

# PWA Manifest
https://seu-dominio.com/manifest.json

# Service Worker
https://seu-dominio.com/sw.js
```

## 🔧 **Configuração do .env**

### **📝 Exemplo de .env para CloudPanel**
```env
APP_NAME="Sistema de Forcing"
APP_ENV=production
APP_KEY=base64:SUA_CHAVE_AQUI
APP_DEBUG=false
APP_URL=https://seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=forcing_sistema
DB_USERNAME=forcing_user
DB_PASSWORD=sua_senha_mysql

JWT_SECRET=SUA_JWT_SECRET_AQUI
JWT_TTL=60
JWT_REFRESH_TTL=20160

# PWA
MOBILE_API_URL=https://seu-dominio.com/api/v1
```

## 📊 **Estrutura Final no CloudPanel**

### **📁 Estrutura de Pastas**
```
/var/www/html/seu-dominio/
├── app/
│   ├── Http/
│   │   ├── Controllers/
│   │   │   ├── Api/          # ✅ API Controllers
│   │   │   └── WebController.php
│   │   ├── Middleware/
│   │   │   └── DetectDevice.php
│   │   └── Resources/
│   │       └── ForcingResource.php
│   └── Models/
├── public/
│   ├── manifest.json         # ✅ PWA Manifest
│   ├── sw.js                 # ✅ Service Worker
│   ├── offline.html          # ✅ Offline Page
│   └── icons/                # ✅ PWA Icons
├── resources/
│   └── views/
│       ├── forcing/
│       └── mobile-suggestion.blade.php
├── routes/
│   ├── api.php               # ✅ API Routes
│   └── web.php
├── .env                      # ✅ Configurado
└── artisan
```

## 🚀 **Script de Deploy Completo**

### **📝 deploy-cloudpanel.sh**
```bash
#!/bin/bash
echo "🚀 Deploy Sistema de Forcing - Versão Mobile Nativa"

# Verificar se está no diretório correto
if [ ! -f "composer.json" ]; then
    echo "❌ Execute no diretório raiz do projeto"
    exit 1
fi

# Instalar dependências
echo "📦 Instalando dependências..."
composer install --no-dev --optimize-autoloader

# Configurar JWT
echo "🔐 Configurando JWT..."
php artisan jwt:secret --force

# Executar migrations
echo "🗄️ Executando migrations..."
php artisan migrate --force

# Otimizar
echo "⚡ Otimizando..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Configurar permissões
echo "🔐 Configurando permissões..."
chmod -R 755 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

echo "✅ Deploy concluído!"
echo "📱 Sistema mobile nativo pronto!"
echo "🌐 Acesse: https://seu-dominio.com"
```

## 📱 **Próximos Passos**

### **✅ Após Deploy:**
1. **Testar sistema web** - Acesse https://seu-dominio.com
2. **Testar detecção mobile** - Acesse pelo celular
3. **Testar API** - https://seu-dominio.com/api/health
4. **Configurar mobile app** - Atualizar URL da API
5. **Testar PWA** - Instalar no dispositivo

### **📱 Mobile App:**
```bash
# Em outro local (não no CloudPanel)
cd mobile-app
npm install
# Editar src/services/api.ts com URL do CloudPanel
expo start
```

## 🎯 **Resumo**

### **✅ SUBIR para CloudPanel:**
- ✅ Estrutura Laravel completa
- ✅ Novos controllers da API
- ✅ Middleware de detecção
- ✅ Views atualizadas
- ✅ Arquivos PWA
- ✅ Scripts de deploy

### **❌ NÃO SUBIR:**
- ❌ Pasta mobile-app/
- ❌ node_modules/
- ❌ Arquivos temporários
- ❌ Configurações de desenvolvimento

### **🚀 Resultado:**
- ✅ Sistema web funcionando
- ✅ API REST disponível
- ✅ Detecção automática mobile
- ✅ PWA instalável
- ✅ Pronto para app mobile nativo

**O sistema ficará completo e funcionando em todas as plataformas!** 🎉

