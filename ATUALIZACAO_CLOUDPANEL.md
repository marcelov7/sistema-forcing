# 🔄 Atualização CloudPanel - Versão Mobile Nativa

## 🎯 **Situação Atual**
- ✅ Sistema Laravel já rodando em `forcing.devaxis.com.br`
- ✅ Banco de dados configurado
- ✅ SSL/HTTPS funcionando
- 🎯 **Objetivo**: Adicionar recursos mobile nativos

## 📋 **Arquivos para ATUALIZAR (não substituir tudo)**

### **🆕 NOVOS ARQUIVOS (criar)**

#### **📱 API Controllers**
```
app/Http/Controllers/Api/
├── AuthController.php          # ✅ NOVO - Login/logout API
└── ForcingController.php       # ✅ NOVO - CRUD API Forcing
```

#### **🔧 Middleware**
```
app/Http/Middleware/
└── DetectDevice.php            # ✅ NOVO - Detecção mobile/desktop
```

#### **📦 Resources**
```
app/Http/Resources/
└── ForcingResource.php         # ✅ NOVO - Formatação dados API
```

#### **🌐 Web Controller**
```
app/Http/Controllers/
└── WebController.php           # ✅ NOVO - Controle web com detecção
```

#### **📱 Views**
```
resources/views/
└── mobile-suggestion.blade.php # ✅ NOVO - Página sugestão mobile
```

#### **📱 PWA Files**
```
public/
├── manifest.json               # ✅ NOVO - PWA Manifest
├── sw.js                       # ✅ NOVO - Service Worker
└── offline.html                # ✅ NOVO - Página offline
```

### **🔄 ARQUIVOS PARA ATUALIZAR**

#### **📝 Models**
```
app/Models/User.php             # ✅ ATUALIZAR - Adicionar métodos JWT
```

#### **🛣️ Routes**
```
routes/
├── api.php                     # ✅ ATUALIZAR - Adicionar rotas API
└── web.php                     # ✅ ATUALIZAR - Adicionar rotas web
```

#### **⚙️ Bootstrap**
```
bootstrap/app.php               # ✅ ATUALIZAR - Configurar middleware
```

#### **🔧 Config**
```
config/auth.php                 # ✅ ATUALIZAR - Adicionar guard JWT
```

#### **📱 Views Existentes**
```
resources/views/forcing/
└── index.blade.php             # ✅ ATUALIZAR - Melhorar interface
```

### **📦 Dependências**
```
composer.json                   # ✅ ATUALIZAR - Adicionar JWT
composer.lock                   # ✅ ATUALIZAR - Lock file
```

## 🚀 **Processo de Atualização**

### **📋 Fase 1: Backup**
```bash
# 1. Backup do banco de dados
mysqldump -u usuario -p nome_banco > backup_antes_mobile.sql

# 2. Backup dos arquivos atuais
tar -czf backup_arquivos_$(date +%Y%m%d).tar.gz /caminho/para/htdocs/
```

### **📁 Fase 2: Upload dos Novos Arquivos**

#### **🆕 Criar Pastas**
```
# No CloudPanel, criar:
app/Http/Controllers/Api/
app/Http/Middleware/
app/Http/Resources/
```

#### **📤 Upload dos Arquivos**
```
# Upload via FTP/SFTP ou interface do CloudPanel:

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

# PWA
public/manifest.json
public/sw.js
public/offline.html
```

### **🔄 Fase 3: Atualizar Arquivos Existentes**

#### **📝 User.php**
```php
// Adicionar no final da classe User:
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    // ... código existente ...
    
    // NOVO: Adicionar métodos JWT
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }
    
    public function getJWTCustomClaims()
    {
        return [];
    }
}
```

#### **🛣️ routes/api.php**
```php
// Adicionar no final do arquivo:
Route::prefix('v1')->group(function () {
    Route::post('auth/login', [AuthController::class, 'login']);
    Route::post('auth/logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('auth/refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('forcings', ForcingController::class);
        Route::get('forcings/{forcing}/equipments', [ForcingController::class, 'equipments']);
    });
});

Route::get('health', function () {
    return response()->json(['status' => 'ok', 'timestamp' => now()]);
});
```

#### **🛣️ routes/web.php**
```php
// Substituir rotas existentes por:
Route::get('/', [WebController::class, 'index'])->name('home');
Route::get('/mobile-suggestion', [WebController::class, 'mobileSuggestion'])->name('mobile-suggestion');
Route::get('/device-info', [WebController::class, 'deviceInfo'])->name('device-info');

Route::middleware('auth')->group(function () {
    Route::get('/dashboard', [WebController::class, 'dashboard'])->name('dashboard');
    // ... outras rotas existentes ...
});
```

#### **⚙️ bootstrap/app.php**
```php
// Adicionar no final do arquivo:
$middleware->alias([
    'check.profile' => \App\Http\Middleware\CheckProfile::class,
    'super.admin' => \App\Http\Middleware\SuperAdminMiddleware::class,
    'detect.device' => \App\Http\Middleware\DetectDevice::class, // NOVO
]);

// Adicionar middleware global
$middleware->append(\App\Http\Middleware\DetectDevice::class);
```

