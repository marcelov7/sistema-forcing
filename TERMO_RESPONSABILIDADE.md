# TERMO DE RESPONSABILIDADE - SISTEMA FORCING
## Baseado no Procedimento SMC-MAN-PR-014 V.4

## 📋 Visão Geral

O Sistema de Termo de Responsabilidade foi implementado seguindo rigorosamente as diretrizes do **SMC-MAN-PR-014 - CONTROLE DE FORCING V.4**, garantindo compliance total com os procedimentos internos da empresa para alterações em PLC e Supervisório.

## 🔧 Funcionalidades Atualizadas

### 1. **Termo Baseado em Procedimento Oficial**
- **Referência**: SMC-MAN-PR-014 V.4 (Publicação: 03/05/2024)
- **Objetivo**: Controle de alterações em PLC/Supervisório para integridade física
- **Compliance**: Total alinhamento com procedimentos da empresa

### 2. **Conteúdo Atualizado do Termo**
- **Identificação do SOLICITANTE**: Conforme definições do procedimento
- **Responsabilidades Específicas**: Baseadas no SMC-MAN-PR-014
- **Tipos de Forcing**: Processo e Manutenção conforme procedimento
- **Hierarquia de Autorização**: Conforme organograma oficial
- **Fluxo Obrigatório**: Nota SAP M2 → Sistema → Autorização → Execução → Retirada

### 3. **Registro Aprimorado de Aceites**
- **Versão do Procedimento**: SMC-MAN-PR-014 V.4
- **Resumo do Procedimento**: Registrado para auditoria
- **Dados Técnicos**: IP, User Agent, timestamp preciso
- **Rastreabilidade**: Compliance total para auditoria

## 🚀 Fluxo Atualizado Conforme SMC-MAN-PR-014

```
1. Usuário (SOLICITANTE) clica em "Novo Forcing"
   ↓
2. Sistema exibe termo baseado em SMC-MAN-PR-014 V.4
   ↓
3. Usuário aceita responsabilidades conforme procedimento
   ↓
4. Sistema registra aceite com versão do procedimento
   ↓
5. Usuário preenche formulário (integração futura com SAP M2)
   ↓
6. Fluxo segue conforme SMC-MAN-PR-014: Autorização → Execução → Retirada
```

## 📄 Principais Atualizações

### Responsabilidades do SOLICITANTE (SMC-MAN-PR-014)
- ✅ **Análise de Segurança**: Riscos pessoais, patrimoniais e de processo
- ✅ **Justificativa Técnica**: Motivos justificados obrigatórios
- ✅ **Área de Competência**: Apenas dentro da responsabilidade do setor
- ✅ **Comunicação Prévia**: Informar operador antes da execução
- ✅ **Acompanhamento**: Monitorar até retirada completa

### Obrigações e Restrições
- ❌ **Execução Proibida**: Apenas técnicos de manutenção elétrica
- ❌ **Autorização Obrigatória**: Conforme hierarquia definida
- ❌ **Prazo de Validade**: Definição obrigatória para retirada
- ❌ **Registro SAP**: Nota M2 obrigatória conforme procedimento

### Tipos de Forcing (SMC-MAN-PR-014)
- **🏭 FORCING DE PROCESSO**: Pressões, vazões, temperaturas, sensores
- **🔧 FORCING DE MANUTENÇÃO**: Proteções, relés, botoeiras, chaves

### Hierarquia de Autorização
- **FORCING DE PRODUÇÃO**: Gerente Industrial, Coordenador Produção
- **FORCING DE MANUTENÇÃO**: Gerente/Coordenadores de Manutenção

## 🔍 Auditoria Aprimorada

### Novos Campos Registrados
- **procedure_version**: SMC-MAN-PR-014 V.4
- **procedure_summary**: Resumo do procedimento aplicado
- **Compliance**: Rastreabilidade total conforme normas

### Comando de Auditoria Atualizado
```bash
# Relatório completo com versão do procedimento
php artisan terms:check

# Exemplo de saída:
ID | Usuário | Email | Data/Hora | Procedimento | IP | Navegador
1  | João    | j@... | 23/07 14:30 | SMC-MAN-PR-014 V.4 | 192.168.1.100 | Chrome...
```

