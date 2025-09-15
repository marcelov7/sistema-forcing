# 📱 Guia Completo - Sistema de Forcing Mobile + CloudPanel

## 🎯 Visão Geral

Este guia mostra como transformar seu sistema Laravel de Forcing em uma aplicação mobile nativa completa, hospedando a API no CloudPanel.

## 🏗️ Arquitetura da Solução

```
📱 Aplicação Mobile (React Native + Expo)
    ↕️ API REST (Laravel + JWT)
    ↕️ Banco MySQL (CloudPanel)
    ↕️ Hospedagem (CloudPanel)
```

## 🚀 **PARTE 1: Deploy da API no CloudPanel**

### 1.1 Preparar CloudPanel

1. **Acesse seu CloudPanel**
2. **Crie um novo site:**
   - Nome: `forcing-api`
   - Domínio: `api.seu-dominio.com`
   - PHP: 8.2+

3. **Configure banco MySQL:**
   - Nome: `forcing_sistema`
   - Usuário: `forcing_user`
   - Senha: `senha_segura_123`

### 1.2 Upload dos Arquivos

```bash
# Via Git (recomendado)
git clone https://github.com/seu-usuario/forcing-system.git
cd forcing-system

# Via Upload manual
# Faça upload de todos os arquivos para a pasta do domínio
```

### 1.3 Configurar Ambiente

```bash
# Copiar arquivo de configuração
cp env.cloudpanel.example .env

# Editar configurações
nano .env
```

**Configurar no .env:**
```env
APP_NAME="Sistema de Forcing"
APP_ENV=production
APP_DEBUG=false
APP_URL=https://api.seu-dominio.com

DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_DATABASE=forcing_sistema
DB_USERNAME=forcing_user
DB_PASSWORD=senha_segura_123

# JWT (será gerado automaticamente)
JWT_SECRET=
```

### 1.4 Executar Deploy

```bash
# Dar permissão ao script
chmod +x deploy-cloudpanel.sh

# Executar deploy automático
./deploy-cloudpanel.sh
```

**O script irá:**
- ✅ Instalar dependências
- ✅ Configurar JWT
- ✅ Executar migrations
- ✅ Otimizar para produção
- ✅ Configurar permissões
- ✅ Testar API

### 1.5 Verificar Deploy

**Teste a API:**
```bash
curl https://api.seu-dominio.com/api/health
```

**Resposta esperada:**
```json
{
  "status": "ok",
  "message": "API do Sistema de Forcing funcionando",
  "timestamp": "2024-01-01T12:00:00.000000Z",
  "version": "1.0.0"
}
```

---

## 📱 **PARTE 2: Configurar Aplicação Mobile**

### 2.1 Pré-requisitos

```bash
# Instalar Node.js (18+)
# Instalar Expo CLI
npm install -g @expo/cli

# Instalar Expo Go no celular
# Android: Play Store
# iOS: App Store
```

### 2.2 Configurar Projeto Mobile

```bash
# Navegar para pasta do mobile
cd mobile-app

# Instalar dependências
npm install

# Configurar URL da API
nano src/services/api.ts
```

**Atualizar URL da API:**
```typescript
const API_BASE_URL = 'https://api.seu-dominio.com/api/v1';
```

### 2.3 Iniciar Aplicação

```bash
# Iniciar Expo
expo start

# Escanear QR Code com Expo Go
# Ou executar no emulador
```

---

## 🔧 **PARTE 3: Configurações Avançadas**

### 3.1 SSL/HTTPS no CloudPanel

1. **Acesse CloudPanel → SSL**
2. **Ativar Let's Encrypt**
3. **Forçar HTTPS**

### 3.2 Configurar CORS

**No arquivo `config/cors.php`:**
```php
'paths' => ['api/*', 'sanctum/csrf-cookie'],
'allowed_methods' => ['*'],
'allowed_origins' => ['https://app.expo.dev', 'exp://localhost:19000'],
'allowed_headers' => ['*'],
'exposed_headers' => [],
'max_age' => 0,
'supports_credentials' => false,
```

### 3.3 Otimizações de Performance

**Configurar PHP-FPM:**
```ini
pm = dynamic
pm.max_children = 50
pm.start_servers = 5
pm.min_spare_servers = 5
pm.max_spare_servers = 35
```

**Configurar OPcache:**
```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=2
opcache.fast_shutdown=1
```

---

## 📊 **PARTE 4: Monitoramento e Manutenção**

### 4.1 Logs

```bash
# Logs da aplicação
tail -f storage/logs/laravel.log

# Logs do servidor
tail -f /var/log/apache2/error.log
```

### 4.2 Backup

