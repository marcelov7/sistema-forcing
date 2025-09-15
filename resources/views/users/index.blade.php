@extends('layouts.app')

@section('title', 'Gerenciar Usuários')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-users"></i> Gerenciar Usuários</h1>
    <div>
        <button type="button" class="btn btn-info me-2" data-bs-toggle="collapse" data-bs-target="#perfisInfo" aria-expanded="false">
            <i class="fas fa-info-circle"></i> Info sobre Perfis
        </button>
        <a href="{{ route('users.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Usuário
        </a>
    </div>
</div>

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

<!-- Informações sobre Perfis -->
<div class="collapse mb-4" id="perfisInfo">
    <div class="card shadow-sm border-0">
            <div class="card-header bg-info text-white">
                <h5 class="mb-0">
                    <i class="fas fa-info-circle"></i> Tipos de Perfil no Sistema
                </h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Admin -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-danger h-100">
                            <div class="card-header bg-danger text-white text-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-crown"></i> ADMINISTRADOR
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-danger mb-2">Permissões:</h6>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Acesso total ao sistema</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Gerenciar usuários</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Criar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Liberar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Executar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Retirar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Ver todos os forcing</li>
                                </ul>
                            </div>
                        </div>
                    </div>

                    <!-- Liberador -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-warning h-100">
                            <div class="card-header bg-warning text-dark text-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-check-circle"></i> LIBERADOR
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-warning mb-2">Permissões:</h6>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Criar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Liberar forcing pendentes</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Adicionar observações</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Ver forcing do sistema</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não executa forcing</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não gerencia usuários</li>
                                </ul>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Responsável por analisar e aprovar forcing
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Executante -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-info h-100">
                            <div class="card-header bg-info text-white text-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-tools"></i> EXECUTANTE
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-info mb-2">Permissões:</h6>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Criar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Executar forcing liberados</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Solicitar retirada</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Confirmar retirada</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Ver forcing atribuídos</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não libera forcing</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não gerencia usuários</li>
                                </ul>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Responsável por executar forcing em campo
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- User -->
                    <div class="col-lg-3 col-md-6 mb-3">
                        <div class="card border-secondary h-100">
                            <div class="card-header bg-secondary text-white text-center">
                                <h6 class="mb-0">
                                    <i class="fas fa-user"></i> USUÁRIO
                                </h6>
                            </div>
                            <div class="card-body p-3">
                                <h6 class="text-secondary mb-2">Permissões:</h6>
                                <ul class="list-unstyled small mb-0">
                                    <li><i class="fas fa-check text-success me-1"></i> Criar forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Ver seus forcing</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Editar forcing próprios</li>
                                    <li><i class="fas fa-check text-success me-1"></i> Acompanhar status</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não libera forcing</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não executa forcing</li>
                                    <li><i class="fas fa-times text-danger me-1"></i> Não gerencia usuários</li>
                                </ul>
                                <div class="mt-2">
                                    <small class="text-muted">
                                        <i class="fas fa-info-circle"></i> 
                                        Perfil básico para criação de forcing
                                    </small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Fluxo do Sistema -->
                <div class="row mt-3">
                    <div class="col-12">
                        <div class="alert alert-light border">
                            <h6 class="mb-2">
                                <i class="fas fa-route text-primary"></i> Fluxo do Sistema:
                            </h6>
                            <div class="d-flex align-items-center flex-wrap">
                                <span class="badge bg-secondary me-2 mb-1">1. USER cria</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-warning me-2 mb-1">2. LIBERADOR aprova</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-info me-2 mb-1">3. EXECUTANTE executa</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-danger me-2 mb-1">4. EXECUTANTE solicita retirada</span>
                                <i class="fas fa-arrow-right text-muted me-2"></i>
                                <span class="badge bg-success mb-1">5. EXECUTANTE confirma retirada</span>
                            </div>
                            <small class="text-muted mt-2 d-block">
                                <i class="fas fa-info-circle"></i> 
                                ADMIN pode realizar qualquer ação em qualquer etapa do processo
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
</div>

