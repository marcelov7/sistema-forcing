@extends('layouts.app')

@section('title', 'Alterações Elétricas - Sistema de Forcing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-bolt text-warning"></i>
                        Controle de Alterações Elétricas
                    </h2>
                    <p class="text-muted mb-0">Gerenciamento de alterações elétricas e lógicas</p>
                </div>
                <a href="{{ route('alteracoes.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nova Alteração
                </a>
            </div>
        </div>
    </div>

    <!-- Estatísticas -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="card bg-primary text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['total'] }}</h4>
                            <p class="card-text">Total</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-file-alt fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-warning text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['pendentes'] }}</h4>
                            <p class="card-text">Pendentes</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-clock fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-success text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['aprovadas'] }}</h4>
                            <p class="card-text">Aprovadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-check-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
        <div class="col-md-3">
            <div class="card bg-danger text-white">
                <div class="card-body">
                    <div class="d-flex justify-content-between">
                        <div>
                            <h4 class="card-title">{{ $stats['rejeitadas'] }}</h4>
                            <p class="card-text">Rejeitadas</p>
                        </div>
                        <div class="align-self-center">
                            <i class="fas fa-times-circle fa-2x"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Filtros -->
    <div class="card mb-4">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-filter"></i> Filtros
            </h5>
        </div>
        <div class="card-body">
            <form method="GET" class="row g-3">
                <div class="col-md-2">
                    <label for="status" class="form-label">Status</label>
                    <select class="form-select" id="status" name="status">
                        <option value="">Todos</option>
                        <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>Pendente</option>
                        <option value="em_analise" {{ request('status') == 'em_analise' ? 'selected' : '' }}>Em Análise</option>
                        <option value="aprovada" {{ request('status') == 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                        <option value="rejeitada" {{ request('status') == 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                        <option value="implementada" {{ request('status') == 'implementada' ? 'selected' : '' }}>Implementada</option>
                    </select>
                </div>
                
                <div class="col-md-3">
                    <label for="solicitante" class="form-label">Solicitante</label>
                    <input type="text" class="form-control" id="solicitante" name="solicitante" 
                           value="{{ request('solicitante') }}" placeholder="Nome do solicitante">
                </div>
                
                <div class="col-md-3">
                    <label for="departamento" class="form-label">Departamento</label>
                    <input type="text" class="form-control" id="departamento" name="departamento" 
                           value="{{ request('departamento') }}" placeholder="Nome do departamento">
                </div>
                
                <div class="col-md-2">
                    <label for="data_inicio" class="form-label">Data Início</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" 
                           value="{{ request('data_inicio') }}">
                </div>
                
                <div class="col-md-2">
                    <label for="data_fim" class="form-label">Data Fim</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" 
                           value="{{ request('data_fim') }}">
                </div>
                
                <div class="col-12">
                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-search"></i> Filtrar
                    </button>
                    <a href="{{ route('alteracoes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-times"></i> Limpar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Lista de Alterações -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-list"></i> Alterações Elétricas
                <span class="badge bg-primary ms-2">{{ $alteracoes->total() }} registros</span>
            </h5>
        </div>
        <div class="card-body p-0">
            @if($alteracoes->count() > 0)
                <div class="table-responsive">
                    <table class="table table-hover mb-0">
                        <thead class="table-light">
                            <tr>
                                <th>Documento</th>
                                <th>Solicitante</th>
                                <th>Departamento</th>
                                @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                                    <th>Unidade</th>
                                @endif
                                <th>Data</th>
                                <th>Status</th>
                                <th>Criado por</th>
                                <th width="150">Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($alteracoes as $alteracao)
                                <tr>
                                    <td>
                                        <div class="fw-bold text-primary">{{ $alteracao->numero_documento }}</div>
                                        <small class="text-muted">v{{ $alteracao->versao }}</small>
                                    </td>
                                    <td>{{ $alteracao->solicitante }}</td>
                                    <td>{{ $alteracao->departamento }}</td>
                                    @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                                        <td>
                                            @if($alteracao->unit)
                                                <span class="badge bg-info">{{ $alteracao->unit->name }}</span>
                                            @else
                                                <span class="text-muted">N/A</span>
                                            @endif
                                        </td>
                                    @endif
                                    <td>
                                        <div>{{ $alteracao->data_formatada }}</div>
                                        <small class="text-muted">{{ $alteracao->created_at->diffForHumans() }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-{{ $alteracao->status_badge }}">
                                            {{ $alteracao->status_texto }}
                                        </span>
                                    </td>
                                    <td>
                                        <div>{{ $alteracao->user->name }}</div>
                                        <small class="text-muted">{{ $alteracao->user->email }}</small>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('alteracoes.show', $alteracao) }}" 
                                               class="btn btn-sm btn-outline-primary" 
                                               title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            
                                            @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                                                <a href="{{ route('alteracoes.edit', $alteracao) }}" 
                                                   class="btn btn-sm btn-outline-warning" 
                                                   title="Editar">
                                                    <i class="fas fa-edit"></i>
                                                </a>
                                                
                                                <button type="button" 
                                                        class="btn btn-sm btn-outline-danger" 
                                                        onclick="confirmDelete({{ $alteracao->id }})"
                                                        title="Excluir">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Paginação -->
                <div class="card-footer">
                    {{ $alteracoes->appends(request()->query())->links() }}
                </div>
            @else
                <div class="text-center py-5">
                    <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                    <h5 class="text-muted">Nenhuma alteração encontrada</h5>
                    <p class="text-muted">Não há alterações elétricas cadastradas ou que atendam aos filtros aplicados.</p>
                    <a href="{{ route('alteracoes.create') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeira Alteração
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>

<!-- Modal de Confirmação de Exclusão -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Confirmar Exclusão</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Tem certeza que deseja excluir esta alteração elétrica?</p>
                <p class="text-danger"><strong>Esta ação não pode ser desfeita!</strong></p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">Excluir</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
function confirmDelete(alteracaoId) {
    const form = document.getElementById('deleteForm');
    form.action = `/alteracoes/${alteracaoId}`;
    
    const modal = new bootstrap.Modal(document.getElementById('deleteModal'));
    modal.show();
}

// Auto-submit do formulário de filtros quando select muda
document.getElementById('status').addEventListener('change', function() {
    if (this.value) {
        this.form.submit();
    }
});
</script>
@endsection
