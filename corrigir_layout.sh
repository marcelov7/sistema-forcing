#!/bin/bash

echo "🔧 CORRIGINDO LAYOUT APP.BLADE.PHP..."

cd /home/devaxis-forcing/htdocs/forcing.devaxis.com.br

# 1. Fazer backup
cp resources/views/layouts/app.blade.php resources/views/layouts/app.blade.php.backup

# 2. Verificar arquivo atual
echo "📋 Arquivo atual:"
head -n 10 resources/views/layouts/app.blade.php

# 3. Criar versão corrigida
cat > resources/views/layouts/app.blade.php << 'EOF'
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>@yield('title', 'Sistema de Controle de Forcing')</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forcing.index') }}">
                            <i class="fas fa-list"></i> Lista de Forcing
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('forcing.terms') }}">
                            <i class="fas fa-plus"></i> Novo Forcing
                        </a>
                    </li>
                </ul>

                <ul class="navbar-nav">
                    <li class="nav-item dropdown">
                        <a class="nav-link dropdown-toggle" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown">
                            <i class="fas fa-user"></i> {{ Auth::user()->name }}
                        </a>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" href="#">
                                <i class="fas fa-user-cog"></i> Perfil
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
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mt-4">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle"></i> {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-triangle"></i> {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
EOF

# 4. Verificar novo arquivo
echo ""
echo "📋 Novo arquivo criado:"
head -n 10 resources/views/layouts/app.blade.php

# 5. Limpar caches
php artisan view:clear
php artisan config:clear

echo "✅ Layout corrigido!"