**Script de backup automático:**
```bash
#!/bin/bash
# backup-daily.sh

DATE=$(date +%Y%m%d_%H%M%S)
BACKUP_DIR="/backups/forcing"
DB_NAME="forcing_sistema"

# Criar diretório se não existir
mkdir -p $BACKUP_DIR

# Backup do banco
mysqldump $DB_NAME > $BACKUP_DIR/db_backup_$DATE.sql

# Backup dos arquivos
tar -czf $BACKUP_DIR/files_backup_$DATE.tar.gz /var/www/html/forcing-api

# Manter apenas últimos 7 dias
find $BACKUP_DIR -name "*.sql" -mtime +7 -delete
find $BACKUP_DIR -name "*.tar.gz" -mtime +7 -delete

echo "Backup concluído: $DATE"
```

### 4.3 Monitoramento

**Health Check automático:**
```bash
#!/bin/bash
# health-check.sh

API_URL="https://api.seu-dominio.com/api/health"
RESPONSE=$(curl -s -o /dev/null -w "%{http_code}" $API_URL)

if [ $RESPONSE -ne 200 ]; then
    echo "API não está respondendo: $RESPONSE"
    # Enviar notificação
    # curl -X POST "https://hooks.slack.com/services/..." -d '{"text":"API Forcing offline"}'
fi
```

---

## 🎯 **PARTE 5: Funcionalidades da Aplicação Mobile**

### 5.1 Telas Implementadas

- ✅ **Login** - Autenticação JWT
- ✅ **Dashboard** - Estatísticas em tempo real
- ✅ **Lista de Forcings** - Com filtros avançados
- ✅ **Detalhes do Forcing** - Visualização completa
- ✅ **Criar Forcing** - Formulário responsivo
- ✅ **Perfil** - Dados do usuário

### 5.2 Funcionalidades por Perfil

**👤 Usuário:**
- Criar forcings
- Visualizar lista
- Editar próprios forcings
- Solicitar retirada

**🔓 Liberador:**
- Todas funcionalidades de usuário
- Liberar forcings
- Adicionar observações

**⚙️ Executante:**
- Registrar execução
- Informar local de execução
- Retirar forcings

**👑 Admin:**
- Todas funcionalidades
- Excluir forcings
- Gerenciar usuários

---

## 🚀 **PARTE 6: Deploy da Aplicação Mobile**

### 6.1 Build para Produção

```bash
# Android
expo build:android

# iOS
expo build:ios

# Ou usando EAS Build (recomendado)
npm install -g @expo/eas-cli
eas build --platform android
eas build --platform ios
```

### 6.2 Publicar nas Stores

**Google Play Store:**
1. Criar conta de desenvolvedor
2. Upload do APK/AAB
3. Configurar metadados
4. Publicar

**Apple App Store:**
1. Conta Apple Developer
2. Upload via Xcode/Transporter
3. Configurar App Store Connect
4. Submeter para revisão

---

## 📋 **Checklist Final**

### ✅ API Backend
- [ ] Deploy no CloudPanel
- [ ] SSL/HTTPS configurado
- [ ] JWT funcionando
- [ ] Banco MySQL configurado
- [ ] CORS configurado
- [ ] Logs funcionando

### ✅ Aplicação Mobile
- [ ] Expo configurado
- [ ] URL da API atualizada
- [ ] Login funcionando
- [ ] Todas as telas funcionais
- [ ] Testes em dispositivos reais

### ✅ Produção
- [ ] Backup configurado
- [ ] Monitoramento ativo
- [ ] Documentação atualizada
- [ ] Treinamento da equipe

---

## 🆘 **Suporte e Troubleshooting**

### Problemas Comuns

**1. API não responde:**
```bash
# Verificar logs
tail -f storage/logs/laravel.log

# Verificar permissões
chmod -R 755 storage bootstrap/cache
```

**2. Erro de CORS:**
```bash
# Verificar configuração CORS
nano config/cors.php
```

**3. Token JWT inválido:**
```bash
# Regenerar secret
php artisan jwt:secret --force
```

**4. App mobile não conecta:**
```typescript
// Verificar URL no api.ts
const API_BASE_URL = 'https://api.seu-dominio.com/api/v1';
```

### Contatos de Suporte

- 📧 **Email:** suporte@seu-dominio.com
- 📱 **WhatsApp:** +55 11 99999-9999
- 📚 **Documentação:** https://docs.seu-dominio.com

---

## 🎉 **Conclusão**

Seu sistema de Forcing agora está completo com:

- ✅ **API REST** hospedada no CloudPanel
- ✅ **Aplicação Mobile** nativa para iOS/Android
- ✅ **Sistema Multi-tenant** funcionando
- ✅ **Autenticação JWT** segura
- ✅ **Interface responsiva** e intuitiva
- ✅ **Monitoramento** e backup configurados

**Próximos passos sugeridos:**
1. Implementar notificações push
2. Adicionar relatórios em PDF
3. Integrar com sistemas externos
4. Implementar auditoria completa

**Boa sorte com seu sistema! 🚀**

