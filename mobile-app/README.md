# 📱 Sistema de Forcing Mobile

Aplicação mobile nativa para o Sistema de Controle de Forcing, desenvolvida com React Native + Expo.

## 🚀 Início Rápido

### Pré-requisitos

- Node.js 18+
- Expo CLI
- Expo Go (app no celular)

### Instalação

```bash
# Instalar dependências
npm install

# Configurar URL da API
# Edite o arquivo src/services/api.ts e atualize:
# const API_BASE_URL = 'https://seu-dominio.com/api/v1';

# Iniciar aplicação
expo start
```

### Usar no Celular

1. **Instale o Expo Go:**
   - Android: [Google Play Store](https://play.google.com/store/apps/details?id=host.exp.exponent)
   - iOS: [App Store](https://apps.apple.com/app/expo-go/id982107779)

2. **Escaneie o QR Code** que aparece no terminal

3. **Faça login** com suas credenciais do sistema

## 📱 Funcionalidades

### 🔐 Autenticação
- Login seguro com JWT
- Logout automático
- Refresh de token

### 📊 Dashboard
- Estatísticas em tempo real
- Cards visuais com status
- Ações rápidas

### 📋 Forcings
- Lista completa com filtros
- Visualização detalhada
- Criação de novos forcings
- Edição (conforme permissão)

### 👤 Perfis de Usuário
- **Usuário:** Criar e editar forcings
- **Liberador:** Liberar forcings pendentes
- **Executante:** Registrar execução e retirar
- **Admin:** Controle total

## 🛠️ Desenvolvimento

### Estrutura do Projeto

```
src/
├── components/          # Componentes reutilizáveis
├── contexts/           # Context API (Auth)
├── hooks/              # Custom hooks
├── navigation/         # Configuração de navegação
├── screens/            # Telas da aplicação
├── services/           # API e serviços
└── types/              # Tipos TypeScript
```

### Scripts Disponíveis

```bash
# Desenvolvimento
npm start              # Iniciar Expo
npm run android        # Executar no Android
npm run ios           # Executar no iOS
npm run web           # Executar no navegador

# Build
expo build:android    # Build Android
expo build:ios        # Build iOS
```

## 🔧 Configuração

### Variáveis de Ambiente

Crie um arquivo `.env` na raiz:

```env
API_BASE_URL=https://api.seu-dominio.com/api/v1
APP_VERSION=1.0.0
```

### Configurar API

Edite `src/services/api.ts`:

```typescript
const API_BASE_URL = 'https://seu-dominio.com/api/v1';
```

## 📦 Build para Produção

### Android

```bash
# Usando EAS Build (recomendado)
npm install -g @expo/eas-cli
eas build --platform android

# Ou build local
expo build:android
```

### iOS

```bash
# Usando EAS Build
eas build --platform ios

# Ou build local (requer macOS)
expo build:ios
```

## 🐛 Troubleshooting

### Problemas Comuns

**1. Erro de conexão com API:**
- Verifique se a URL está correta em `src/services/api.ts`
- Confirme se o servidor está rodando
- Verifique se o CORS está configurado

**2. Token JWT inválido:**
- Faça logout e login novamente
- Verifique se o token não expirou

**3. App não carrega:**
- Limpe o cache: `expo start -c`
- Reinstale dependências: `rm -rf node_modules && npm install`

### Logs

```bash
# Ver logs do Expo
expo logs

# Logs específicos do dispositivo
npx react-native log-android  # Android
npx react-native log-ios      # iOS
```

## 📚 Documentação

- [Expo Documentation](https://docs.expo.dev/)
- [React Navigation](https://reactnavigation.org/)
- [NativeBase](https://nativebase.io/)
- [React Query](https://tanstack.com/query/latest)

## 🤝 Contribuição

1. Fork o projeto
2. Crie uma branch: `git checkout -b feature/nova-funcionalidade`
3. Commit: `git commit -m 'Adiciona nova funcionalidade'`
4. Push: `git push origin feature/nova-funcionalidade`
5. Abra um Pull Request

## 📄 Licença

Este projeto está sob a licença MIT. Veja o arquivo `LICENSE` para mais detalhes.

## 🆘 Suporte

- 📧 Email: suporte@seu-dominio.com
- 📱 WhatsApp: +55 11 99999-9999
- 🐛 Issues: [GitHub Issues](https://github.com/seu-usuario/forcing-mobile/issues)
