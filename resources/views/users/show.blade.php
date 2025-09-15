@extends('layouts.app')

@section('title', 'Detalhes do Usuário')

@section('content')
<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-user"></i> Detalhes do Usuário</h4>
                    @if($user->perfil === 'admin')
                        <span class="badge bg-danger fs-6">
                            <i class="fas fa-crown"></i> ADMIN
                        </span>
                    @elseif($user->perfil === 'liberador')
                        <span class="badge bg-success fs-6">
                            <i class="fas fa-check-circle"></i> LIBERADOR
                        </span>
                    @elseif($user->perfil === 'executante')
                        <span class="badge bg-warning text-dark fs-6">
                            <i class="fas fa-tools"></i> EXECUTANTE
                        </span>
                    @else
                        <span class="badge bg-secondary fs-6">
                            <i class="fas fa-user"></i> USUÁRIO
                        </span>
                    @endif
                </div>
            </div>
            <div class="card-body">
                <div class="row">
                    <!-- Informações Pessoais -->
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-info"></i> Informações Pessoais</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Nome:</strong></td>
                                        <td>{{ $user->name }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Username:</strong></td>
                                        <td><code>{{ $user->username }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Email:</strong></td>
                                        <td>{{ $user->email }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Perfil:</strong></td>
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
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Informações Profissionais -->
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-building"></i> Informações Profissionais</h6>
                                <table class="table table-sm text-white">
                                    <tr>
                                        <td><strong>Empresa:</strong></td>
                                        <td>{{ $user->empresa }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Setor:</strong></td>
                                        <td>{{ $user->setor }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Cadastrado em:</strong></td>
                                        <td>{{ $user->created_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Última atualização:</strong></td>
                                        <td>{{ $user->updated_at->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Estatísticas do Usuário -->
                <div class="row mt-4">
                    <div class="col-12">
                        <h6><i class="fas fa-chart-bar"></i> Estatísticas de Forcing</h6>
                        <div class="row">
                            <div class="col-md-3">
                                <div class="card bg-primary text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">{{ $user->forcings()->count() }}</h4>
                                        <p class="mb-0"><i class="fas fa-plus"></i> Criados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-success text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">{{ $user->forcingsLiberados()->count() }}</h4>
                                        <p class="mb-0"><i class="fas fa-check"></i> Liberados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-warning text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">{{ $user->forcingsExecutados()->count() }}</h4>
                                        <p class="mb-0"><i class="fas fa-tools"></i> Executados</p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <div class="card bg-dark text-white">
                                    <div class="card-body text-center">
                                        <h4 class="mb-0">{{ $user->forcingsRetirados()->count() }}</h4>
                                        <p class="mb-0"><i class="fas fa-check-double"></i> Retirados</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Permissões do Perfil -->
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-key"></i> Permissões do Perfil</h6>
                                <div class="row">
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            <li><i class="fas fa-check text-success"></i> Visualizar forcing</li>
                                            <li><i class="fas fa-check text-success"></i> Criar forcing</li>
                                            @if(in_array($user->perfil, ['liberador', 'executante', 'admin']))
                                                <li><i class="fas fa-check text-success"></i> Liberar forcing</li>
                                            @else
                                                <li><i class="fas fa-times text-muted"></i> <span class="text-muted">Liberar forcing</span></li>
                                            @endif
                                            @if(in_array($user->perfil, ['executante', 'admin']))
                                                <li><i class="fas fa-check text-success"></i> Executar forcing</li>
                                            @else
                                                <li><i class="fas fa-times text-muted"></i> <span class="text-muted">Executar forcing</span></li>
                                            @endif
                                        </ul>
                                    </div>
                                    <div class="col-md-6">
                                        <ul class="list-unstyled">
                                            @if($user->perfil === 'admin')
                                                <li><i class="fas fa-check text-success"></i> Gerenciar usuários</li>
                                                <li><i class="fas fa-check text-success"></i> Excluir forcing</li>
                                                <li><i class="fas fa-check text-success"></i> Acesso total</li>
                                            @else
                                                <li><i class="fas fa-times text-muted"></i> <span class="text-muted">Gerenciar usuários</span></li>
                                                <li><i class="fas fa-times text-muted"></i> <span class="text-muted">Excluir forcing</span></li>
                                                <li><i class="fas fa-times text-muted"></i> <span class="text-muted">Acesso total</span></li>
                                            @endif
                                            <li><i class="fas fa-check text-success"></i> Solicitar retirada</li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('users.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                    <div>
                        @if(auth()->user()->isAdmin() || auth()->id() === $user->id)
                            <a href="{{ route('users.edit', $user) }}" class="btn btn-warning">
                                <i class="fas fa-edit"></i> Editar
                            </a>
                        @endif
                        
                        @if(auth()->user()->isAdmin() && $user->id !== auth()->id())
                            <button type="button" class="btn btn-danger" 
                                    data-bs-toggle="modal" data-bs-target="#deleteModal">
                                <i class="fas fa-trash"></i> Excluir
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Exclusão -->
@if(auth()->user()->isAdmin() && $user->id !== auth()->id())
    <div class="modal fade" id="deleteModal" tabindex="-1">
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
                        Esta ação não pode ser desfeita e removerá todos os dados relacionados!
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
                            <i class="fas fa-trash"></i> Excluir Definitivamente
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endif
@endsection
