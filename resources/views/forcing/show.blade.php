@extends('layouts.app')

@section('title', 'Detalhes do Forcing')

@section('content')
<style>
/* CSS para Timeline */
.timeline-container {
    position: relative;
    padding-left: 30px;
}

.timeline-container::before {
    content: '';
    position: absolute;
    left: 15px;
    top: 0;
    bottom: 0;
    width: 2px;
    background: #e9ecef;
}

.timeline-item {
    position: relative;
    margin-bottom: 30px;
    padding-left: 40px;
}

.timeline-marker {
    position: absolute;
    left: -40px;
    top: 5px;
    width: 30px;
    height: 30px;
    border-radius: 50%;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 14px;
    border: 2px solid #e9ecef;
    background: #fff;
    z-index: 1;
}

.timeline-item.completed .timeline-marker {
    background: #28a745;
    border-color: #28a745;
    color: white;
}

.timeline-item.active .timeline-marker {
    background: #007bff;
    border-color: #007bff;
    color: white;
    animation: pulse 2s infinite;
}

.timeline-item.pending .timeline-marker {
    background: #ffc107;
    border-color: #ffc107;
    color: #212529;
}

.timeline-item.inactive .timeline-marker {
    background: #f8f9fa;
    border-color: #dee2e6;
    color: #6c757d;
}

.timeline-content {
    background: #f8f9fa;
    padding: 15px;
    border-radius: 8px;
    border-left: 4px solid #e9ecef;
}

.timeline-item.completed .timeline-content {
    border-left-color: #28a745;
}

.timeline-item.active .timeline-content {
    border-left-color: #007bff;
    background: #e3f2fd;
}

.timeline-item.pending .timeline-content {
    border-left-color: #ffc107;
    background: #fff8e1;
}

.timeline-header {
    margin-bottom: 10px;
}

.timeline-header h6 {
    margin: 0;
    font-weight: 600;
}

@keyframes pulse {
    0% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0.7);
    }
    70% {
        box-shadow: 0 0 0 10px rgba(0, 123, 255, 0);
    }
    100% {
        box-shadow: 0 0 0 0 rgba(0, 123, 255, 0);
    }
}

