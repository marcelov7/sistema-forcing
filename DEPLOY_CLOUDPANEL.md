# 🚀 GUIA DE DEPLOY - CLOUDPANEL
## Sistema de Controle de Forcing - Versão Atualizada

### 📋 CHECKLIST PRÉ-DEPLOY
- [x] Arquivos de teste removidos
- [x] Sistema funcionando localmente
- [x] Emails configurados e testados
- [x] Autorização de retirada funcionando
- [x] Links dos emails direcionando corretamente

---

## 🔧 CONFIGURAÇÕES IMPORTANTES NO .ENV

### 📧 **EMAIL SMTP (OBRIGATÓRIO):**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=sistema@devaxis.com.br
MAIL_PASSWORD=M@rcelo1809@3033
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="sistema@devaxis.com.br"
MAIL_FROM_NAME="Sistema de Controle de Forcing"
```

### 🗄️ **BANCO DE DADOS:**
```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=nome_do_banco
DB_USERNAME=usuario_db
DB_PASSWORD=senha_db
```

### ⚙️ **APLICAÇÃO:**
```env
APP_NAME="Sistema de Controle de Forcing"
APP_ENV=production
APP_KEY=base64:GERAR_NOVA_CHAVE
APP_DEBUG=false
APP_URL=https://seudominio.com.br

ASSET_URL=https://seudominio.com.br
```

### 🔄 **FILAS (RECOMENDADO):**
```env
QUEUE_CONNECTION=database
```

---

## 📋 COMANDOS APÓS UPLOAD NO CLOUDPANEL

### 1️⃣ **Gerar chave da aplicação:**
```bash
php artisan key:generate
```

### 2️⃣ **Executar migrações:**
```bash
php artisan migrate --force
```

### 3️⃣ **Limpar todos os caches:**
```bash
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear
```

### 4️⃣ **Otimizar para produção:**
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### 5️⃣ **Configurar permissões:**
```bash
chmod -R 775 storage
chmod -R 775 bootstrap/cache
```

### 6️⃣ **🔧 DIAGNÓSTICO DE EMAILS (IMPORTANTE):**
```bash
# Testar todas as configurações de email
php artisan forcing:testar-emails

# Ou testar partes específicas:
php artisan forcing:testar-emails --tipo=config   # Apenas configurações
php artisan forcing:testar-emails --tipo=smtp     # Apenas conexão SMTP
php artisan forcing:testar-emails --tipo=envio    # Apenas envio simples
php artisan forcing:testar-emails --tipo=fluxo    # Apenas fluxo do sistema
```

### 7️⃣ **🌐 DIAGNÓSTICO VIA WEB:**
Acesse: `https://seudominio.com.br/verificar-email-deploy.php`
- Verifica todas as configurações
- Testa conexão SMTP
- Envia email de teste
- Mostra logs de erro
- Fornece recomendações

---

## 🔍 VERIFICAÇÕES IMPORTANTES

### ✅ **Teste de Email:**
1. **Execute primeiro:** `php artisan forcing:testar-emails`
2. **Ou acesse:** `verificar-email-deploy.php` no navegador
3. Criar um forcing de teste
4. Verificar se o email é enviado
5. Clicar nos links do email para testar redirecionamento
6. Confirmar que filtra e destaca o forcing correto

### 🚨 **Se emails não funcionarem:**
1. Verifique se `MAIL_MAILER=smtp` (não `log`)
2. Confirme credenciais SMTP no .env
3. Execute `php artisan config:clear`
4. Teste novamente com `php artisan forcing:testar-emails`

### ✅ **Teste de Autorização:**
1. Testar retirada com usuário executante
2. Verificar se admin pode editar observações de liberação
3. Confirmar que apenas liberador responsável pode editar suas observações

### ✅ **Funcionalidades Principais:**
- [ ] Criação de forcing
- [ ] Liberação de forcing  
- [ ] Execução de forcing
- [ ] Solicitação de retirada
- [ ] Retirada final
- [ ] Envio de emails em cada etapa
- [ ] Links dos emails funcionando

---

## 📧 FLUXO DE EMAILS IMPLEMENTADO

### 🔄 **Ciclo Completo:**
1. **CRIADO** → Email para liberadores
2. **LIBERADO** → Email para criador e executantes  
3. **EXECUTADO** → Email para criador, liberador e executante
4. **SOLICITAÇÃO RETIRADA** → Email para executantes e admins
5. **RETIRADO** → Email para criador, liberador e executante

### 🔗 **Links nos Emails:**
- **Botão Principal:** "Ver Forcing na Lista" → Filtra e destaca o forcing
- **Botão Secundário:** "Ver Detalhes Completos" → Página de detalhes

---

## 🆕 NOVAS FUNCIONALIDADES ADICIONADAS

### ✅ **Autorização Aprimorada:**
- Executantes podem retirar forcings (além de liberadores e admins)
- Apenas liberador responsável pode editar observações de liberação
- Admins mantêm acesso total

### ✅ **Links Inteligentes nos Emails:**
- Direcionamento direto para o forcing específico
- Filtro automático pela TAG
- Destaque visual com borda amarela pulsante
- Mensagem de confirmação de origem do email

### ✅ **Configuração SMTP Corrigida:**
- Linha `encryption` adicionada no config/mail.php
- Emails enviando com sucesso via Hostinger

---

## 🚨 PONTOS DE ATENÇÃO

### ⚠️ **Migrations:**
- A migration `add_deleted_at_to_forcing_table.php` deve ser executada
- Verificar se todas as colunas necessárias existem no banco

### ⚠️ **Permissões:**
- Storage deve ter permissão de escrita para logs
- Bootstrap/cache deve ter permissão para cache de views

### ⚠️ **Email:**
- Verificar se o servidor permite conexão SMTP na porta 465
- Testar envio real após deploy

---

## 🎯 URLS IMPORTANTES PARA TESTE

### 📝 **Funcionalidades:**
- `/forcing` - Lista de forcings
- `/forcing/create` - Criar novo forcing
- `/forcing/{id}` - Detalhes do forcing
- `/forcing/from-email/{id}` - Link vindo do email (NOVO)

### 🔧 **Administrativo:**
- `/users` - Gerenciar usuários (apenas admin)
- `/admin/email-stats` - Estatísticas de email

---

## 🏁 STATUS FINAL

### ✅ **100% IMPLEMENTADO:**
- [x] Autorização para executantes retirarem forcings
- [x] Sistema de emails completo e funcional
- [x] Links inteligentes nos emails
- [x] Filtro e destaque automático do forcing
- [x] Configuração SMTP corrigida
- [x] Notificações em todas as etapas do processo

### 🎉 **PRONTO PARA PRODUÇÃO!**

**O sistema está completamente funcional e pronto para uso em produção. Todas as funcionalidades foram testadas e validadas.**
