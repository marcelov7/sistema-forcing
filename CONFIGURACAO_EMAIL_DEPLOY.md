# 📧 CONFIGURAÇÃO DE EMAIL - DEPLOY CLOUDPANEL

## 🎯 PROBLEMA IDENTIFICADO
Os emails não estão sendo enviados no ambiente de produção (CloudPanel).

## 🔍 DIAGNÓSTICO

### 1️⃣ EXECUTE O SCRIPT DE VERIFICAÇÃO
```
https://forcing.devaxis.com.br/verificar-email-deploy.php
```

Este script irá mostrar:
- ✅ Configurações atuais
- 🔌 Teste de conexão SMTP  
- 📧 Teste de envio
- 📜 Logs de erro
- 💡 Recomendações

## 🛠️ CORREÇÕES NECESSÁRIAS

### 2️⃣ CONFIGURAÇÃO DO .ENV (PRODUÇÃO)

Edite o arquivo `.env` no servidor:

```bash
# Via SSH
ssh devaxis-forcing@31.97.168.137
nano /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/.env
```

**CONFIGURAÇÕES HOSTINGER/CLOUDPANEL:**

```env
# Email Configuration
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=sistema@devaxis.com.br
MAIL_PASSWORD=SuaSenhaAqui
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=sistema@devaxis.com.br
MAIL_FROM_NAME="Sistema de Controle de Forcing"
```

### 3️⃣ CONFIGURAÇÕES ALTERNATIVAS

**Se estiver usando outro provedor:**

```env
# Gmail (para testes)
MAIL_HOST=smtp.gmail.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@gmail.com
MAIL_PASSWORD=senha_aplicativo
MAIL_ENCRYPTION=tls

# Outlook/Hotmail
MAIL_HOST=smtp-mail.outlook.com
MAIL_PORT=587
MAIL_USERNAME=seuemail@outlook.com
MAIL_PASSWORD=suasenha
MAIL_ENCRYPTION=tls

# Outros provedores
MAIL_HOST=mail.seudominio.com
MAIL_PORT=587 (ou 465 para SSL)
MAIL_ENCRYPTION=tls (ou ssl)
```

## 🔧 COMANDOS NO SERVIDOR

### 4️⃣ APÓS ALTERAR O .ENV

```bash
# Limpar cache de configuração
php artisan config:clear
php artisan cache:clear

# Verificar se as configurações foram aplicadas
php artisan config:show mail
```

### 5️⃣ TESTAR ENVIO DE EMAIL

```bash
# Via Artisan Tinker
php artisan tinker

# No tinker, execute:
Mail::raw('Teste de email', function($m) {
    $m->to('seu@email.com')->subject('Teste');
});
```

## 🔍 PROBLEMAS COMUNS

### ❌ MAIL_MAILER=log
**Problema:** Emails sendo salvos em log em vez de enviados
**Solução:** Alterar para `MAIL_MAILER=smtp`

### ❌ Host 127.0.0.1 ou localhost
**Problema:** Tentativa de envio local
**Solução:** Usar servidor SMTP real (ex: smtp.hostinger.com)

### ❌ Porta 2525
**Problema:** Porta de desenvolvimento
**Solução:** Usar porta 587 (TLS) ou 465 (SSL)

### ❌ Credenciais incorretas
**Problema:** Username/password inválidos
**Solução:** Verificar no painel do provedor

### ❌ Sem encryption
**Problema:** Conexão insegura
**Solução:** Usar TLS ou SSL

## 📋 CHECKLIST DE VERIFICAÇÃO

- [ ] MAIL_MAILER = smtp
- [ ] MAIL_HOST configurado com servidor real
- [ ] MAIL_PORT = 587 ou 465
- [ ] MAIL_USERNAME e MAIL_PASSWORD configurados
- [ ] MAIL_ENCRYPTION = tls ou ssl
- [ ] MAIL_FROM_ADDRESS com email válido
- [ ] Cache limpo após alterações
- [ ] Teste de envio realizado

## 🚨 CONFIGURAÇÃO ESPECÍFICA HOSTINGER

### Para domínio devaxis.com.br:

1. **Criar conta de email no cPanel:**
   - Acesse o cPanel da Hostinger
   - Vá em "Contas de Email"
   - Crie: `sistema@devaxis.com.br`

2. **Configurações SMTP:**
   ```env
   MAIL_HOST=smtp.hostinger.com
   MAIL_PORT=587
   MAIL_USERNAME=sistema@devaxis.com.br
   MAIL_PASSWORD=senha_criada_no_cpanel
   MAIL_ENCRYPTION=tls
   ```

3. **Verificar DNS:**
   - MX records devem apontar para Hostinger
   - SPF record recomendado

## 🔄 FLUXO DE EMAILS NO SISTEMA

### Emails Enviados:
1. **Forcing Criado** → Para liberadores
2. **Forcing Liberado** → Para executantes  
3. **Forcing Executado** → Para liberadores
4. **Solicitação de Retirada** → Para executantes
5. **Forcing Retirado** → Para criador e liberadores

### Verificar se está funcionando:
1. Criar um forcing
2. Verificar se liberadores receberam email
3. Liberar o forcing
4. Verificar se executantes receberam email

## 📞 SUPORTE

Se o problema persistir:

1. **Execute o diagnóstico:** `verificar-email-deploy.php`
2. **Copie os logs de erro**
3. **Verifique com provedor de hospedagem**
4. **Considere usar serviço externo** (SendGrid, Mailgun, etc.)

---

**📅 Atualizado em:** $(date)  
**🏆 Versão:** Deploy CloudPanel v1.0 