.alert-sm {
    padding: 8px 12px;
    font-size: 0.875rem;
}
</style>
<div class="row justify-content-center">
    <div class="col-md-10">
        <div class="card shadow">
            <div class="card-header bg-primary text-white">
                <div class="d-flex justify-content-between align-items-center">
                    <h4 class="mb-0"><i class="fas fa-info-circle"></i> Detalhes do Forcing #{{ $forcing->id }}</h4>
                    <div>
                        @if($forcing->status === 'pendente')
                            <span class="badge bg-secondary fs-6">
                                <i class="fas fa-clock"></i> PENDENTE
                            </span>
                        @elseif($forcing->status === 'liberado')
                            <span class="badge bg-success fs-6">
                                <i class="fas fa-check"></i> LIBERADO
                            </span>
                        @elseif($forcing->status === 'forcado')
                            <span class="badge bg-warning text-dark fs-6">
                                <i class="fas fa-exclamation-triangle"></i> FORÇADO
                            </span>
                        @elseif($forcing->status === 'solicitacao_retirada')
                            <span class="badge bg-info fs-6">
                                <i class="fas fa-paper-plane"></i> SOLICITAÇÃO DE RETIRADA
                            </span>
                        @else
                            <span class="badge bg-dark fs-6">
                                <i class="fas fa-check-double"></i> RETIRADO
                            </span>
                        @endif
                    </div>
                </div>
            </div>
            <div class="card-body">
                <!-- Timeline do Processo -->
                <div class="row mb-4">
                    <div class="col-12">
                        <h5 class="mb-3"><i class="fas fa-history"></i> Timeline do Processo</h5>
                        <div class="timeline-container">
                            <!-- Etapa 1: Criação -->
                            <div class="timeline-item {{ $forcing->status !== 'pendente' ? 'completed' : 'active' }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-plus-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="mb-1">Solicitação do Forcing</h6>
                                        <small class="text-muted">{{ $forcing->data_forcing->format('d/m/Y H:i:s') }}</small>
                                    </div>
                                    <p class="mb-1"><strong>Por:</strong> {{ $forcing->user->name }} ({{ $forcing->user->perfil }})</p>
                                    <p class="mb-1"><strong>TAG:</strong> <code>{{ $forcing->tag }}</code></p>
                                    @if($forcing->descricao_equipamento)
                                        <p class="mb-1"><strong>Equipamento:</strong> {{ $forcing->descricao_equipamento }}</p>
                                    @endif
                                    @if($forcing->area)
                                        <p class="mb-0"><strong>Área:</strong> {{ $forcing->area }}</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Etapa 2: Liberação -->
                            <div class="timeline-item {{ in_array($forcing->status, ['liberado', 'forcado', 'solicitacao_retirada', 'retirado']) ? 'completed' : ($forcing->status === 'pendente' ? 'pending' : 'inactive') }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="mb-1">Forcing Liberado</h6>
                                        @if($forcing->data_liberacao)
                                            <small class="text-muted">{{ $forcing->data_liberacao->format('d/m/Y H:i:s') }}</small>
                                        @elseif($forcing->status === 'liberado' || in_array($forcing->status, ['forcado', 'solicitacao_retirada', 'retirado']))
                                            <small class="text-muted">{{ $forcing->updated_at->format('d/m/Y H:i:s') }}</small>
                                        @else
                                            <small class="text-muted">Aguardando liberação</small>
                                        @endif
                                    </div>
                                    @if($forcing->liberador)
                                        @if($forcing->status === 'pendente')
                                            <p class="mb-1">
                                                <strong>Liberador Responsável:</strong> 
                                                {{ $forcing->liberador->name }} ({{ $forcing->liberador->perfil }})
                                            </p>
                                            <p class="mb-0">
                                                <small class="text-info">
                                                    <i class="fas fa-envelope"></i> 
                                                    Notificado por email em {{ $forcing->created_at->format('d/m/Y H:i') }}
                                                </small>
                                            </p>
                                        @else
                                            <p class="mb-1"><strong>Por:</strong> {{ $forcing->liberador->name }} ({{ $forcing->liberador->perfil }})</p>
                                            @if($forcing->observacoes_liberacao)
                                                <p class="mb-0"><strong>Observações da Liberação:</strong> {{ $forcing->observacoes_liberacao }}</p>
                                            @endif
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Aguardando ação do liberador</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Etapa 3: Execução -->
                            <div class="timeline-item {{ in_array($forcing->status, ['forcado', 'solicitacao_retirada', 'retirado']) ? 'completed' : ($forcing->status === 'liberado' ? 'pending' : 'inactive') }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-tools"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="mb-1">Equipamento Forçado</h6>
                                        @if($forcing->data_execucao)
                                            <small class="text-muted">{{ $forcing->data_execucao->format('d/m/Y H:i:s') }}</small>
                                        @else
                                            <small class="text-muted">Aguardando execução</small>
                                        @endif
                                    </div>
                                    @if($forcing->executante)
                                        <p class="mb-1"><strong>Por:</strong> {{ $forcing->executante->name }} ({{ $forcing->executante->perfil }})</p>
                                        <p class="mb-1"><strong>Local:</strong> <span class="badge bg-info">{{ $forcing->getLocalExecucaoTexto() }}</span></p>
                                        @if($forcing->observacoes_execucao)
                                            <p class="mb-0"><strong>Observações:</strong> {{ $forcing->observacoes_execucao }}</p>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Aguardando ação do executante</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Etapa 4: Solicitação de Retirada -->
                            <div class="timeline-item {{ in_array($forcing->status, ['solicitacao_retirada', 'retirado']) ? 'completed' : ($forcing->status === 'forcado' ? 'pending' : 'inactive') }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-paper-plane"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="mb-1">Solicitação de Retirada</h6>
                                        @if($forcing->data_solicitacao_retirada)
                                            <small class="text-muted">{{ $forcing->data_solicitacao_retirada->format('d/m/Y H:i:s') }}</small>
                                        @else
                                            <small class="text-muted">Aguardando solicitação</small>
                                        @endif
                                    </div>
                                    @if($forcing->solicitadoRetiradaPor)
                                        <p class="mb-1"><strong>Por:</strong> {{ $forcing->solicitadoRetiradaPor->name }} ({{ $forcing->solicitadoRetiradaPor->perfil }})</p>
                                        @if($forcing->descricao_resolucao)
                                            <div class="alert alert-info alert-sm mb-0">
                                                <strong>Descrição da Resolução:</strong> {{ $forcing->descricao_resolucao }}
                                            </div>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Aguardando solicitação de qualquer usuário</p>
                                    @endif
                                </div>
                            </div>

                            <!-- Etapa 5: Retirada -->
                            <div class="timeline-item {{ $forcing->status === 'retirado' ? 'completed' : ($forcing->status === 'solicitacao_retirada' ? 'pending' : 'inactive') }}">
                                <div class="timeline-marker">
                                    <i class="fas fa-check-double"></i>
                                </div>
                                <div class="timeline-content">
                                    <div class="timeline-header">
                                        <h6 class="mb-1">Forcing Retirado</h6>
                                        @if($forcing->data_retirada)
                                            <small class="text-muted">{{ $forcing->data_retirada->format('d/m/Y H:i:s') }}</small>
                                        @else
                                            <small class="text-muted">Aguardando retirada</small>
                                        @endif
                                    </div>
                                    @if($forcing->retiradoPor)
                                        <p class="mb-1"><strong>Por:</strong> {{ $forcing->retiradoPor->name }} ({{ $forcing->retiradoPor->perfil }})</p>
                                        @if($forcing->observacoes_retirada)
                                            <p class="mb-1"><strong>Observações da Retirada:</strong> {{ $forcing->observacoes_retirada }}</p>
                                        @endif
                                        @if($forcing->descricao_resolucao)
                                            <div class="alert alert-success alert-sm mb-0">
                                                <strong>Resolução:</strong> {{ $forcing->descricao_resolucao }}
                                            </div>
                                        @endif
                                    @else
                                        <p class="mb-0 text-muted">Aguardando ação do executante</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="row">
                    <!-- Informações Básicas -->
                    <div class="col-md-6">
                        <div class="card bg-light">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-info"></i> Informações Básicas</h6>
                                <table class="table table-sm">
                                    <tr>
                                        <td><strong>Data do Forcing:</strong></td>
                                        <td>{{ $forcing->data_forcing->format('d/m/Y H:i:s') }}</td>
                                    </tr>
                                    @if($forcing->data_liberacao || ($forcing->status === 'liberado' || in_array($forcing->status, ['forcado', 'solicitacao_retirada', 'retirado'])))
                                    <tr>
                                        <td><strong>Data de Liberação:</strong></td>
                                        <td>
                                            <span class="text-success">
                                                <i class="fas fa-check-circle"></i> 
                                                @if($forcing->data_liberacao)
                                                    {{ $forcing->data_liberacao->format('d/m/Y H:i:s') }}
                                                @else
                                                    {{ $forcing->updated_at->format('d/m/Y H:i:s') }}
                                                @endif
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    <tr>
                                        <td><strong>Status:</strong></td>
                                        <td>
                                            @if($forcing->status === 'pendente')
                                                <span class="badge bg-secondary">Pendente</span>
                                            @elseif($forcing->status === 'liberado')
                                                <span class="badge bg-success">Liberado</span>
                                            @elseif($forcing->status === 'forcado')
                                                <span class="badge bg-warning text-dark">Forçado</span>
                                            @elseif($forcing->status === 'solicitacao_retirada')
                                                <span class="badge bg-info">Solicitação de Retirada</span>
                                            @else
                                                <span class="badge bg-dark">Retirado</span>
                                            @endif
                                        </td>
                                    </tr>
                                    @if($forcing->liberador)
                                    <tr>
                                        <td><strong>
                                            @if($forcing->status === 'pendente')
                                                Liberador Responsável:
                                            @else
                                                Liberado por:
                                            @endif
                                        </strong></td>
                                        <td>
                                            @if($forcing->status === 'pendente')
                                                <span class="text-warning">
                                                    <i class="fas fa-user-clock"></i> 
                                                    {{ $forcing->liberador->name }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $forcing->liberador->empresa }} - {{ $forcing->liberador->setor }}</small>
                                                <br>
                                                <small class="text-info">
                                                    <i class="fas fa-envelope"></i> 
                                                    Notificado por email
                                                </small>
                                            @else
                                                <span class="text-success">
                                                    <i class="fas fa-user-check"></i> 
                                                    {{ $forcing->liberador->name }}
                                                </span>
                                                <br>
                                                <small class="text-muted">{{ $forcing->liberador->empresa }} - {{ $forcing->liberador->setor }}</small>
                                            @endif
                                        </td>
                                    </tr>
                                    @endif
                                    @if($forcing->data_execucao)
                                    <tr>
                                        <td><strong>Data de Execução:</strong></td>
                                        <td>
                                            <span class="text-primary">
                                                <i class="fas fa-tools"></i> 
                                                {{ $forcing->data_execucao->format('d/m/Y H:i:s') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($forcing->executante)
                                    <tr>
                                        <td><strong>Executante:</strong></td>
                                        <td>
                                            <span class="text-primary">
                                                <i class="fas fa-user-cog"></i> 
                                                {{ $forcing->executante->name }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $forcing->executante->empresa }} - {{ $forcing->executante->setor }}</small>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($forcing->data_solicitacao_retirada)
                                    <tr>
                                        <td><strong>Data Solicitação Retirada:</strong></td>
                                        <td>
                                            <span class="text-info">
                                                <i class="fas fa-paper-plane"></i> 
                                                {{ $forcing->data_solicitacao_retirada->format('d/m/Y H:i:s') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($forcing->data_retirada)
                                    <tr>
                                        <td><strong>Data de Retirada:</strong></td>
                                        <td>
                                            <span class="text-dark">
                                                <i class="fas fa-check-double"></i> 
                                                {{ $forcing->data_retirada->format('d/m/Y H:i:s') }}
                                            </span>
                                        </td>
                                    </tr>
                                    @endif
                                    @if($forcing->retiradoPor)
                                    <tr>
                                        <td><strong>Retirado por:</strong></td>
                                        <td>
                                            <span class="text-dark">
                                                <i class="fas fa-user-times"></i> 
                                                {{ $forcing->retiradoPor->name }}
                                            </span>
                                            <br>
                                            <small class="text-muted">{{ $forcing->retiradoPor->empresa }} - {{ $forcing->retiradoPor->setor }}</small>
                                        </td>
                                    </tr>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>

                    <!-- Informações do Equipamento -->
                    <div class="col-md-6">
                        <div class="card bg-info text-white">
                            <div class="card-body">
                                <h6 class="card-title"><i class="fas fa-cogs"></i> Informações do Equipamento</h6>
                                <table class="table table-sm text-white">
                                    <tr>
                                        <td><strong>Situação:</strong></td>
                                        <td>
                                            @if($forcing->situacao_equipamento === 'desativado')
                                                <span class="badge bg-secondary">{{ $forcing->getSituacaoEquipamentoTexto() }}</span>
                                            @elseif($forcing->situacao_equipamento === 'ativacao_futura')
                                                <span class="badge bg-warning text-dark">{{ $forcing->getSituacaoEquipamentoTexto() }}</span>
                                            @else
                                                <span class="badge bg-success">{{ $forcing->getSituacaoEquipamentoTexto() }}</span>
                                            @endif
                                        </td>
                                    </tr>
                                    <tr>
                                        <td><strong>TAG:</strong></td>
                                        <td><code class="text-white bg-dark px-2 py-1 rounded">{{ $forcing->tag }}</code></td>
                                    </tr>
                                    <tr>
                                        <td><strong>Descrição:</strong></td>
                                        <td>{{ $forcing->descricao_equipamento }}</td>
                                    </tr>
                                    <tr>
                                        <td><strong>Área:</strong></td>
                                        <td><span class="badge bg-light text-dark">{{ $forcing->area }}</span></td>
                                    </tr>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Ações -->
                <div class="d-flex justify-content-between mt-4">
                    <a href="{{ route('forcing.index') }}" class="btn btn-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar para Lista
                    </a>
                    <div>
                        @if($forcing->status !== 'retirado')
                            @if(auth()->user()->perfil === 'admin' || $forcing->user_id === auth()->id())
                                <a href="{{ route('forcing.edit', $forcing) }}" class="btn btn-warning">
                                    <i class="fas fa-edit"></i> Editar
                                </a>
                            @endif
                        @else
                            <button class="btn btn-outline-secondary" disabled title="Forcing concluído não pode ser editado">
                                <i class="fas fa-lock"></i> Processo Concluído
                            </button>
                        @endif
                        
                        @if(auth()->user()->perfil === 'admin' && $forcing->status !== 'retirado')
                            <form action="{{ route('forcing.destroy', $forcing) }}" method="POST" class="d-inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" 
                                        onclick="return confirm('Tem certeza que deseja excluir?')">
                                    <i class="fas fa-trash"></i> Excluir
                                </button>
                            </form>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal para registrar execução -->
@if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status_execucao === 'pendente' && !$forcing->executante)
    <div class="modal fade" id="execucaoModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="{{ route('forcing.registrar-execucao', $forcing) }}" method="POST">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">Registrar Execução</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <p><strong>Forcing:</strong> {{ $forcing->titulo }}</p>
                        
                        <div class="mb-3">
                            <label for="local_execucao" class="form-label">Local de Execução <span class="text-danger">*</span></label>
                            <select class="form-select" id="local_execucao" name="local_execucao" required>
                                <option value="">Selecione o local...</option>
                                <option value="supervisorio">Supervisório</option>
                                <option value="plc">PLC</option>
                                <option value="local">Local</option>
                            </select>
                        </div>

                        <div class="mb-3">
                            <label for="observacoes_execucao" class="form-label">Observações da Execução</label>
                            <textarea class="form-control" id="observacoes_execucao" 
                                      name="observacoes_execucao" rows="3" placeholder="Observações sobre a execução (opcional)"></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-tools"></i> Registrar Execução
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endif
@endsection
