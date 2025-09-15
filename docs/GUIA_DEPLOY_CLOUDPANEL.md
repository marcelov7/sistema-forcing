# 🚀 Guia de Deploy CloudPanel - Sistema de Forcing

## ✅ STATUS: PRONTO PARA MYSQL CLOUDPANEL

### 📊 Resumo das Migrações

| # | Migração | Status | Descrição |
|---|----------|--------|-----------|
| 1 | `create_users_table` | ✅ | Usuários com perfis |
| 2 | `create_cache_table` | ✅ | Sistema de cache |
| 3 | `create_jobs_table` | ✅ | Fila de e-mails |
| 4 | `create_forcing_table` | ✅ | Tabela principal |
| 5 | `add_execution_fields` | ✅ | Campos de execução |
| 6 | `update_forcing_status_flow` | ✅ | Fluxo de status |
| 7 | `add_retirada_status` | ✅ | Status de retirada |
| 8 | `add_retirada_fields` | ✅ | Campos de retirada |
| 9 | `add_equipment_fields` | ✅ | Equipamentos |
| 10 | `update_forcing_form_fields` | ✅ | Campos do form |
| 11 | `add_notification_fields` | ✅ | Notificações |

**Total**: 11 migrações ✅ Todas compatíveis com MySQL

---

## 🛠️ Processo de Deploy

### 1️⃣ **Preparar CloudPanel**

#### No painel CloudPanel:
1. **Criar banco MySQL**:
   - Nome: `forcing_sistema` (exemplo)
   - Usuário: `forcing_user` (exemplo)
   - Senha: `senha_segura` (definir)

