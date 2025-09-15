@extends('layouts.app')

@section('title', $alteracao->numero_documento . ' - Sistema de Forcing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-bolt text-warning"></i>
                        {{ $alteracao->numero_documento }}
                    </h2>
                    <p class="text-muted mb-0">
                        Versão {{ $alteracao->versao }} - 
                        <span class="badge bg-{{ $alteracao->status_badge }}">
                            {{ $alteracao->status_texto }}
                        </span>
                    </p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('alteracoes.index') }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    
                    @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                        <a href="{{ route('alteracoes.edit', $alteracao) }}" class="btn btn-outline-warning">
                            <i class="fas fa-edit"></i> Editar
                        </a>
                    @endif
                    
                    <a href="{{ route('alteracoes.pdf', $alteracao) }}" class="btn btn-outline-primary" target="_blank" rel="noopener noreferrer">
                        <i class="fas fa-file-pdf"></i> PDF
                    </a>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Documento Principal -->
        <div class="col-lg-8">
            <div class="card shadow-sm">
                <div class="card-header bg-light">
                    <div class="row align-items-center">
                        <div class="col">
                            <h4 class="mb-0 text-dark">
                                <i class="fas fa-file-alt text-primary"></i>
                                CONTROLE DE ALTERAÇÕES ELÉTRICAS E LÓGICAS
                            </h4>
                        </div>
                        <div class="col-auto">
                            <small class="text-muted">
                                {{ $alteracao->numero_documento }} - Versão {{ $alteracao->versao }}<br>
                                Publicado em: {{ $alteracao->data_publicacao->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <!-- Informações do Solicitante -->
                    <div class="row mb-4">
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary">Solicitante:</label>
                            <p class="form-control-plaintext border-bottom pb-2">{{ $alteracao->solicitante }}</p>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary">Departamento:</label>
                            <p class="form-control-plaintext border-bottom pb-2">{{ $alteracao->departamento }}</p>
                        </div>
                        
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-primary">Data:</label>
                            <p class="form-control-plaintext border-bottom pb-2">{{ $alteracao->data_formatada }}</p>
                        </div>
                    </div>


                    <hr class="my-4">

                    <!-- Descrição da Alteração -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            DESCRIÇÃO DA ALTERAÇÃO NECESSÁRIA:
                        </label>
                        <div class="border p-3 rounded bg-light">
                            <p class="mb-0">{{ $alteracao->descricao_alteracao }}</p>
                        </div>
                    </div>

                    <hr class="my-4">

                    <!-- Motivo da Alteração -->
                    <div class="mb-4">
                        <label class="form-label fw-bold text-primary">
                            MOTIVO DA ALTERAÇÃO:
                        </label>
                        <div class="border p-3 rounded bg-light">
                            <p class="mb-0">{{ $alteracao->motivo_alteracao }}</p>
                        </div>
                    </div>


                    <!-- Termo de Concordância -->
                    <div class="alert alert-info">
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" checked disabled>
                            <label class="form-check-label fw-bold">
                                Estou ciente das alterações acima descritas e concordo que permanecerão válidas por tempo indeterminado até que uma nova versão deste documento seja validada pelos responsáveis.
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar com Aprovações e Ações -->
        <div class="col-lg-4">
            <!-- Status e Ações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-cogs"></i> Status e Ações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="text-center mb-3">
                        <span class="badge bg-{{ $alteracao->status_badge }} fs-6">
                            {{ $alteracao->status_texto }}
                        </span>
                    </div>

                    @if($alteracao->podeSerAprovada())
                        @php
                            $tiposPermitidos = auth()->user()->tiposAprovacaoPermitidos();
                        @endphp
                        
                        @if(!empty($tiposPermitidos))
                            <div class="mb-2">
                                <small class="text-muted d-block mb-2">
                                    <i class="fas fa-info-circle"></i> 
                                    Seu setor: <strong>{{ auth()->user()->setor }}</strong>
                                </small>
                                
                                @foreach($tiposPermitidos as $tipo)
                                    @if($alteracao->podeSerAprovadaPor($tipo))
                                        <button class="btn btn-success btn-sm w-100 mb-2" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#aprovarModal"
                                                data-tipo="{{ $tipo }}">
                                            <i class="fas fa-check"></i> 
                                            @if($tipo === 'gerente')
                                                Aprovar como Gerente
                                            @elseif($tipo === 'coordenador')
                                                Aprovar como Coordenador
                                            @elseif($tipo === 'tecnico')
                                                Aprovar como Técnico
                                            @endif
                                        </button>
                                    @endif
                                @endforeach
                            </div>
                        @else
                            <div class="alert alert-warning p-2 mb-2">
                                <small>
                                    <i class="fas fa-exclamation-triangle"></i> 
                                    <strong>Sem permissão:</strong><br>
                                    Seu setor ({{ auth()->user()->setor }}) não tem permissão para aprovar alterações.<br>
                                    <strong>Setores permitidos:</strong><br>
                                    • Gerente/Coordenador: Manutenção<br>
                                    • Técnico Especialista: Automação, Elétrica, Instrumentação, Técnico Eletricista
                                </small>
                            </div>
                        @endif
                    @endif

                    @if($alteracao->podeSerRejeitada() && (auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin))
                        <button class="btn btn-danger btn-sm w-100 mb-2" data-bs-toggle="modal" data-bs-target="#rejeitarModal">
                            <i class="fas fa-times"></i> Rejeitar
                        </button>
                    @endif

                    @if($alteracao->podeSerImplementada() && auth()->user()->podeImplementarAlteracoes())
                        <form method="POST" action="{{ route('alteracoes.implementar', $alteracao) }}" class="d-inline">
                            @csrf
                            <button type="submit" class="btn btn-primary btn-sm w-100 mb-2">
                                <i class="fas fa-play"></i> Marcar como Implementada
                            </button>
                        </form>
                    @elseif($alteracao->status === 'aprovada' && !$alteracao->podeSerImplementada())
                        <div class="alert alert-info p-2 mb-2">
                            <small>
                                <i class="fas fa-info-circle"></i> 
                                Aguardando aprovação de todos os responsáveis
                            </small>
                        </div>
                    @elseif($alteracao->podeSerImplementada() && !auth()->user()->podeImplementarAlteracoes())
                        <div class="alert alert-warning p-2 mb-2">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Sem permissão para implementar:</strong><br>
                                Apenas administradores e usuários dos setores técnicos podem implementar alterações.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Aprovações -->
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-signature"></i> Aprovações
                    </h5>
                </div>
                <div class="card-body">
                    <!-- Gerente de Manutenção -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>Gerente de Manutenção</strong>
                            @if($alteracao->gerente_manutencao)
                                <div class="text-success">
                                    <i class="fas fa-check-circle"></i> {{ $alteracao->gerente_manutencao }}
                                </div>
                                <small class="text-muted">{{ $alteracao->data_aprovacao_gerente->format('d/m/Y H:i') }}</small>
                            @else
                                <div class="text-warning">
                                    <i class="fas fa-clock"></i> Pendente
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Coordenador de Manutenção -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>Coordenador de Manutenção</strong>
                            @if($alteracao->coordenador_manutencao)
                                <div class="text-success">
                                    <i class="fas fa-check-circle"></i> {{ $alteracao->coordenador_manutencao }}
                                </div>
                                <small class="text-muted">{{ $alteracao->data_aprovacao_coordenador->format('d/m/Y H:i') }}</small>
                            @else
                                <div class="text-warning">
                                    <i class="fas fa-clock"></i> Pendente
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Técnico Especialista -->
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <div>
                            <strong>Técnico Especialista</strong>
                            @if($alteracao->tecnico_especialista)
                                <div class="text-success">
                                    <i class="fas fa-check-circle"></i> {{ $alteracao->tecnico_especialista }}
                                </div>
                                <small class="text-muted">{{ $alteracao->data_aprovacao_tecnico->format('d/m/Y H:i') }}</small>
                            @else
                                <div class="text-warning">
                                    <i class="fas fa-clock"></i> Pendente
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Status das Aprovações -->
                    @if($alteracao->todosAprovaram())
                        <div class="alert alert-success p-2 mt-3">
                            <small>
                                <i class="fas fa-check-double"></i> 
                                <strong>Todas as aprovações concluídas!</strong><br>
                                A alteração pode ser implementada.
                            </small>
                        </div>
                    @else
                        <div class="alert alert-warning p-2 mt-3">
                            <small>
                                <i class="fas fa-exclamation-triangle"></i> 
                                <strong>Aguardando aprovações:</strong><br>
                                {{ $alteracao->aprovaçõesPendentes() }} de 3 responsáveis ainda precisam aprovar.
                            </small>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Informações do Sistema -->
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">
                        <i class="fas fa-info-circle"></i> Informações
                    </h5>
                </div>
                <div class="card-body">
                    <div class="row mb-2">
                        <div class="col-5"><strong>Criado por:</strong></div>
                        <div class="col-7">{{ $alteracao->user->name }}</div>
                    </div>
                    @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                        <div class="row mb-2">
                            <div class="col-5"><strong>Unidade:</strong></div>
                            <div class="col-7">
                                @if($alteracao->unit)
                                    <span class="badge bg-info">{{ $alteracao->unit->name }}</span>
                                @else
                                    <span class="text-muted">N/A</span>
                                @endif
                            </div>
                        </div>
                    @endif
                    <div class="row mb-2">
                        <div class="col-5"><strong>Criado em:</strong></div>
                        <div class="col-7">{{ $alteracao->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="row mb-2">
                        <div class="col-5"><strong>Atualizado em:</strong></div>
                        <div class="col-7">{{ $alteracao->updated_at->format('d/m/Y H:i') }}</div>
                    </div>
                    @if($alteracao->comentarios_rejeicao)
                        <hr>
                        <div class="row">
                            <div class="col-12">
                                <strong>Motivo da Rejeição:</strong>
                                <p class="text-danger mt-2">{{ $alteracao->comentarios_rejeicao }}</p>
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal de Aprovação -->
<div class="modal fade" id="aprovarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Aprovar Alteração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('alteracoes.aprovar', $alteracao) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="tipo_aprovador_display" class="form-label">Tipo de Aprovador</label>
                        <input type="text" class="form-control" id="tipo_aprovador_display" readonly>
                        <input type="hidden" id="tipo_aprovador" name="tipo_aprovador">
                        <small class="text-muted">
                            <i class="fas fa-info-circle"></i> 
                            Apenas usuários do setor apropriado podem realizar esta aprovação.
                        </small>
                    </div>
                    <div class="mb-3">
                        <label for="nome_aprovador" class="form-label">Nome do Aprovador</label>
                        <input type="text" class="form-control" id="nome_aprovador" name="nome_aprovador" 
                               value="{{ auth()->user()->name }}" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-success">Confirmar Aprovação</button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Captura o tipo quando o botão é clicado
    document.querySelectorAll('[data-bs-target="#aprovarModal"]').forEach(function(button) {
        button.addEventListener('click', function() {
            const tipo = this.getAttribute('data-tipo');
            const tipoDisplay = document.getElementById('tipo_aprovador_display');
            const tipoHidden = document.getElementById('tipo_aprovador');
            
            let tipoTexto = '';
            switch(tipo) {
                case 'gerente':
                    tipoTexto = 'Gerente de Manutenção';
                    break;
                case 'coordenador':
                    tipoTexto = 'Coordenador de Manutenção';
                    break;
                case 'tecnico':
                    tipoTexto = 'Técnico Especialista Automação';
                    break;
            }
            
            tipoDisplay.value = tipoTexto;
            tipoHidden.value = tipo;
        });
    });
});
</script>

<!-- Modal de Rejeição -->
<div class="modal fade" id="rejeitarModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Rejeitar Alteração</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST" action="{{ route('alteracoes.rejeitar', $alteracao) }}">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="comentarios_rejeicao" class="form-label">Motivo da Rejeição</label>
                        <textarea class="form-control" id="comentarios_rejeicao" name="comentarios_rejeicao" 
                                  rows="4" placeholder="Explique o motivo da rejeição..." required></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="submit" class="btn btn-danger">Rejeitar</button>
                </div>
            </form>
        </div>
    </div>
</div>

<style>
.card-header {
    border-bottom: 3px solid #0d6efd;
}

.form-label {
    color: #2c3e50;
    font-size: 0.95rem;
}

.border-bottom {
    border-bottom: 2px solid #e9ecef !important;
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    border: 2px solid #17a2b8;
    border-radius: 8px;
}

.text-success {
    color: #198754 !important;
}

.text-danger {
    color: #dc3545 !important;
}

.btn-group .btn {
    border-radius: 0.375rem;
    margin-left: 0.25rem;
}

.btn-group .btn:first-child {
    margin-left: 0;
}
</style>
@endsection
