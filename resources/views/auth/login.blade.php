@extends('layouts.auth')

@section('title', 'Acesso ao Sistema - Controle de Forcing')

@section('content')
<div class="login-container">
    <!-- Header -->
    <div class="login-header">
        <h1 class="login-title">
            <i class="fas fa-exclamation-triangle"></i>
            Sistema de Controle de Forcing
        </h1>
        <p class="login-subtitle">Portal de Acesso e Ferramentas</p>
    </div>

    <!-- Grid de Ferramentas -->
    <div class="tools-grid">
        <!-- Login Form -->
        <div class="tool-tile login-tile">
            <div class="tile-icon">
                <i class="fas fa-sign-in-alt"></i>
            </div>
            <div class="tile-content">
                <h3>Entrar no Sistema</h3>
                <form method="POST" action="{{ route('login') }}" class="login-form">
                    @csrf
                    
                    <div class="form-group">
                        <input type="text" class="form-control @error('username') is-invalid @enderror" 
                               id="username" name="username" value="{{ old('username') }}" 
                               placeholder="Usuário" required>
                        @error('username')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group">
                        <input type="password" class="form-control @error('password') is-invalid @enderror" 
                               id="password" name="password" placeholder="Senha" required>
                        @error('password')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-sign-in-alt"></i> Entrar
                    </button>
                </form>
            </div>
        </div>

        <!-- Sistema de Relatórios -->
        <div class="tool-tile reports-tile" onclick="openReports()">
            <div class="tile-icon">
                <i class="fas fa-chart-line"></i>
            </div>
            <div class="tile-content">
                <h3>Sistema de Relatórios</h3>
                <p>Relatórios e análises detalhadas</p>
                <span class="tile-badge">Acesso Direto</span>
            </div>
        </div>

        <!-- Documentação -->
        <div class="tool-tile docs-tile" onclick="openDocs()">
            <div class="tile-icon">
                <i class="fas fa-book"></i>
            </div>
            <div class="tile-content">
                <h3>Documentação</h3>
                <p>Manuais e guias do sistema</p>
                <span class="tile-badge">Público</span>
            </div>
        </div>

        <!-- Suporte -->
        <div class="tool-tile support-tile" onclick="openSupport()">
            <div class="tile-icon">
                <i class="fas fa-life-ring"></i>
            </div>
            <div class="tile-content">
                <h3>Suporte Técnico</h3>
                <p>Central de ajuda e suporte</p>
                <span class="tile-badge">Disponível</span>
            </div>
        </div>

        <!-- Status do Sistema -->
        <div class="tool-tile status-tile" onclick="checkStatus()">
            <div class="tile-icon">
                <i class="fas fa-server"></i>
            </div>
            <div class="tile-content">
                <h3>Status do Sistema</h3>
                <p>Monitoramento em tempo real</p>
                <span class="tile-badge status-indicator">Online</span>
            </div>
        </div>

        <!-- Configurações -->
        <div class="tool-tile config-tile" onclick="openConfig()">
            <div class="tile-icon">
                <i class="fas fa-cog"></i>
            </div>
            <div class="tile-content">
                <h3>Configurações</h3>
                <p>Configurações do sistema</p>
                <span class="tile-badge">Admin</span>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <div class="login-footer">
        <p>&copy; 2025 Sistema de Controle de Forcing. Todos os direitos reservados.</p>
        <div class="footer-links">
            <a href="#" onclick="openPrivacy()">Política de Privacidade</a>
            <a href="#" onclick="openTerms()">Termos de Uso</a>
            <a href="#" onclick="openContact()">Contato</a>
        </div>
    </div>
</div>

<script>
function openReports() {
    window.open('https://app.devaxis.com.br/login', '_blank');
}

function openDocs() {
    // Implementar link para documentação
    alert('Documentação será implementada em breve!');
}

function openSupport() {
    // Implementar link para suporte
    alert('Central de Suporte será implementada em breve!');
}

function checkStatus() {
    // Implementar verificação de status
    alert('Status do Sistema: Online ✅\nTodos os serviços funcionando normalmente.');
}

function openConfig() {
    // Implementar configurações (requer login)
    alert('Configurações disponíveis apenas para administradores.\nFaça login primeiro.');
}

function openPrivacy() {
    alert('Política de Privacidade será implementada em breve!');
}

function openTerms() {
    alert('Termos de Uso serão implementados em breve!');
}

function openContact() {
    alert('Informações de contato:\n\nEmail: suporte@forcingsystem.com\nTelefone: (11) 9999-9999');
}
</script>
@endsection
