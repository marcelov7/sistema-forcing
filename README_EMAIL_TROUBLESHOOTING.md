# 🚨 EMAILS NÃO FUNCIONAM NO DEPLOY? - GUIA RÁPIDO

## 🎯 PROBLEMA MAIS COMUM
**MAIL_MAILER=log** no .env de produção (emails vão para log, não são enviados)

## ⚡ SOLUÇÃO RÁPIDA (5 passos)

### 1️⃣ **VERIFICAR .ENV**
```bash
# SSH no servidor
ssh devaxis-forcing@31.97.168.137
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# Verificar configurações
grep MAIL_ .env
```

### 2️⃣ **CORRIGIR .ENV**
```env
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=587
MAIL_USERNAME=sistema@devaxis.com.br
MAIL_PASSWORD=SuaSenhaAqui
MAIL_ENCRYPTION=tls
MAIL_FROM_ADDRESS=sistema@devaxis.com.br
MAIL_FROM_NAME="Sistema de Controle de Forcing"
```

### 3️⃣ **LIMPAR CACHE**
```bash
php artisan config:clear
php artisan cache:clear
```

### 4️⃣ **TESTAR**
```bash
# Comando de teste
php artisan forcing:testar-emails

# Ou via navegador
https://forcing.devaxis.com.br/verificar-email-deploy.php
```

### 5️⃣ **CONFIRMAR**
- ✅ Criar um forcing de teste
- ✅ Verificar se email chega na caixa de entrada

## 🔍 DIAGNÓSTICO DETALHADO

### Via Comando:
```bash
php artisan forcing:testar-emails --tipo=config
```

### Via Web:
```
https://forcing.devaxis.com.br/verificar-email-deploy.php
```

## 📋 CHECKLIST DE VERIFICAÇÃO

- [ ] MAIL_MAILER = smtp (não log)
- [ ] MAIL_HOST configurado (não 127.0.0.1)
- [ ] MAIL_USERNAME e MAIL_PASSWORD corretos
- [ ] MAIL_PORT = 587 ou 465
- [ ] MAIL_ENCRYPTION = tls ou ssl
- [ ] Cache limpo após alterações

## 🆘 SE AINDA NÃO FUNCIONAR

1. **Verifique logs:**
   ```bash
   tail -n 50 storage/logs/laravel.log | grep -i mail
   ```

2. **Teste conexão SMTP:**
   ```bash
   php artisan forcing:testar-emails --tipo=smtp
   ```

3. **Confirme credenciais no painel da Hostinger**

4. **Tente porta/encryption diferentes:**
   - Porta 587 + TLS
   - Porta 465 + SSL

## 📞 CONTATOS

- **Hostinger Support** (se problema for SMTP)
- **Verificar spam/lixo eletrônico**
- **Confirmar MX records do domínio**

---

**💡 DICA:** Execute sempre `php artisan forcing:testar-emails` após qualquer alteração! 