# Configuração de E-mail com Hostinger VPS

## Configurações SMTP da Hostinger

### Servidor de Saída (SMTP)
- **Host**: smtp.hostinger.com
- **Porta**: 465
- **Criptografia**: SSL
- **Autenticação**: Sim

### Servidor de Entrada (IMAP)
- **Host**: imap.hostinger.com
- **Porta**: 993
- **Criptografia**: SSL

### Servidor de Entrada (POP3)
- **Host**: pop.hostinger.com
- **Porta**: 995
- **Criptografia**: SSL

## Configuração no Laravel

### 1. Configurar o arquivo .env

```env
# Configurações de E-mail para Hostinger
MAIL_MAILER=smtp
MAIL_HOST=smtp.hostinger.com
MAIL_PORT=465
MAIL_USERNAME=sistema@seudominio.com
MAIL_PASSWORD=sua_senha_do_email
MAIL_ENCRYPTION=ssl
MAIL_FROM_ADDRESS="sistema@seudominio.com"
MAIL_FROM_NAME="Sistema de Forcing"
```

### 2. Substitua os valores:
- `seudominio.com`: Seu domínio registrado na Hostinger
- `sua_senha_do_email`: A senha da conta de e-mail criada

## Limites da Hostinger VPS

### E-mails por Dia
- **Limite**: 100 e-mails por dia
- **Recomendação**: Usar até 85 e-mails (deixar 15 de buffer)
- **Reset**: Diário às 00:00 UTC

### Monitoramento
O sistema possui comandos para monitorar o uso:

```bash
# Verificar status atual
php artisan email:status

# Testar envio de e-mail
php artisan email:test seuemail@exemplo.com
```

## Vantagens da Configuração Hostinger

### ✅ Benefícios
1. **Incluído no VPS**: Sem custo adicional
2. **SSL/TLS**: Criptografia completa
3. **Confiabilidade**: Boa reputação de entrega
4. **Suporte a IMAP**: Para receber respostas
5. **Integração Simples**: Funciona perfeitamente com Laravel

### ⚠️ Limitações
1. **100 e-mails/dia**: Adequado para operações normais
2. **Dependência do domínio**: Precisa ter domínio próprio

## Criação de Conta de E-mail

### No Painel da Hostinger:
1. Acesse o painel de controle
2. Vá em "E-mails"
3. Crie uma conta: `sistema@seudominio.com`
4. Configure uma senha segura
5. Use essas credenciais no Laravel

## Teste de Configuração

Após configurar, teste o sistema:

```bash
# 1. Verificar configuração
php artisan config:cache

# 2. Testar envio
php artisan email:test seuemail@teste.com

# 3. Verificar logs se houver erro
tail -f storage/logs/laravel.log
```

## Troubleshooting

### Erro de Autenticação
- Verifique usuário e senha
- Confirme se a conta de e-mail existe
- Teste login no webmail da Hostinger

### Erro de Conexão
- Confirme porta 465 com SSL
- Verifique se o firewall permite saída na porta 465
- Teste conectividade: `telnet smtp.hostinger.com 465`

### Limite Excedido
- Use o comando `php artisan email:status`
- O sistema automaticamente gerencia o limite
- Aguarde reset diário se necessário

## Conclusão

A configuração de e-mail da Hostinger é **perfeitamente adequada** para o sistema de forcing:

- ✅ Suporta todas as notificações do sistema
- ✅ Limite de 100 e-mails/dia é suficiente para operações normais
- ✅ Configuração simples e confiável
- ✅ Incluído no plano VPS sem custo adicional
- ✅ Sistema otimizado para gerenciar automaticamente os limites