#### **🔧 config/auth.php**
```php
// Adicionar na seção 'guards':
'guards' => [
    'web' => [
        'driver' => 'session',
        'provider' => 'users',
    ],
    'api' => [
        'driver' => 'jwt',        // NOVO
        'provider' => 'users',
    ],
],
```

### **📦 Fase 4: Instalar Dependências**

#### **🔧 Composer**
```bash
# No terminal do CloudPanel ou via SSH:
composer install --no-dev --optimize-autoloader
```

#### **🔑 JWT**
```bash
# Configurar JWT
php artisan jwt:secret --force
```

### **⚙️ Fase 5: Configuração**

#### **🗄️ Migrations**
```bash
# Executar migrations (se houver novas)
php artisan migrate --force
```

#### **🧹 Cache**
```bash
# Limpar e recriar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 🧪 **Teste da Atualização**

### **🌐 Teste Web**
```bash
# 1. Acessar site principal
https://forcing.devaxis.com.br

# 2. Testar login
# 3. Verificar dashboard
# 4. Testar funcionalidades existentes
```

### **📱 Teste Mobile**
```bash
# 1. Acessar pelo celular
https://forcing.devaxis.com.br

# 2. Verificar detecção mobile
# 3. Testar banner PWA
# 4. Verificar redirecionamento
```

### **🔌 Teste API**
```bash
# 1. Health check
curl https://forcing.devaxis.com.br/api/health

# 2. Teste login
curl -X POST https://forcing.devaxis.com.br/api/v1/auth/login \
  -H "Content-Type: application/json" \
  -d '{"email":"usuario@email.com","password":"senha"}'

# 3. Teste listagem
curl -X GET https://forcing.devaxis.com.br/api/v1/forcings \
  -H "Authorization: Bearer SEU_TOKEN_AQUI"
```

### **📱 Teste PWA**
```bash
# 1. Manifest
https://forcing.devaxis.com.br/manifest.json

# 2. Service Worker
https://forcing.devaxis.com.br/sw.js

# 3. Teste instalação no celular
```

## 🚨 **Rollback (se necessário)**

### **🔄 Reverter Alterações**
```bash
# 1. Restaurar backup do banco
mysql -u usuario -p nome_banco < backup_antes_mobile.sql

# 2. Restaurar arquivos
tar -xzf backup_arquivos_YYYYMMDD.tar.gz

# 3. Limpar cache
php artisan config:clear
php artisan route:clear
php artisan view:clear
```

## 📋 **Checklist de Atualização**

### **✅ Preparação**
- [ ] ✅ Backup do banco de dados
- [ ] ✅ Backup dos arquivos
- [ ] ✅ Verificar espaço em disco
- [ ] ✅ Verificar permissões

### **📤 Upload**
- [ ] ✅ Criar pastas da API
- [ ] ✅ Upload controllers da API
- [ ] ✅ Upload middleware
- [ ] ✅ Upload resources
- [ ] ✅ Upload views
- [ ] ✅ Upload arquivos PWA

### **🔄 Atualização**
- [ ] ✅ Atualizar User.php
- [ ] ✅ Atualizar routes/api.php
- [ ] ✅ Atualizar routes/web.php
- [ ] ✅ Atualizar bootstrap/app.php
- [ ] ✅ Atualizar config/auth.php
- [ ] ✅ Atualizar composer.json

### **⚙️ Configuração**
- [ ] ✅ Instalar dependências
- [ ] ✅ Configurar JWT
- [ ] ✅ Executar migrations
- [ ] ✅ Limpar cache
- [ ] ✅ Otimizar cache

### **🧪 Teste**
- [ ] ✅ Testar sistema web
- [ ] ✅ Testar detecção mobile
- [ ] ✅ Testar API
- [ ] ✅ Testar PWA
- [ ] ✅ Verificar logs

## 🎯 **Resultado Final**

### **✅ Sistema Atualizado:**
- ✅ **Interface web** melhorada
- ✅ **API REST** funcionando
- ✅ **Detecção mobile** ativa
- ✅ **PWA** instalável
- ✅ **Funcionalidades existentes** mantidas
- ✅ **Zero downtime** na atualização

### **📱 Novos Recursos:**
- ✅ Login via API
- ✅ CRUD Forcing via API
- ✅ Detecção automática de dispositivo
- ✅ PWA para Android/iOS
- ✅ Interface mobile otimizada

## 🚀 **Próximos Passos**

### **📱 Mobile App**
1. Configurar URL da API: `https://forcing.devaxis.com.br/api/v1`
2. Testar login mobile
3. Desenvolver funcionalidades
4. Preparar para produção

### **📊 Monitoramento**
1. Acompanhar logs
2. Monitorar performance
3. Verificar uso da API
4. Coletar feedback

---

## 🎉 **Resumo da Atualização**

### **🔄 O que foi feito:**
- ✅ Adicionados novos arquivos da API
- ✅ Atualizados arquivos existentes
- ✅ Mantidas funcionalidades atuais
- ✅ Adicionados recursos mobile

### **🚀 Resultado:**
**Sistema atualizado com recursos mobile nativos sem perder funcionalidades existentes!**

### **📱 Acesso:**
- **Web**: https://forcing.devaxis.com.br (melhorado)
- **Mobile**: Detecção automática + PWA
- **API**: https://forcing.devaxis.com.br/api/v1

**🎯 Atualização concluída com sucesso!**