## 📊 Informações de Compliance

### Dados de Auditoria SMC-MAN-PR-014
- **user_id**: ID do SOLICITANTE
- **procedure_version**: Versão exata do procedimento
- **procedure_summary**: Descrição do procedimento aplicado
- **ip_address**: Endereço IP da sessão
- **user_agent**: Informações do navegador
- **accepted_at**: Timestamp preciso do aceite

### Conformidade Legal
- ✅ **Procedimento Oficial**: SMC-MAN-PR-014 V.4
- ✅ **Registro Completo**: Todos os dados para auditoria
- ✅ **Rastreabilidade**: Compliance total
- ✅ **Versioning**: Controle de versão do procedimento

## 🔒 Aspectos de Segurança Atualizados

### 1. **Compliance SMC-MAN-PR-014**
- Termo baseado 100% no procedimento oficial
- Linguagem técnica conforme padrões da empresa
- Responsabilidades claras conforme hierarquia

### 2. **Segurança Operacional**
- Proibição expressa de execução por não-técnicos
- Obrigatoriedade de comunicação prévia
- Análise de riscos obrigatória

### 3. **Controle de Acesso**
- Registro de versão do procedimento
- Auditoria completa para compliance
- Rastreabilidade total das ações

## 📈 Benefícios da Atualização

### 1. **Compliance Total**
- Alinhamento com SMC-MAN-PR-014 V.4
- Registro legal conforme procedimentos
- Auditoria adequada para normas internas

### 2. **Segurança Aprimorada**
- Responsabilidades claras por função
- Hierarquia de autorização definida
- Procedimentos de segurança específicos

### 3. **Gestão Profissional**
- Terminologia técnica adequada
- Fluxo conforme procedimentos oficiais
- Integração futura com sistemas SAP

## 🔧 Próximos Passos Recomendados

### 1. **Integração SAP**
- Conexão com notas M2 do SAP
- Sincronização de dados
- Workflow automatizado

### 2. **Validação Hierárquica**
- Verificação automática de autorizantes
- Controle por área de responsabilidade
- Aprovação eletrônica

### 3. **Relatórios Gerenciais**
- Dashboard para coordenadores
- Relatórios por área/tipo
- Indicadores de compliance

---

**Atualizado Conforme**: SMC-MAN-PR-014 - CONTROLE DE FORCING V.4  
**Data da Atualização**: 23 de Julho de 2025  
**Versão do Sistema**: 1.1  
**Status**: ✅ Compliance Total com Procedimento Oficial

## 🔧 Funcionalidades

### 1. **Termo Obrigatório**
- **Localização**: `/forcing/terms`
- **Quando aparece**: Sempre que um usuário tentar criar um novo forcing
- **Comportamento**: Redireciona automaticamente para o termo antes do formulário

### 2. **Conteúdo do Termo**
- **Identificação do Solicitante**: Nome, empresa, setor, email e perfil
- **Data/Hora**: Timestamp preciso do momento da visualização
- **Responsabilidades**: Lista detalhada das obrigações do solicitante
- **Consequências**: Advertências sobre uso inadequado
- **Fluxo do Processo**: Explicação visual das etapas do forcing
- **Compromissos de Segurança**: Checklist de verificações necessárias

### 3. **Registro de Aceites**
- **Tabela**: `terms_acceptances`
- **Dados Registrados**:
  - ID do usuário
  - Endereço IP
  - User Agent (navegador)
  - Data/hora precisa do aceite
- **Finalidade**: Auditoria e controle de compliance

## 🚀 Fluxo de Funcionamento

```
1. Usuário clica em "Novo Forcing"
   ↓
2. Sistema redireciona para /forcing/terms
   ↓
3. Usuário visualiza termo de responsabilidade
   ↓
4. Usuário marca checkbox de aceite
   ↓
5. Usuário clica em "Aceitar e Continuar"
   ↓
6. Sistema registra aceite no banco de dados
   ↓
7. Usuário é redirecionado para formulário de criação
```

## 📄 Arquivos Envolvidos

