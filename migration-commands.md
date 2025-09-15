# Comandos para Migração Manual - CloudPanel

Execute estes comandos em sequência no **Command Prompt (cmd)** ou **PowerShell externo**:

## 1. Conectar ao servidor via SSH
```bash
ssh root@31.97.168.137
```

## 2. Comandos no servidor após conectar

### Criar diretório para o site
```bash
mkdir -p /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
```

### Instalar dependências no servidor
```bash
# Atualizar repositórios
apt update

# Instalar Composer se não estiver instalado
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Instalar PHP e extensões necessárias
apt install -y php8.2 php8.2-mbstring php8.2-xml php8.2-zip php8.2-pdo php8.2-sqlite3 php8.2-curl php8.2-gd
```

## 3. Transferir arquivos (Execute do seu computador local)

### Opção A: Usando SCP
```bash
# Do diretório c:\xampp\htdocs\Forcing, execute:
scp -r . root@31.97.168.137:/home/devaxis-forcing/htdocs/forcing.devaxis.com.br/
```

### Opção B: Usando rsync (se disponível)
```bash
rsync -avz --progress . root@31.97.168.137:/home/devaxis-forcing/htdocs/forcing.devaxis.com.br/
```

## 4. Comandos no servidor após transferência

### Configurar permissões
```bash
cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br
chown -R devaxis-forcing:devaxis-forcing .
chmod -R 755 .
chmod -R 775 storage bootstrap/cache
```

### Instalar dependências do Composer
```bash
composer install --no-dev --optimize-autoloader
```

### Configurar ambiente
```bash
cp .env.example .env
php artisan key:generate
```

### Executar migrations
```bash
php artisan migrate --force
```

### Executar seeders (usuários padrão)
```bash
php artisan db:seed --force
```

### Configurar cache
```bash
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

## 5. Configuração do Apache/Nginx no CloudPanel

### Configurar Document Root no CloudPanel:
- Vá para: Sites > forcing.devaxis.com.br > Settings
- Document Root: `/home/devaxis-forcing/htdocs/forcing.devaxis.com.br/public`

### Configurar SSL (Let's Encrypt):
- Vá para: Sites > forcing.devaxis.com.br > SSL/TLS
- Ativar Let's Encrypt

## 6. Testar acesso
```
https://forcing.devaxis.com.br
```

## Usuários padrão para teste:
- **Super Admin**: superadmin / 123456789
- **Admin**: admin / admin123
- **Liberador**: liberador / liberador123
- **Executante**: executante / executante123
- **Usuário**: usuario / usuario123

---

## Troubleshooting

### Se der erro de permissão:
```bash
chmod -R 775 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage
chmod -R 775 /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/bootstrap/cache
```

### Se der erro de banco de dados:
```bash
php artisan migrate:fresh --seed --force
```

### Verificar logs:
```bash
tail -f /home/devaxis-forcing/htdocs/forcing.devaxis.com.br/storage/logs/laravel.log
```