@if($users->count() > 0)
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-striped table-hover mb-0">
                            <thead class="table-dark">
                                <tr>
                                    <th>#</th>
                                    <th>Nome</th>
                                    <th>Usuário</th>
                                    <th>Email</th>
                                    <th>Perfil</th>
                                    <th>Empresa/Setor</th>
                                    <th>Cadastrado em</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($users as $user)
                                    <tr>
                                        <td>{{ $user->id }}</td>
                                        <td>
                                            <strong>{{ $user->name }}</strong>
                                        </td>
                                        <td>
                                            <code>{{ $user->username }}</code>
                                        </td>
                                        <td>{{ $user->email }}</td>
                                        <td>
                                            @if($user->perfil === 'admin')
                                                <span class="badge bg-danger">
                                                    <i class="fas fa-crown"></i> Admin
                                                </span>
                                            @elseif($user->perfil === 'liberador')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check-circle"></i> Liberador
                                                </span>
                                            @elseif($user->perfil === 'executante')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-tools"></i> Executante
                                                </span>
                                            @else
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-user"></i> Usuário
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $user->empresa }}</strong>
                                            <br><small class="text-muted">{{ $user->setor }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $user->created_at->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('users.show', $user) }}" 
                                                   class="btn btn-info btn-sm" title="Ver Detalhes">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                <a href="{{ route('users.edit', $user) }}" 
                                                   class="btn btn-warning btn-sm" title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                @if($user->id !== auth()->id())
                                                    <button type="button" class="btn btn-danger btn-sm" 
                                                            data-bs-toggle="modal" 
                                                            data-bs-target="#deleteModal{{ $user->id }}"
                                                            title="Excluir">
                                                        <i class="fas fa-trash"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal de Exclusão -->
                                    @if($user->id !== auth()->id())
                                        <div class="modal fade" id="deleteModal{{ $user->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <div class="modal-header">
                                                        <h5 class="modal-title">Confirmar Exclusão</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <p>Tem certeza que deseja excluir o usuário:</p>
                                                        <p><strong>{{ $user->name }}</strong> ({{ $user->username }})</p>
                                                        <div class="alert alert-warning">
                                                            <i class="fas fa-exclamation-triangle"></i>
                                                            Esta ação não pode ser desfeita!
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                                                            Cancelar
                                                        </button>
                                                        <form action="{{ route('users.destroy', $user) }}" method="POST" class="d-inline">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="btn btn-danger">
                                                                <i class="fas fa-trash"></i> Excluir
                                                            </button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Paginação -->
                    @if($users->hasPages())
                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted">
                                Mostrando {{ $users->firstItem() }} a {{ $users->lastItem() }} de {{ $users->total() }} usuários
                            </div>
                            <div>
                                {{ $users->links('pagination::bootstrap-4') }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Resumo dos perfis -->
    <div class="row mt-4">
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $users->where('perfil', 'admin')->count() }}</h3>
                    <p class="mb-0"><i class="fas fa-crown"></i> Administradores</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $users->where('perfil', 'liberador')->count() }}</h3>
                    <p class="mb-0"><i class="fas fa-check-circle"></i> Liberadores</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $users->where('perfil', 'executante')->count() }}</h3>
                    <p class="mb-0"><i class="fas fa-tools"></i> Executantes</p>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card bg-secondary text-white">
                <div class="card-body text-center">
                    <h3 class="mb-0">{{ $users->where('perfil', 'user')->count() }}</h3>
                    <p class="mb-0"><i class="fas fa-user"></i> Usuários</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-users fa-3x text-muted mb-3"></i>
                    <h4>Nenhum usuário encontrado</h4>
                    <p class="text-muted">Cadastre o primeiro usuário no sistema!</p>
                    <a href="{{ route('users.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeiro Usuário
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

<style>
.card.border-danger:hover,
.card.border-warning:hover,
.card.border-info:hover,
.card.border-secondary:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
    transition: all 0.3s ease;
}

.badge {
    font-size: 0.75rem;
    font-weight: 500;
}

.alert-light {
    background-color: #f8f9fa;
    border-color: #dee2e6;
}

#perfisInfo .card-body {
    max-height: 600px;
    overflow-y: auto;
}

@media (max-width: 768px) {
    .d-flex.align-items-center.flex-wrap .badge {
        margin-bottom: 0.5rem !important;
    }
    
    .fas.fa-arrow-right {
        display: none;
    }
}

.collapse.show {
    animation: slideDown 0.3s ease-out;
}

@keyframes slideDown {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Adicionar tooltip aos badges de perfil
    const tooltips = {
        'ADMIN': 'Controle total do sistema com todas as permissões',
        'LIBERADOR': 'Responsável por analisar e aprovar forcing pendentes',
        'EXECUTANTE': 'Executa forcing aprovados e gerencia retiradas',
        'USER': 'Cria forcing e acompanha status dos próprios forcing'
    };
    
    document.querySelectorAll('.badge').forEach(badge => {
        const text = badge.textContent.trim();
        if (tooltips[text]) {
            badge.setAttribute('title', tooltips[text]);
            badge.setAttribute('data-bs-toggle', 'tooltip');
        }
    });
    
    // Inicializar tooltips do Bootstrap
    if (typeof bootstrap !== 'undefined') {
        var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
        var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrap.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Efeito de destaque ao expandir informações
    const collapseElement = document.getElementById('perfisInfo');
    const toggleButton = document.querySelector('[data-bs-target="#perfisInfo"]');
    
    if (collapseElement && toggleButton) {
        collapseElement.addEventListener('shown.bs.collapse', function () {
            toggleButton.innerHTML = '<i class="fas fa-eye-slash"></i> Ocultar Info';
        });
        
        collapseElement.addEventListener('hidden.bs.collapse', function () {
            toggleButton.innerHTML = '<i class="fas fa-info-circle"></i> Info sobre Perfis';
        });
    }
});
</script>
@endsection
