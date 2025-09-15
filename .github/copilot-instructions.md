<!-- Use this file to provide workspace-specific custom instructions to Copilot. For more details, visit https://code.visualstudio.com/docs/copilot/copilot-customization#_use-a-githubcopilotinstructionsmd-file -->

# Sistema de Controle de Forcing - Laravel

Este é um sistema de controle de forcing desenvolvido em Laravel com as seguintes características:

## Funcionalidades Principais
- Sistema de autenticação com 3 perfis: user, liberador, admin
- Gerenciamento de forcing com status: Forçado/Retirado
- Dashboard para visualização de todos os forcing
- Controle de acesso baseado em perfis

## Estrutura de Usuários
- Nome completo, email, username, senha, empresa, setor
- Perfis: user (padrão), liberador, admin

## Padrões do Projeto
- Use Laravel 12.x
- Siga as convenções do Laravel para nomes de arquivos e estrutura
- Use Eloquent ORM para relacionamentos
- Implemente middleware para controle de acesso
- Use Blade templates para views
- Mantenha código limpo e bem documentado