### Views
- `resources/views/forcing/terms.blade.php` - Interface do termo

### Controllers
- `app/Http/Controllers/ForcingController.php` - Métodos `showTerms()` e `create()`

### Models
- `app/Models/TermsAcceptance.php` - Model para aceites
- `app/Models/User.php` - Relacionamento com aceites

### Migrations
- `database/migrations/*_create_terms_acceptances_table.php` - Estrutura da tabela

### Routes
- `routes/web.php` - Rota `/forcing/terms`

### Commands
- `app/Console/Commands/CheckTermsAcceptances.php` - Auditoria de aceites

## 🔍 Comandos de Auditoria

### Verificar Todos os Aceites
```bash
php artisan terms:check
```

### Filtrar por Usuário Específico
```bash
php artisan terms:check --user=1
```

### Mostrar Apenas Últimos 30 Dias
```bash
php artisan terms:check --recent
```

### Combinar Filtros
```bash
php artisan terms:check --user=1 --recent
```

## 📊 Informações Registradas

### Dados de Auditoria
- **user_id**: ID do usuário que aceitou
- **ip_address**: Endereço IP da sessão
- **user_agent**: Informações do navegador
- **accepted_at**: Data/hora precisa do aceite
- **created_at**: Timestamp de criação do registro
- **updated_at**: Timestamp de última atualização

### Relatório de Aceites
O comando `terms:check` fornece:
- Lista completa de aceites
- Estatísticas por período (hoje, semana, mês)
- Top usuários com mais aceites
- Filtros por usuário e data

## 🔒 Aspectos de Segurança

### 1. **Não Contornável**
- O termo é obrigatório e não pode ser pulado
- Redirecionamento automático antes do formulário
- Verificação no controller

### 2. **Auditoria Completa**
- Todos os aceites são registrados
- Dados de sessão preservados
- Rastreabilidade completa

### 3. **Interface Intuitiva**
- Checkbox obrigatório para habilitar botão
- Loading state durante redirecionamento
- Design responsivo e acessível

## 🎨 Características da Interface

### Design
- **Cores**: Esquema de cores de aviso (amarelo/laranja)
- **Ícones**: Font Awesome para visual profissional
- **Layout**: Responsivo com Bootstrap 5

### Interatividade
- Checkbox obrigatório para prosseguir
- Botão desabilitado até aceite
- Animação de loading no redirecionamento
- Tooltips informativos

### Responsividade
- Layout adaptável para mobile
- Colapso de elementos em telas pequenas
- Navegação otimizada para touch

## 📈 Benefícios Implementados

### 1. **Compliance**
- Registro legal de aceite
- Auditoria completa
- Rastreabilidade de ações

### 2. **Segurança**
- Conscientização do usuário
- Redução de forcing desnecessários
- Responsabilização clara

### 3. **Gestão**
- Relatórios de auditoria
- Estatísticas de uso
- Controle de acesso

## 🔧 Manutenção

### Limpeza de Registros Antigos
```sql
-- Remover aceites com mais de 2 anos (exemplo)
DELETE FROM terms_acceptances 
WHERE accepted_at < DATE_SUB(NOW(), INTERVAL 2 YEAR);
```

### Verificação de Integridade
```bash
# Verificar aceites órfãos
php artisan tinker
>>> TermsAcceptance::whereDoesntHave('user')->count()
```

### Backup de Dados de Auditoria
```bash
# Export para CSV
php artisan terms:check --recent > aceites_recentes.txt
```

## 📝 Customizações Futuras

### Possíveis Melhorias
1. **Versioning**: Controle de versões do termo
2. **Templates**: Diferentes termos por tipo de forcing
3. **Notificações**: Email de confirmação do aceite
4. **Dashboard**: Interface web para auditoria
5. **API**: Endpoints para integração externa

### Configurações Adicionais
- Timeout de validade do aceite
- Múltiplos termos por usuário
- Assinatura digital
- Integração com sistemas externos

---

**Desenvolvido para**: Sistema de Controle de Forcing  
**Data**: Julho 2025  
**Versão**: 1.0  
**Status**: ✅ Implementado e Operacional
