@extends('layouts.app')

@section('title', 'Meu Perfil')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h1 class="h3 mb-0">
                        <i class="fas fa-user-circle"></i> Meu Perfil
                    </h1>
                    <p class="text-muted mb-0">Visualize e gerencie suas informações pessoais</p>
                </div>
                <div>
                    <a href="{{ route('profile.edit') }}" class="btn btn-primary">
                        <i class="fas fa-edit"></i> Editar Perfil
                    </a>
                </div>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="fas fa-check-circle"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <div class="row">
        <!-- Informações Principais -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-id-card"></i> Informações Pessoais
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 40%;">Nome Completo:</td>
                                    <td>{{ $user->name }}</td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">E-mail:</td>
                                    <td>
                                        <i class="fas fa-envelope text-muted me-1"></i>
                                        {{ $user->email }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Username:</td>
                                    <td>
                                        <i class="fas fa-at text-muted me-1"></i>
                                        {{ $user->username }}
                                    </td>
                                </tr>
                            </table>
                        </div>
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <td class="fw-bold text-muted" style="width: 40%;">Empresa:</td>
                                    <td>
                                        <i class="fas fa-building text-muted me-1"></i>
                                        {{ $user->empresa }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Setor:</td>
                                    <td>
                                        <i class="fas fa-sitemap text-muted me-1"></i>
                                        {{ $user->setor }}
                                    </td>
                                </tr>
                                <tr>
                                    <td class="fw-bold text-muted">Perfil:</td>
                                    <td>
                                        @if($user->perfil === 'admin')
                                            <span class="badge bg-danger">
                                                <i class="fas fa-crown"></i> Administrador
                                            </span>
                                        @elseif($user->perfil === 'liberador')
                                            <span class="badge bg-warning">
                                                <i class="fas fa-check-circle"></i> Liberador
                                            </span>
                                        @elseif($user->perfil === 'executante')
                                            <span class="badge bg-info">
                                                <i class="fas fa-tools"></i> Executante
                                            </span>
                                        @else
                                            <span class="badge bg-secondary">
                                                <i class="fas fa-user"></i> Usuário
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Permissões do Perfil -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-info text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-key"></i> Permissões do Perfil
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Ações Permitidas:</h6>
                            <ul class="list-unstyled">
                                <li class="mb-2">
                                    <i class="fas fa-plus-circle text-success me-2"></i>
                                    Criar forcing
                                </li>
                                @if(in_array($user->perfil, ['liberador', 'executante', 'admin']))
                                    <li class="mb-2">
                                        <i class="fas fa-check-circle text-warning me-2"></i>
                                        Liberar forcing
                                    </li>
                                @endif
                                @if(in_array($user->perfil, ['executante', 'admin']))
                                    <li class="mb-2">
                                        <i class="fas fa-tools text-info me-2"></i>
                                        Executar forcing
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-times-circle text-danger me-2"></i>
                                        Solicitar retirada de forcing
                                    </li>
                                @endif
                                @if($user->perfil === 'admin')
                                    <li class="mb-2">
                                        <i class="fas fa-users text-primary me-2"></i>
                                        Gerenciar usuários
                                    </li>
                                    <li class="mb-2">
                                        <i class="fas fa-cog text-secondary me-2"></i>
                                        Acesso total ao sistema
                                    </li>
                                @endif
                            </ul>
                        </div>
                        <div class="col-md-6">
                            <h6 class="text-muted mb-3">Informações do Perfil:</h6>
                            @if($user->perfil === 'admin')
                                <div class="alert alert-danger">
                                    <i class="fas fa-crown me-2"></i>
                                    <strong>Administrador:</strong> Acesso completo ao sistema, incluindo gerenciamento de usuários e todas as operações de forcing.
                                </div>
                            @elseif($user->perfil === 'liberador')
                                <div class="alert alert-warning">
                                    <i class="fas fa-check-circle me-2"></i>
                                    <strong>Liberador:</strong> Responsável por analisar e liberar forcing para execução.
                                </div>
                            @elseif($user->perfil === 'executante')
                                <div class="alert alert-info">
                                    <i class="fas fa-tools me-2"></i>
                                    <strong>Executante:</strong> Responsável por executar forcing liberados e pode solicitar retirada.
                                </div>
                            @else
                                <div class="alert alert-secondary">
                                    <i class="fas fa-user me-2"></i>
                                    <strong>Usuário:</strong> Pode criar forcing e acompanhar o status dos seus forcing.
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Estatísticas -->
        <div class="col-lg-4">
            <!-- Estatísticas Pessoais -->
            <div class="card shadow-sm">
                <div class="card-header bg-success text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-chart-bar"></i> Minhas Estatísticas
                    </h5>
                </div>
                <div class="card-body">
                    @php
                        $forcingsCriados = $user->forcings()->count();
                        $forcingsLiberados = $user->forcingsLiberados()->count();
                        $forcingsExecutados = $user->forcingsExecutados()->count();
                    @endphp

                    <div class="row text-center">
                        <div class="col-12 mb-3">
                            <div class="border rounded p-3">
                                <h4 class="text-primary mb-1">{{ $forcingsCriados }}</h4>
                                <small class="text-muted">Solicitações de Forcing</small>
                            </div>
                        </div>
                        @if(in_array($user->perfil, ['liberador', 'admin']))
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-warning mb-1">{{ $forcingsLiberados }}</h4>
                                    <small class="text-muted">Forcing Liberados</small>
                                </div>
                            </div>
                        @endif
                        @if(in_array($user->perfil, ['executante', 'admin']))
                            <div class="col-12 mb-3">
                                <div class="border rounded p-3">
                                    <h4 class="text-info mb-1">{{ $forcingsExecutados }}</h4>
                                    <small class="text-muted">Forcing Executados</small>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Informações da Conta -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-secondary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-info-circle"></i> Informações da Conta
                    </h5>
                </div>
                <div class="card-body">
                    <table class="table table-sm table-borderless">
                        <tr>
                            <td class="text-muted">Membro desde:</td>
                            <td>{{ $user->created_at->format('d/m/Y') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Última atualização:</td>
                            <td>{{ $user->updated_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        <tr>
                            <td class="text-muted">Status:</td>
                            <td>
                                <span class="badge bg-success">
                                    <i class="fas fa-check-circle"></i> Ativo
                                </span>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

            <!-- Ações Rápidas -->
            <div class="card shadow-sm mt-4">
                <div class="card-header bg-dark text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-bolt"></i> Ações Rápidas
                    </h5>
                </div>
                <div class="card-body">
                    <div class="d-grid gap-2">
                        <a href="{{ route('forcing.terms') }}" class="btn btn-primary btn-sm">
                            <i class="fas fa-plus"></i> Criar Novo Forcing
                        </a>
                        <a href="{{ route('forcing.index') }}" class="btn btn-outline-primary btn-sm">
                            <i class="fas fa-list"></i> Ver Meus Forcing
                        </a>
                        <a href="{{ route('profile.edit') }}" class="btn btn-outline-secondary btn-sm">
                            <i class="fas fa-edit"></i> Editar Perfil
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.table-borderless td {
    border: none;
    padding: 0.5rem 0;
}

.card {
    border: none;
    border-radius: 10px;
}

.card-header {
    border-radius: 10px 10px 0 0 !important;
}

.badge {
    font-size: 0.875rem;
}

.alert {
    border-radius: 8px;
    border: none;
}
</style>
@endsection
