{{-- 
    Exemplo de integração do Sistema de Acessibilidade para Daltonismo
    
    Adicione as seguintes linhas ao seu layout principal (app.blade.php ou master.blade.php)
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    
    <title>{{ config('app.name', 'Laravel') }}</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome para ícones -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    
    <!-- CSS de Acessibilidade para Daltonismo -->
    <link href="{{ asset('css/colorblind-accessibility.css') }}" rel="stylesheet">
    
    <!-- CSS customizado da aplicação -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
</head>
<body>
    <!-- Navegação -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-dark">
        <div class="container">
            <a class="navbar-brand" href="{{ url('/') }}">
                {{ config('app.name', 'Forcing System') }}
            </a>
            
            <div class="navbar-nav ms-auto">
                <!-- Exemplo de menu com acessibilidade -->
                <a class="nav-link" href="{{ route('dashboard') }}">
                    <i class="fas fa-home" aria-hidden="true"></i>
                    Dashboard
                </a>
                
                <!-- Dropdown de usuário -->
                <div class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown">
                        <i class="fas fa-user" aria-hidden="true"></i>
                        {{ auth()->user()->name ?? 'Usuário' }}
                    </a>
                    <ul class="dropdown-menu">
                        <li><a class="dropdown-item" href="{{ route('profile') }}">Perfil</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="dropdown-item">
                                    <i class="fas fa-sign-out-alt" aria-hidden="true"></i>
                                    Sair
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Conteúdo Principal -->
    <main class="container-fluid py-4">
        <!-- Alertas com suporte a acessibilidade -->
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <strong>Sucesso!</strong> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <strong>Erro!</strong> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        @if(session('warning'))
            <div class="alert alert-warning alert-dismissible fade show" role="alert">
                <strong>Atenção!</strong> {{ session('warning') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Fechar"></button>
            </div>
        @endif

        <!-- Exemplo de cards de forcing com acessibilidade -->
        <div class="row">
            <div class="col-12">
                <h1>Sistema de Forcing</h1>
                <p class="text-muted">Controle de forçamento de sistemas com acessibilidade completa</p>
            </div>
        </div>

        <!-- Cards de exemplo -->
        <div class="row">
            @php
                $forcings = [
                    ['id' => 1, 'nome' => 'Forcing Sistema A', 'status' => 'ativo', 'badge_class' => 'badge-danger'],
                    ['id' => 2, 'nome' => 'Forcing Sistema B', 'status' => 'retirado', 'badge_class' => 'badge-success'],
                    ['id' => 3, 'nome' => 'Forcing Sistema C', 'status' => 'pendente', 'badge_class' => 'badge-warning'],
                ];
            @endphp

            @foreach($forcings as $forcing)
            <div class="col-md-4 mb-3">
                <div class="card forcing-status-card" data-status="{{ $forcing['status'] }}">
                    <div class="card-body">
                        <div class="d-flex justify-content-between align-items-start">
                            <h5 class="card-title">{{ $forcing['nome'] }}</h5>
                            <span class="badge {{ $forcing['badge_class'] }}">
                                {{ ucfirst($forcing['status']) }}
                            </span>
                        </div>
                        <p class="card-text">
                            ID: {{ $forcing['id'] }}<br>
                            Status atual: {{ $forcing['status'] }}<br>
                            Última atualização: {{ now()->format('d/m/Y H:i') }}
                        </p>
                        <div class="btn-group w-100" role="group">
                            @if($forcing['status'] === 'ativo')
                                <button type="button" class="btn btn-warning btn-sm">
                                    <i class="fas fa-pause" aria-hidden="true"></i>
                                    Pausar
                                </button>
                                <button type="button" class="btn btn-success btn-sm">
                                    <i class="fas fa-stop" aria-hidden="true"></i>
                                    Retirar
                                </button>
                            @elseif($forcing['status'] === 'retirado')
                                <button type="button" class="btn btn-primary btn-sm">
                                    <i class="fas fa-play" aria-hidden="true"></i>
                                    Reativar
                                </button>
                            @else
                                <button type="button" class="btn btn-info btn-sm">
                                    <i class="fas fa-clock" aria-hidden="true"></i>
                                    Aguardando
                                </button>
                            @endif
                            <button type="button" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-edit" aria-hidden="true"></i>
                                Editar
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <!-- Tabela de exemplo -->
        <div class="row mt-4">
            <div class="col-12">
                <h3>Histórico de Forcing</h3>
                <div class="table-responsive">
                    <table class="table table-striped" aria-label="Tabela de histórico de forcing">
                        <thead class="table-dark">
                            <tr>
                                <th scope="col">ID</th>
                                <th scope="col">Sistema</th>
                                <th scope="col">Status</th>
                                <th scope="col">Data/Hora</th>
                                <th scope="col">Usuário</th>
                                <th scope="col">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td>001</td>
                                <td>Sistema Principal</td>
                                <td><span class="badge badge-danger">Forçado</span></td>
                                <td>{{ now()->format('d/m/Y H:i') }}</td>
                                <td>Admin</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        Ver
                                    </button>
                                    <button class="btn btn-sm btn-outline-success">
                                        <i class="fas fa-stop" aria-hidden="true"></i>
                                        Retirar
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>002</td>
                                <td>Sistema Backup</td>
                                <td><span class="badge badge-success">Retirado</span></td>
                                <td>{{ now()->subHour()->format('d/m/Y H:i') }}</td>
                                <td>Operador</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        Ver
                                    </button>
                                    <button class="btn btn-sm btn-outline-info">
                                        <i class="fas fa-redo" aria-hidden="true"></i>
                                        Reativar
                                    </button>
                                </td>
                            </tr>
                            <tr>
                                <td>003</td>
                                <td>Sistema Teste</td>
                                <td><span class="badge badge-warning">Pendente</span></td>
                                <td>{{ now()->subMinutes(30)->format('d/m/Y H:i') }}</td>
                                <td>Teste</td>
                                <td>
                                    <button class="btn btn-sm btn-outline-primary">
                                        <i class="fas fa-eye" aria-hidden="true"></i>
                                        Ver
                                    </button>
                                    <button class="btn btn-sm btn-outline-warning">
                                        <i class="fas fa-clock" aria-hidden="true"></i>
                                        Aguardar
                                    </button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Conteúdo específico da página -->
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white text-center py-3 mt-5">
        <div class="container">
            <p>&copy; {{ date('Y') }} Sistema de Forcing. Desenvolvido com acessibilidade.</p>
            <small>
                Pressione <kbd>Ctrl</kbd> + <kbd>Shift</kbd> + <kbd>A</kbd> para ativar/desativar o modo daltonismo
            </small>
        </div>
    </footer>

    <!-- Scripts necessários -->
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- JavaScript de Acessibilidade para Daltonismo -->
    <script src="{{ asset('js/colorblind-accessibility.js') }}"></script>
    
    <!-- Scripts customizados da aplicação -->
    <script src="{{ asset('js/app.js') }}"></script>
    
    @stack('scripts')
</body>
</html>
