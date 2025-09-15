<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Sistema de Controle de Forcing')</title>
    
    <!-- PWA Meta Tags -->
    <meta name="theme-color" content="#667eea">
    <meta name="apple-mobile-web-app-capable" content="yes">
    <meta name="apple-mobile-web-app-status-bar-style" content="default">
    <meta name="apple-mobile-web-app-title" content="Forcing Control">
    <meta name="description" content="Sistema de controle operacional de forcing">
    <meta name="mobile-web-app-capable" content="yes">
    
    <!-- PWA Manifest -->
    <link rel="manifest" href="{{ asset('manifest.json') }}">
    
    <!-- PWA Icons -->
    <link rel="apple-touch-icon" href="{{ asset('icons/icon-192x192.png') }}">
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('icons/icon-32x32.png') }}">
    <link rel="icon" type="image/png" sizes="16x16" href="{{ asset('icons/icon-16x16.png') }}">
    
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS customizado -->
    <link href="{{ asset('css/loading-animations.css') }}" rel="stylesheet">
    <link href="{{ asset('css/forcing-cards.css') }}" rel="stylesheet">
    <link href="{{ asset('css/ferramentas-menu.css') }}" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ route('forcing.index') }}">
                <i class="fas fa-exclamation-triangle"></i> Controle de Forcing
            </a>
            
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    @auth
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('forcing.index') }}">
                                <i class="fas fa-list"></i> Forcing
                            </a>
                        </li>
                        
                        @if(auth()->user()->perfil === 'admin')
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('users.index') }}">
                                    <i class="fas fa-users"></i> Usuários
                                </a>
                            </li>
                        @endif
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('alteracoes.index') }}">
                                <i class="fas fa-bolt"></i> Alterações Elétricas
                            </a>
                        </li>
                        @if(auth()->user()->is_super_admin)
                            <li class="nav-item">
                                <a class="nav-link" href="{{ route('admin.units.index') }}">
                                    <i class="fas fa-building"></i> Unidades
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                
                <ul class="navbar-nav">
                    @auth
                        <!-- Menu Outras Ferramentas -->
                        <li class="nav-item dropdown me-3">
                            <a class="nav-link dropdown-toggle position-relative" href="#" id="ferramentasDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-tools"></i> Outras Ferramentas
                                <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-success" style="font-size: 8px;">
                                    Novo
                                </span>
                            </a>
                            <ul class="dropdown-menu dropdown-menu-dark" aria-labelledby="ferramentasDropdown">
                                <li>
                                    <a class="dropdown-item" href="https://app.devaxis.com.br/login" target="_blank" rel="noopener noreferrer">
                                        <i class="fas fa-chart-line text-info"></i> Sistema de Relatórios
                                        <small class="text-muted d-block">Relatórios e análises detalhadas</small>
                                    </a>
                                </li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <a class="dropdown-item" href="#" onclick="showComingSoon('Em breve: Mais ferramentas serão adicionadas!')">
                                        <i class="fas fa-plus-circle text-success"></i> Mais Ferramentas
                                        <small class="text-muted d-block">Em desenvolvimento</small>
                                    </a>
                                </li>
                            </ul>
                        </li>
                        
                        <!-- Menu do Usuário -->
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                                <i class="fas fa-user"></i> {{ auth()->user()->name }}
                                <span class="badge bg-secondary">{{ ucfirst(auth()->user()->perfil) }}</span>
                            </a>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="{{ route('profile.show') }}">
                                    <i class="fas fa-user"></i> Meu Perfil
                                </a></li>
                                <li><a class="dropdown-item" href="{{ route('profile.edit') }}">
                                    <i class="fas fa-edit"></i> Editar Perfil
                                </a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST" class="d-inline">
                                        @csrf
                                        <button type="submit" class="dropdown-item">
                                            <i class="fas fa-sign-out-alt"></i> Sair
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">
                                <i class="fas fa-sign-in-alt"></i> Entrar
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('register') }}">
                                <i class="fas fa-user-plus"></i> Registrar
                            </a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

    <main class="container-fluid mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <h6><i class="fas fa-exclamation-triangle"></i> Erro(s) encontrado(s):</h6>
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-dark text-light text-center py-3 mt-5">
        <div class="container">
            <p class="mb-0">&copy; {{ date('Y') }} Sistema de Controle de Forcing. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript customizado -->
    <script src="{{ asset('js/loading-animations.js') }}"></script>
    <script src="{{ asset('js/loading-optimized.js') }}"></script>
    <script src="{{ asset('js/forcing-view-toggle.js') }}"></script>
    <script src="{{ asset('js/dropdown-fix.js') }}"></script>
    <script src="{{ asset('js/pwa-updater.js') }}"></script>
    
    <!-- Debug temporário -->
    @if(app()->environment('local'))
        <script src="{{ asset('js/test-dropdowns.js') }}"></script>
    @endif
    
    <!-- Função para mostrar mensagens -->
    <script>
        function showComingSoon(message) {
            // Cria um toast elegante
            const toast = document.createElement('div');
            toast.className = 'toast-notification';
            toast.innerHTML = `
                <div class="toast-content">
                    <i class="fas fa-info-circle text-info"></i>
                    <span>${message}</span>
                </div>
            `;
            
            // Estilos inline para garantir funcionamento
            toast.style.cssText = `
                position: fixed;
                top: 20px;
                right: 20px;
                background: rgba(0, 0, 0, 0.9);
                color: white;
                padding: 15px 20px;
                border-radius: 8px;
                box-shadow: 0 4px 12px rgba(0, 0, 0, 0.3);
                z-index: 10000;
                font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
                font-size: 14px;
                transform: translateX(100%);
                transition: transform 0.3s ease;
                max-width: 300px;
            `;
            
            const toastContent = toast.querySelector('.toast-content');
            toastContent.style.cssText = `
                display: flex;
                align-items: center;
                gap: 10px;
            `;
            
            document.body.appendChild(toast);
            
            // Anima para dentro
            setTimeout(() => {
                toast.style.transform = 'translateX(0)';
            }, 100);
            
            // Remove após 4 segundos
            setTimeout(() => {
                toast.style.transform = 'translateX(100%)';
                setTimeout(() => {
                    if (toast.parentNode) {
                        toast.parentNode.removeChild(toast);
                    }
                }, 300);
            }, 4000);
        }
        
        // Configuração simples do dropdown de ferramentas
        document.addEventListener('DOMContentLoaded', function() {
            // Remove qualquer JavaScript complexo - deixa o Bootstrap gerenciar
            // Apenas garante que o badge funcione corretamente
            const ferramentasDropdown = document.getElementById('ferramentasDropdown');
            if (ferramentasDropdown) {
                // Hover simples no botão
                ferramentasDropdown.addEventListener('mouseenter', function() {
                    this.style.backgroundColor = 'rgba(255, 255, 255, 0.1)';
                });
                
                ferramentasDropdown.addEventListener('mouseleave', function() {
                    this.style.backgroundColor = '';
                });
            }
        });
    </script>
    
    @yield('scripts')
</body>
</html>