2. **Configurar domínio**:
   - Adicionar domínio/subdomínio
   - Configurar SSL (Let's Encrypt)

3. **Upload dos arquivos**:
   - Fazer upload do projeto para pasta do domínio
   - Ou usar Git: `git clone seu-repositorio.git`

### 2️⃣ **Configurar Sistema**

#### Configurar .env:
```bash
# Copiar template
cp .env.cloudpanel .env

# Editar credenciais MySQL
nano .env
```

Configurar no .env:
```env
DB_DATABASE=forcing_sistema
DB_USERNAME=forcing_user  
DB_PASSWORD=sua_senha_mysql
```

#### Executar deploy:
```bash
# Dar permissão ao script
chmod +x deploy-cloudpanel.sh

# Executar deploy
./deploy-cloudpanel.sh
```

### 3️⃣ **Verificar Sistema**

#### Testar funcionalidades:
- [ ] Acesso ao sistema
- [ ] Login com usuários padrão
- [ ] Criação de forcing
- [ ] Sistema de e-mails
- [ ] Permissões por perfil

---

## 📋 Estrutura Final do Banco

### Tabela `users` (Usuários)
```sql
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `username` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `empresa` varchar(255) NOT NULL,
  `setor` varchar(255) NOT NULL,
  `perfil` enum('user','liberador','executante','admin') NOT NULL DEFAULT 'user',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`),
  UNIQUE KEY `users_username_unique` (`username`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

### Tabela `forcing` (Forcing Principal)
```sql
CREATE TABLE `forcing` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `titulo` varchar(255) NOT NULL,
  `descricao` text,
  `status` enum('pendente','liberado','executado','solicitacao_retirada','retirado') NOT NULL DEFAULT 'pendente',
  `user_id` bigint unsigned NOT NULL,
  `liberador_id` bigint unsigned DEFAULT NULL,
  `executante_id` bigint unsigned DEFAULT NULL,
  `retirado_por_id` bigint unsigned DEFAULT NULL,
  `solicitado_retirada_por` bigint unsigned DEFAULT NULL,
  `equipamento` varchar(255) DEFAULT NULL,
  `sistema` varchar(255) DEFAULT NULL,
  `area` varchar(255) DEFAULT NULL,
  `prioridade` enum('baixa','media','alta') NOT NULL DEFAULT 'media',
  `local_execucao` varchar(255) DEFAULT NULL,
  `status_execucao` enum('pendente','executado') NOT NULL DEFAULT 'pendente',
  `data_forcing` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `data_liberacao` timestamp NULL DEFAULT NULL,
  `data_execucao` timestamp NULL DEFAULT NULL,
  `data_retirada` timestamp NULL DEFAULT NULL,
  `data_solicitacao_retirada` timestamp NULL DEFAULT NULL,
  `observacoes` text,
  `observacoes_liberacao` text,
  `observacoes_execucao` text,
  `observacoes_retirada` text,
  `observacoes_solicitacao` text,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `forcing_user_id_foreign` (`user_id`),
  KEY `forcing_liberador_id_foreign` (`liberador_id`),
  KEY `forcing_executante_id_foreign` (`executante_id`),
  KEY `forcing_retirado_por_id_foreign` (`retirado_por_id`),
  KEY `forcing_solicitado_retirada_por_foreign` (`solicitado_retirada_por`),
  CONSTRAINT `forcing_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `forcing_liberador_id_foreign` FOREIGN KEY (`liberador_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forcing_executante_id_foreign` FOREIGN KEY (`executante_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forcing_retirado_por_id_foreign` FOREIGN KEY (`retirado_por_id`) REFERENCES `users` (`id`) ON DELETE SET NULL,
  CONSTRAINT `forcing_solicitado_retirada_por_foreign` FOREIGN KEY (`solicitado_retirada_por`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
```

---

## 🔧 Comandos Úteis

### Verificação:
```bash
# Status das migrações
php artisan migrate:status

# Verificar compatibilidade
php artisan migrate:check-mysql

# Verificar usuários
php artisan users:list

# Status do e-mail
php artisan email:status
```

### Manutenção:
```bash
# Limpar caches
php artisan cache:clear
php artisan config:clear
php artisan route:clear
php artisan view:clear

# Recriar caches
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

### Backup:
```bash
# Backup do banco
mysqldump -u usuario -p nome_banco > backup_forcing.sql

# Restaurar backup
mysql -u usuario -p nome_banco < backup_forcing.sql
```

---

## 🔐 Segurança

### Permissões de Arquivos:
```bash
# Pastas do Laravel
chmod -R 755 storage/
chmod -R 755 bootstrap/cache/
chown -R www-data:www-data storage/
chown -R www-data:www-data bootstrap/cache/

# Proteger .env
chmod 600 .env
```

### Configurações de Produção:
- [ ] `APP_DEBUG=false`
- [ ] `APP_ENV=production`
- [ ] SSL habilitado
- [ ] Senha do MySQL segura
- [ ] Backup automático configurado

---

## 📧 Sistema de E-mail

### Configuração Hostinger:
- ✅ **SMTP**: smtp.hostinger.com:465 (SSL)
- ✅ **Conta**: sistema@devaxis.com.br
- ✅ **Limite**: 85 e-mails/dia (com buffer)
- ✅ **Monitoramento**: Automático

### Notificações Automáticas:
1. **Forcing Criado** → Liberadores
2. **Forcing Liberado** → Executantes
3. **Forcing Executado** → Criador + Liberadores
4. **Solicitação Retirada** → Executantes
5. **Forcing Retirado** → Todos envolvidos

---

## ✅ Checklist Final

### Antes do Deploy:
- [ ] Banco MySQL criado no CloudPanel
- [ ] Credenciais anotadas
- [ ] Domínio configurado
- [ ] SSL configurado

### Durante o Deploy:
- [ ] Arquivos enviados
- [ ] .env configurado
- [ ] Script de deploy executado
- [ ] Migrações aplicadas
- [ ] Usuários criados

### Após o Deploy:
- [ ] Login testado
- [ ] E-mails funcionando
- [ ] Forcing criado com sucesso
- [ ] Notificações sendo enviadas
- [ ] Backups configurados

---

## 🎉 Resultado Final

**Sistema 100% pronto para produção no CloudPanel!**

- ✅ **11 migrações** executadas com sucesso
- ✅ **4 perfis de usuário** funcionais
- ✅ **5 etapas de fluxo** de forcing
- ✅ **Sistema de e-mail** otimizado
- ✅ **Interface responsiva** e moderna
- ✅ **Segurança** implementada
- ✅ **Monitoramento** automático

**Pronto para uso empresarial!** 🚀
