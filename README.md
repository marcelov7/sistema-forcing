# Sistema de Controle de Forcing

Sistema desenvolvido em Laravel para controle e gerenciamento de forcing operacionais, permitindo que usuários criem, acompanhem e liberadores/administradores gerenciem o status dos forcing.

## 🚀 Funcionalidades

### Para Usuários (Perfil: user)
- ✅ Criar novos forcing
- ✅ Visualizar lista completa de forcing
- ✅ Editar forcing próprios (até serem retirados)
- ✅ Acompanhar status com timeline visual
- ✅ Solicitar retirada de qualquer forcing

### Para Liberadores (Perfil: liberador)
- ✅ Todas as funcionalidades de usuário
- ✅ Criar e liberar forcing
- ✅ Liberar forcing pendentes
- ✅ Adicionar observações na liberação

### Para Executantes (Perfil: executante)
- ✅ Todas as funcionalidades de usuário (exceto liberar)
- ✅ Criar forcing (mas não pode liberar)
- ✅ Registrar execução de forcing liberados
- ✅ Informar local de execução (Supervisório, PLC, Local)
- ✅ Retirar forcing após solicitação
- ✅ Adicionar resolução do problema

### Para Administradores (Perfil: admin)
- ✅ Todas as funcionalidades de liberador e executante
- ✅ Gerenciar usuários do sistema
- ✅ Criar/editar/excluir usuários
- ✅ Alterar perfis de usuários
- ✅ Excluir forcing do sistema (exceto retirados)

## 📋 Pré-requisitos

- PHP 8.2 ou superior
- Composer
- SQLite (já configurado) ou MySQL/PostgreSQL
- Servidor web (Apache/Nginx ou built-in do PHP)

## 🔧 Instalação

1. **Clone o repositório** (se aplicável) ou navegue até o diretório:
   ```bash
   cd C:\xampp\htdocs\Forcing
   ```

2. **Instale as dependências:**
   ```bash
   composer install
   ```

3. **Configure o ambiente:**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

4. **Execute as migrations:**
   ```bash
   php artisan migrate
   ```

5. **Execute os seeders para criar usuários iniciais:**
   ```bash
   php artisan db:seed
   ```

6. **Inicie o servidor:**
   ```bash
   php artisan serve
   ```

7. **Acesse o sistema:**
   - URL: http://localhost:8000
   - Será redirecionado para a tela de login

## 👥 Usuários Padrão

O sistema vem com 4 usuários pré-cadastrados para teste:

### Administrador
- **Usuário:** admin
- **Senha:** admin123
- **Perfil:** Admin
- **Empresa:** Sistema
- **Setor:** TI

### Liberador
- **Usuário:** liberador
- **Senha:** liberador123
- **Perfil:** Liberador
- **Empresa:** Empresa Exemplo
- **Setor:** Operações

### Executante
- **Usuário:** executante
- **Senha:** executante123
- **Perfil:** Executante
- **Empresa:** Empresa Exemplo
- **Setor:** Manutenção

### Usuário Comum
- **Usuário:** usuario
- **Senha:** usuario123
- **Perfil:** User
- **Empresa:** Empresa Exemplo
- **Setor:** Produção

## 📊 Estrutura do Banco de Dados

### Tabela: users
- id, name, email, username, password
- empresa, setor, perfil (user/liberador/executante/admin)
- timestamps

### Tabela: forcing
- id, tag, descricao_equipamento, situacao_equipamento, area
- status (pendente/liberado/forcado/solicitacao_retirada/retirado)
- user_id (criador), liberador_id, executante_id, retirado_por_id, solicitado_retirada_por_id
- data_forcing, data_liberacao, data_execucao, data_retirada, data_solicitacao_retirada
- local_execucao (supervisorio/plc/local), status_execucao (pendente/executado)
- observacoes, observacoes_execucao, descricao_resolucao
- timestamps

## 🎯 Como Usar

### 1. Login no Sistema
- Acesse a URL do sistema
- Use um dos usuários padrão ou registre-se

### 2. Criar um Forcing
- Clique em "Novo Forcing"
- Preencha título e descrição
- O forcing será criado com status "Forçado"

### 3. Gerenciar Forcing
- **Usuários:** Podem ver todos os forcing e editar os próprios
- **Liberadores:** Podem retirar forcing ativos e forçar novamente
- **Executantes:** Podem registrar execução informando onde foi feito (Supervisório, PLC ou Local)
- **Administradores:** Têm controle total do sistema

### 4. Dashboard
- Lista todos os forcing com informações completas
- Filtros visuais por status (Forçado/Retirado)
- Status de execução (Pendente/Executado)
- Informações de local de execução
- Ações rápidas para cada perfil

## 🔐 Controle de Acesso

### Perfis de Usuário
1. **user**: Acesso básico para criar e visualizar forcing
2. **liberador**: Pode gerenciar status dos forcing (retirar/forçar)
3. **executante**: Pode registrar execução dos forcing
4. **admin**: Controle total do sistema e usuários

### Middleware de Segurança
- Autenticação obrigatória para todas as funcionalidades
- Verificação de perfil para ações específicas
- Proteção contra acesso não autorizado

## 🛠️ Tecnologias Utilizadas

- **Backend:** Laravel 12.x
- **Frontend:** Blade Templates + Bootstrap 5
- **Banco:** SQLite (padrão) - configurável para MySQL/PostgreSQL
- **Autenticação:** Laravel Auth com middleware customizado
- **Icons:** Font Awesome 6
- **Styling:** Bootstrap 5.3

## 📱 Interface

- **Design responsivo** com Bootstrap 5
- **Navegação intuitiva** com menu baseado no perfil do usuário
- **Feedback visual** com alertas e badges de status
- **Modais** para confirmações e formulários
- **Tabelas responsivas** para listagem de dados

## 🚀 Próximas Funcionalidades

- [ ] Sistema de notificações
- [ ] Relatórios e exportação
- [ ] Histórico detalhado de alterações
- [ ] API REST para integração
- [ ] Dashboard com gráficos
- [ ] Sistema de anexos
- [ ] Filtros avançados por data, status, etc.
- [ ] Sistema de aprovação em múltiplas etapas

## 📞 Suporte

Para dúvidas ou problemas:
1. Verifique se todas as dependências estão instaladas
2. Confirme se as migrations foram executadas
3. Verifique as permissões do banco de dados
4. Consulte os logs em `storage/logs/laravel.log`

## 🔄 Fluxo do Sistema

### Ciclo Completo de 5 Etapas:
1. **Pendente** - Usuário cria forcing (aguarda liberação)
2. **Liberado** - Liberador aprova o forcing (aguarda execução)
3. **Forçado** - Executante registra a execução (equipamento forçado)
4. **Solicitação de Retirada** - Qualquer usuário solicita retirada
5. **Retirado** - Executante retira o forcing (processo concluído)

### Controles e Permissões:
- **Bloqueio de edição** para forcing concluídos (retirados)
- **Timeline visual** mostrando todo o histórico
- **Banner de notificação** para solicitações de retirada
- **Auditoria completa** com usuários responsáveis
- **Formulários focados** em TAG/equipamento

## 📄 Licença

Este projeto é proprietário e destinado ao controle interno de forcing operacionais.

---

**Desenvolvido com ❤️ usando Laravel**

---

## 🔑 **CONTAS PARA TESTAR O SISTEMA MULTI-TENANT**

### **CREDENCIAIS DE ACESSO:**

#### **1. SUPER ADMINISTRADOR** 🌟
- **Username:** `superadmin`
- **Senha:** `123456789`  
- **Email:** `superadmin@smc.com.br`
- **Funcionalidades:**
  - Acesso total ao sistema
  - Gerenciamento de todas as unidades
  - Visualização de forcings de todas as unidades
  - Criação e gerenciamento de unidades
  - **⚠️ ÚNICO NO SISTEMA** - Não é possível criar mais Super Admins

#### **2. ADMINISTRADOR** 👨‍💼
- **Username:** `admin`
- **Senha:** `admin123`
- **Email:** `marcelo.vine@gmail.com`
- **Funcionalidades:**
  - Administração da sua unidade
  - Liberação e execução de forcings
  - Gerenciamento de usuários da unidade

#### **3. LIBERADOR** ✅
- **Username:** `liberador`
- **Senha:** `liberador123`
- **Email:** `liberador@forcing.com`
- **Funcionalidades:**
  - Liberação de forcings pendentes
  - Visualização de forcings da sua unidade

#### **4. EXECUTANTE** 🔧
- **Username:** `executante`
- **Senha:** `executante123`
- **Email:** `executante@forcing.com`
- **Funcionalidades:**
  - Execução de forcings liberados
  - Retirada de forcings após solicitação

#### **5. USUÁRIO/SOLICITANTE** 👤
- **Username:** `usuario`
- **Senha:** `usuario123`
- **Email:** `usuario@forcing.com`
- **Funcionalidades:**
  - Criação de novos forcings
  - Solicitação de retirada de forcings
  - Visualização de forcings da sua unidade

### **🏢 UNIDADES DISPONÍVEIS:**
1. **UND001 - Unidade Central** (São Paulo/SP)
2. **UND002 - Unidade Zona Norte** (São Paulo/SP)  
3. **UND003 - Unidade ABC** (Santo André/SP)

### **🌐 ACESSO AO SISTEMA:**
**URL:** http://localhost:8000/login

### **🔄 FLUXO DE TESTE COMPLETO:**
1. **Login como USUÁRIO** → Criar forcing → Aceitar termos
2. **Login como LIBERADOR** → Liberar forcing criado
3. **Login como EXECUTANTE** → Executar forcing liberado
4. **Login como USUÁRIO** → Solicitar retirada do forcing
5. **Login como EXECUTANTE** → Retirar forcing
6. **Login como SUPER ADMIN** → Visualizar todos os forcings e gerenciar unidades

### **🎯 FUNCIONALIDADES MULTI-TENANT:**
- ✅ Usuários veem apenas forcings da sua unidade
- ✅ Super Admin vê forcings de todas as unidades
- ✅ Sistema de termo de responsabilidade (SMC-MAN-PR-014 V.4)
- ✅ Controle de acesso por perfil
- ✅ Gerenciamento de múltiplas unidades

### **🔧 COMANDOS ÚTEIS:**
```bash
# Verificar hierarquia do sistema
php artisan show:hierarchy

# Verificar configuração multi-tenant
php artisan show:multi-tenant-demo

# Criar dados de teste para paginação
php artisan create:test-data {quantidade}

# Limpar dados de teste
php artisan clear:test-data

# Verificar Super Admin
php artisan check:super-admin

# Listar todos os usuários
php artisan users:list

# Criar usuários de teste (se necessário)
php artisan users:create-test

# Definir um usuário como Super Admin
php artisan users:set-super-admin {username}

# Executar migrations
php artisan migrate

# Executar seeders
php artisan db:seed

# Iniciar servidor
php artisan serve
```

### **📄 PAGINAÇÃO IMPLEMENTADA:**
- ✅ **Lista de Forcings**: 15 itens por página
- ✅ **Lista de Usuários**: 20 itens por página  
- ✅ **Lista de Unidades**: 10 itens por página
- ✅ **Preservação de filtros** na navegação
- ✅ **Design responsivo** Bootstrap 4
- ✅ **Performance otimizada** para grandes volumes