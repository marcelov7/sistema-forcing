@extends('layouts.app')

@section('title', 'Lista de Forcing')

@section('content')
<div class="container-fluid px-3">
<!-- Header responsivo -->
<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3">
    <div class="mb-2 mb-md-0">
        <h1 class="h3 mb-0"><i class="fas fa-exclamation-triangle"></i> Controle de Forcing</h1>
        <small class="text-muted">Sistema de gerenciamento de forcing</small>
    </div>
    <div class="d-flex flex-column flex-sm-row gap-2">
        <!-- Toggle de Visualização -->
        <div class="btn-group btn-group-sm" role="group" aria-label="Visualização">
            <button type="button" class="btn btn-outline-secondary" id="viewListBtn" title="Visualização em Lista">
                <i class="fas fa-list"></i>
                <span class="d-none d-sm-inline ms-1">Lista</span>
            </button>
            <button type="button" class="btn btn-outline-secondary" id="viewCardsBtn" title="Visualização em Cards">
                <i class="fas fa-th-large"></i>
                <span class="d-none d-sm-inline ms-1">Cards</span>
            </button>
        </div>
        
        <button id="refreshTableBtn" class="btn btn-outline-primary btn-sm" title="Atualizar Lista" onclick="window.location.reload();">
            <i class="fas fa-sync-alt" id="refreshIcon"></i> 
            <span class="d-none d-sm-inline">Atualizar</span>
        </button>
        <a href="{{ route('forcing.terms') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> <span class="d-none d-sm-inline">Novo Forcing</span>
        </a>
    </div>
</div>

<!-- Banner de Notificação para Solicitações de Retirada -->
@php
    $solicitacoesRetirada = $forcings->where('status', 'solicitacao_retirada');
@endphp

@if($solicitacoesRetirada->count() > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <div class="d-flex align-items-center">
            <i class="fas fa-bell fa-2x me-3"></i>
            <div class="flex-grow-1">
                <h5 class="alert-heading mb-2">
                    <i class="fas fa-exclamation-triangle"></i> 
                    {{ $solicitacoesRetirada->count() }} Solicitação(ões) de Retirada Pendente(s)
                </h5>
                @foreach($solicitacoesRetirada as $forcing)
                    <div class="border-start border-warning border-3 ps-3 mb-2">
                        <strong>TAG:</strong> <code class="text-primary">{{ $forcing->tag }}</code> |
                        <strong>Equipamento:</strong> {{ Str::limit($forcing->descricao_equipamento, 40) }} |
                        <strong>Área:</strong> <span class="badge bg-secondary">{{ $forcing->area }}</span>
                        <br>
                        <small class="text-muted">
                            <i class="fas fa-user"></i> 
                            Solicitado por: <strong>{{ $forcing->solicitadoRetiradaPor->name ?? 'N/A' }}</strong> 
                            em {{ $forcing->data_solicitacao_retirada->format('d/m/Y H:i') }}
                        </small>
                    </div>
                @endforeach
                <small class="text-muted">
                    <i class="fas fa-info-circle"></i> 
                    Executantes: Verifiquem os detalhes antes de proceder com a retirada.
                </small>
            </div>
        </div>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
@endif

<!-- Painel de Filtros -->
<div class="card mb-4">
    <div class="card-header bg-light">
        <div class="d-flex justify-content-between align-items-center">
            <h6 class="mb-0">
                <i class="fas fa-filter"></i> Filtros
                <span id="filtrosAtivosBadge" class="badge bg-primary ms-2 d-none">
                    <i class="fas fa-check"></i> Ativos
                </span>
            </h6>
            <button class="btn btn-sm btn-outline-secondary" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosCollapse">
                <i class="fas fa-chevron-down"></i> <span class="d-none d-sm-inline">Expandir</span>
            </button>
        </div>
    </div>
    <div class="collapse" id="filtrosCollapse">
        <div class="card-body">
            <form method="GET" action="{{ route('forcing.index') }}" id="filtroForm">
                <!-- Filtros Principais - Sempre Visíveis -->
                <div class="row g-2 mb-3">
                    <!-- Busca por TAG/Descrição -->
                    <div class="col-12 col-md-6">
                        <label class="form-label small">🔍 Buscar TAG/Equipamento</label>
                        <input type="text" class="form-control" name="busca" 
                               value="{{ request('busca') }}" 
                               placeholder="Digite TAG ou descrição...">
                    </div>

                    <!-- Filtro por Status -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small">📊 Status</label>
                        <select class="form-select" name="status">
                            <option value="todos" {{ request('status') == 'todos' ? 'selected' : '' }}>Todos</option>
                            <option value="pendente" {{ request('status') == 'pendente' ? 'selected' : '' }}>⏳ Pendente</option>
                            <option value="liberado" {{ request('status') == 'liberado' ? 'selected' : '' }}>✅ Liberado</option>
                            <option value="forcado" {{ request('status') == 'forcado' ? 'selected' : '' }}>⚠️ Forçado</option>
                            <option value="solicitacao_retirada" {{ request('status') == 'solicitacao_retirada' ? 'selected' : '' }}>✈️ Sol. Retirada</option>
                            <option value="retirado" {{ request('status') == 'retirado' ? 'selected' : '' }}>✅✅ Retirado</option>
                        </select>
                    </div>

                    <!-- Filtro por Área -->
                    <div class="col-6 col-md-3">
                        <label class="form-label small">🏭 Área</label>
                        <select class="form-select" name="area">
                            <option value="todas" {{ request('area') == 'todas' ? 'selected' : '' }}>Todas</option>
                            @foreach($areas as $area)
                                <option value="{{ $area }}" {{ request('area') == $area ? 'selected' : '' }}>
                                    {{ $area }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <!-- Filtros Avançados - Collapsible em Mobile -->
                <div class="d-md-block">
                    <div class="d-md-none mb-2">
                        <button class="btn btn-sm btn-outline-info w-100" type="button" data-bs-toggle="collapse" data-bs-target="#filtrosAvancados">
                            <i class="fas fa-cogs"></i> Filtros Avançados
                        </button>
                    </div>
                    
                    <div class="collapse d-md-block" id="filtrosAvancados">
                        <div class="row g-2 mb-3">
                            <!-- Filtro por Situação -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small">⚙️ Situação</label>
                                <select class="form-select" name="situacao">
                                    <option value="todas" {{ request('situacao') == 'todas' ? 'selected' : '' }}>Todas</option>
                                    <option value="ativado" {{ request('situacao') == 'ativado' ? 'selected' : '' }}>🟢 Ativado</option>
                                    <option value="desativado" {{ request('situacao') == 'desativado' ? 'selected' : '' }}>🔴 Desativado</option>
                                    <option value="ativacao_futura" {{ request('situacao') == 'ativacao_futura' ? 'selected' : '' }}>🟡 Ativação Futura</option>
                                </select>
                            </div>

                            <!-- Filtro por Criador -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small">👤 Criado por</label>
                                <select class="form-select" name="criador">
                                    <option value="todos" {{ request('criador') == 'todos' ? 'selected' : '' }}>Todos</option>
                                    @foreach($criadores as $criador)
                                        <option value="{{ $criador->id }}" {{ request('criador') == $criador->id ? 'selected' : '' }}>
                                            {{ $criador->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Filtros de Data -->
                            <div class="col-6 col-md-3">
                                <label class="form-label small">📅 Data Início</label>
                                <input type="date" class="form-control" name="data_inicio" value="{{ request('data_inicio') }}">
                            </div>

                            <div class="col-6 col-md-3">
                                <label class="form-label small">📅 Data Fim</label>
                                <input type="date" class="form-control" name="data_fim" value="{{ request('data_fim') }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Botões de Ação -->
                <div class="row g-2">
                    <!-- Botões Principais -->
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-2 justify-content-start">
                            <button type="submit" class="btn btn-primary btn-sm">
                                <i class="fas fa-search"></i> <span class="d-none d-sm-inline">Filtrar</span>
                            </button>
                            <button type="button" class="btn btn-outline-secondary btn-sm" onclick="limparTodosFiltros()" title="Limpar todos os filtros">
                                <i class="fas fa-times"></i> <span class="d-none d-sm-inline">Limpar</span>
                            </button>
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="mostrarFiltrosAtivos()" title="Ver filtros ativos">
                                <i class="fas fa-info-circle"></i> <span class="d-none d-sm-inline">Info</span>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Botões de Filtros Rápidos -->
                <div class="row g-2 mt-2">
                    <div class="col-12">
                        <div class="d-flex flex-wrap gap-1 justify-content-start">
                            <button type="button" class="btn btn-outline-info btn-sm" onclick="aplicarFiltrosRapidos('forcado')">
                                ⚠️ <span class="d-none d-sm-inline">Ativos</span>
                            </button>
                            <button type="button" class="btn btn-outline-success btn-sm" onclick="aplicarFiltrosRapidos('retirado')">
                                ✅ <span class="d-none d-sm-inline">Concluídos</span>
                            </button>
                            <button type="button" class="btn btn-outline-warning btn-sm" onclick="aplicarFiltrosRapidos('solicitacao_retirada')">
                                🔔 <span class="d-none d-sm-inline">Pendente Retirada</span>
                            </button>
                        </div>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Resumo dos Resultados -->
@if(request()->hasAny(['status', 'area', 'situacao', 'criador', 'data_inicio', 'data_fim', 'busca']))
    <div class="alert alert-info mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <i class="fas fa-info-circle"></i> 
                <strong>{{ $forcings->count() }}</strong> forcing(s) encontrado(s) 
                @if(request('busca'))
                    para "<strong>{{ request('busca') }}</strong>"
                @endif
                @if(request('status') && request('status') !== 'todos')
                    • Status: <span class="badge bg-primary">{{ ucfirst(request('status')) }}</span>
                @endif
                @if(request('area') && request('area') !== 'todas')
                    • Área: <span class="badge bg-secondary">{{ request('area') }}</span>
                @endif
            </div>
            <small class="text-muted">Total no sistema: {{ \App\Models\Forcing::count() }}</small>
        </div>
    </div>
@endif

@if($forcings->count() > 0)
    <!-- Container da tabela responsiva -->
    <div class="table-responsive-container">
        <div class="card shadow-sm border-0">
            <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center py-2">
                <h6 class="mb-0 text-muted">
                    <i class="fas fa-list"></i> Lista de Forcings 
                    <span class="badge bg-primary ms-2">{{ $forcings->total() }}</span>
                </h6>
                <div class="d-flex align-items-center gap-2">
                    <small class="text-muted d-none d-md-inline">Mostrando {{ $forcings->firstItem() ?? 0 }} a {{ $forcings->lastItem() ?? 0 }} de {{ $forcings->total() }}</small>
                    <button class="btn btn-sm btn-outline-secondary" onclick="toggleFullscreen()" title="Tela cheia">
                        <i class="fas fa-expand"></i>
                    </button>
                </div>
            </div>
                <div class="card-body p-0">
                    <!-- Visualização em Lista -->
                    <div id="table-container" class="view-container">
                        <div class="table-responsive">
                        <table class="table table-hover table-striped mb-0">
                            <thead class="table-dark sticky-top">
                                <tr>
                                    <th class="text-center" style="width: 50px;">#</th>
                                    <th style="min-width: 200px;">TAG/Descrição</th>
                                    <th style="min-width: 100px;">Área</th>
                                    <th style="min-width: 120px;">Status</th>
                                    <th style="min-width: 150px;">Criado por</th>
                                    <th style="min-width: 150px;">Empresa/Setor</th>
                                    <th style="min-width: 130px;">Data do Forcing</th>
                                    <th style="min-width: 120px;">Liberador</th>
                                    <th style="min-width: 150px;">Data Liberação/Retirada</th>
                                    <th style="min-width: 120px;">Executante</th>
                                    <th style="min-width: 120px;">Local Execução</th>
                                    <th style="min-width: 120px;">Status Execução</th>
                                    <th class="text-center" style="width: 100px;">Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($forcings as $forcing)
                                    <tr @if(request('highlight') == $forcing->id) class="table-warning" style="border: 2px solid #ffc107; animation: pulse 2s infinite;" @endif>
                                        <td>{{ $forcing->id }}</td>
                                        <td>
                                            <code class="text-primary">{{ $forcing->tag }}</code>
                                            <br><small class="text-muted">{{ Str::limit($forcing->descricao_equipamento, 50) }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-secondary">{{ $forcing->area }}</span>
                                        </td>
                                        <td>
                                            @if($forcing->status === 'pendente')
                                                <span class="badge bg-secondary">
                                                    <i class="fas fa-clock"></i> Pendente
                                                </span>
                                            @elseif($forcing->status === 'liberado')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Liberado
                                                </span>
                                            @elseif($forcing->status === 'forcado')
                                                <span class="badge bg-warning text-dark">
                                                    <i class="fas fa-exclamation-triangle"></i> Forçado
                                                </span>
                                            @elseif($forcing->status === 'solicitacao_retirada')
                                                <span class="badge bg-info">
                                                    <i class="fas fa-paper-plane"></i> Sol. Retirada
                                                </span>
                                            @else
                                                <span class="badge bg-dark">
                                                    <i class="fas fa-check-double"></i> Retirado
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <strong>{{ $forcing->user->name }}</strong>
                                            <br><small class="text-muted">{{ $forcing->user->username }}</small>
                                        </td>
                                        <td>
                                            <strong>{{ $forcing->user->empresa }}</strong>
                                            <br><small class="text-muted">{{ $forcing->user->setor }}</small>
                                        </td>
                                        <td>
                                            <small>{{ $forcing->data_forcing->format('d/m/Y H:i') }}</small>
                                        </td>
                                        <td>
                                            @if($forcing->liberador)
                                                <strong>{{ $forcing->liberador->name }}</strong>
                                                <br><small class="text-muted">{{ $forcing->liberador->username }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($forcing->status === 'liberado' && $forcing->data_liberacao)
                                                <small class="text-success">
                                                    <i class="fas fa-check-circle"></i> 
                                                    {{ $forcing->data_liberacao->format('d/m/Y H:i') }}
                                                </small>
                                                <br><small class="text-muted">Liberado</small>
                                            @elseif($forcing->status === 'retirado' && $forcing->data_retirada)
                                                <small class="text-dark">
                                                    <i class="fas fa-check-double"></i> 
                                                    {{ $forcing->data_retirada->format('d/m/Y H:i') }}
                                                </small>
                                                <br><small class="text-muted">Retirado</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($forcing->executante)
                                                <strong>{{ $forcing->executante->name }}</strong>
                                                <br><small class="text-muted">{{ $forcing->executante->username }}</small>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($forcing->local_execucao)
                                                <span class="badge bg-info">{{ $forcing->getLocalExecucaoTexto() }}</span>
                                            @else
                                                <span class="text-muted">-</span>
                                            @endif
                                        </td>
                                        <td>
                                            @if($forcing->status_execucao === 'executado')
                                                <span class="badge bg-success">
                                                    <i class="fas fa-check"></i> Executado
                                                </span>
                                            @else
                                                <span class="badge bg-warning">
                                                    <i class="fas fa-clock"></i> Pendente
                                                </span>
                                            @endif
                                        </td>
                                        <td>
                                            <div class="btn-group" role="group">
                                                <a href="{{ route('forcing.show', $forcing) }}" class="btn btn-sm btn-outline-info" title="Visualizar">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                @if(auth()->user()->perfil === 'admin' || $forcing->user_id === auth()->id())
                                                    <a href="{{ route('forcing.edit', $forcing) }}" class="btn btn-sm btn-outline-warning" title="Editar">
                                                        <i class="fas fa-edit"></i>
                                                    </a>
                                                @endif
                                                
                                                @if((auth()->user()->perfil === 'liberador' && $forcing->liberado_por == auth()->id()) || auth()->user()->perfil === 'admin')
                                                    @if($forcing->status === 'pendente')
                                                        <a href="{{ route('forcing.liberar-page', $forcing) }}" class="btn btn-sm btn-outline-success d-md-none" title="Liberar">
                                                            <i class="fas fa-check"></i>
                                                        </a>
                                                        <button type="button" class="btn btn-sm btn-outline-success d-none d-md-inline-block" 
                                                                data-bs-toggle="modal" data-bs-target="#liberarModal{{ $forcing->id }}" title="Liberar">
                                                            <i class="fas fa-check"></i>
                                                        </button>
                                                    @endif
                                                @endif

                                                @if(auth()->user()->perfil === 'executante' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                                    <a href="{{ route('forcing.executar-page', $forcing) }}" class="btn btn-sm btn-outline-primary d-md-none" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#execucaoModal{{ $forcing->id }}" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </button>
                                                @elseif(auth()->user()->perfil === 'admin' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                                    <a href="{{ route('forcing.executar-page', $forcing) }}" class="btn btn-sm btn-outline-primary d-md-none" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#execucaoModal{{ $forcing->id }}" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </button>
                                                @endif

                                                @if($forcing->status === 'forcado')
                                                    <a href="{{ route('forcing.solicitar-retirada-page', $forcing) }}" class="btn btn-sm btn-outline-info d-md-none" title="Solicitar Retirada">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-info d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#solicitarRetiradaModal{{ $forcing->id }}" title="Solicitar Retirada">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif

                                                @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status === 'solicitacao_retirada')
                                                    <a href="{{ route('forcing.retirar-page', $forcing) }}" class="btn btn-sm btn-outline-dark d-md-none" title="Retirar Forcing">
                                                        <i class="fas fa-check-double"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-dark d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#retirarModal{{ $forcing->id }}" title="Retirar Forcing">
                                                        <i class="fas fa-check-double"></i>
                                                    </button>
                                                @endif
                                                
                                                @if(auth()->user()->perfil === 'admin')
                                                    <form action="{{ route('forcing.destroy', $forcing) }}" method="POST" class="d-inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="btn btn-sm btn-outline-danger" 
                                                                onclick="return confirm('Tem certeza que deseja excluir?')" title="Excluir">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </form>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>

                                    <!-- Modal para liberar forcing -->
                                    @if((auth()->user()->perfil === 'liberador' || auth()->user()->perfil === 'admin') && $forcing->status === 'pendente')
                                        <div class="modal fade" id="liberarModal{{ $forcing->id }}" tabindex="-1">
                                            <div class="modal-dialog">
                                                <div class="modal-content">
                                                    <form action="{{ route('forcing.liberar', $forcing) }}" method="POST">
                                                        @csrf
                                                        <div class="modal-header">
                                                            <h5 class="modal-title">Liberar Forcing</h5>
                                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body">
                                                            <p><strong>Forcing:</strong> {{ $forcing->titulo }}</p>
                                                            <div class="mb-3">
                                                                <label for="observacoes{{ $forcing->id }}" class="form-label">Observações</label>
                                                                <textarea class="form-control" id="observacoes{{ $forcing->id }}" 
                                                                          name="observacoes" rows="3" placeholder="Observações sobre a liberação (opcional)"></textarea>
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer">
                                                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                                            <button type="submit" class="btn btn-success">
                                                                <i class="fas fa-check"></i> Liberar Forcing
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Modal para registrar execução -->
                                    @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status_execucao === 'pendente')
                                        <div class="modal fade" id="execucaoModal{{ $forcing->id }}" tabindex="-1">
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
                                                                <label for="local_execucao{{ $forcing->id }}" class="form-label">Local de Execução <span class="text-danger">*</span></label>
                                                                <select class="form-select" id="local_execucao{{ $forcing->id }}" name="local_execucao" required>
                                                                    <option value="">Selecione o local...</option>
                                                                    <option value="supervisorio">Supervisório</option>
                                                                    <option value="plc">PLC</option>
                                                                    <option value="local">Local</option>
                                                                </select>
                                                            </div>

                                                            <div class="mb-3">
                                                                <label for="observacoes_execucao{{ $forcing->id }}" class="form-label">Observações da Execução</label>
                                                                <textarea class="form-control" id="observacoes_execucao{{ $forcing->id }}" 
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
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
                
                <!-- Visualização em Cards -->
                <div id="cards-container" class="view-container" style="display: none;">
                    <div class="p-3">
                        <div class="row g-3">
                            @foreach($forcings as $forcing)
                                <div class="col-12 col-md-6 col-lg-4 col-xl-3">
                                    <div class="card h-100 forcing-card d-flex flex-column @if(request('highlight') == $forcing->id) border-warning @endif
                                        @if($forcing->status == 'pendente') border-start border-2 border-secondary bg-light-secondary
                                        @elseif($forcing->status == 'liberado') border-start border-2 border-success bg-light-success
                                        @elseif($forcing->status == 'forcado') border-start border-2 border-warning bg-light-warning
                                        @elseif($forcing->status == 'solicitacao_retirada') border-start border-2 border-info bg-light-info
                                        @elseif($forcing->status == 'retirado') border-start border-2 border-dark bg-light-dark
                                        @endif" 
                                         data-forcing-id="{{ $forcing->id }}" 
                                         data-area="{{ $forcing->area }}"
                                         data-tag="{{ $forcing->tag }}"
                                         data-created="{{ $forcing->created_at->timestamp }}">
                                        <!-- Header do Card -->
                                        <div class="card-header d-flex justify-content-between align-items-center p-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-secondary me-2">#{{ $forcing->id }}</span>
                                                <code class="text-primary small">{{ $forcing->tag }}</code>
                                            </div>
                                            <div class="dropdown">
                                                <button class="btn btn-sm btn-outline-secondary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown" 
                                                        aria-expanded="false"
                                                        onclick="event.stopPropagation();">
                                                    <i class="fas fa-ellipsis-v"></i>
                                                </button>
                                                <ul class="dropdown-menu dropdown-menu-end">
                                                    <li>
                                                        <a class="dropdown-item" href="{{ route('forcing.show', $forcing->id) }}" onclick="event.stopPropagation();">
                                                            <i class="fas fa-eye me-2"></i>Ver Detalhes
                                                        </a>
                                                    </li>
                                                    @if(auth()->user()->perfil === 'admin' || (auth()->user()->perfil === 'liberador' && $forcing->status === 'pendente'))
                                                        <li>
                                                            <a class="dropdown-item" href="{{ route('forcing.edit', $forcing->id) }}" onclick="event.stopPropagation();">
                                                                <i class="fas fa-edit me-2"></i>Editar
                                                            </a>
                                                        </li>
                                                    @endif
                                                    <li><hr class="dropdown-divider"></li>
                                                    <li>
                                                        <a class="dropdown-item text-muted" href="#" onclick="event.preventDefault(); event.stopPropagation();">
                                                            <i class="fas fa-info-circle me-2"></i>Mais opções em breve
                                                        </a>
                                                    </li>
                                                </ul>
                                            </div>
                                        </div>
                                        
                                        <!-- Body do Card -->
                                        <div class="card-body p-3">
                                            <!-- Status -->
                                            <div class="mb-2">
                                                @if($forcing->status === 'pendente')
                                                    <span class="badge bg-secondary">
                                                        <i class="fas fa-clock"></i> Pendente
                                                    </span>
                                                @elseif($forcing->status === 'liberado')
                                                    <span class="badge bg-success">
                                                        <i class="fas fa-check"></i> Liberado
                                                    </span>
                                                @elseif($forcing->status === 'forcado')
                                                    <span class="badge bg-warning text-dark">
                                                        <i class="fas fa-exclamation-triangle"></i> Forçado
                                                    </span>
                                                @elseif($forcing->status === 'solicitacao_retirada')
                                                    <span class="badge bg-info">
                                                        <i class="fas fa-paper-plane"></i> Sol. Retirada
                                                    </span>
                                                @else
                                                    <span class="badge bg-dark">
                                                        <i class="fas fa-check-double"></i> Retirado
                                                    </span>
                                                @endif
                                            </div>
                                            
                                            <!-- Descrição -->
                                            <h6 class="card-title text-truncate" title="{{ $forcing->descricao_equipamento }}">
                                                {{ Str::limit($forcing->descricao_equipamento, 30) }}
                                            </h6>
                                            
                                            <!-- Área -->
                                            <p class="card-text mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-map-marker-alt me-1"></i>
                                                    {{ $forcing->area }}
                                                </small>
                                            </p>
                                            
                                            <!-- Criado por -->
                                            <p class="card-text mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-user me-1"></i>
                                                    {{ $forcing->user->name }}
                                                </small>
                                            </p>
                                            
                                            <!-- Data -->
                                            <p class="card-text mb-2">
                                                <small class="text-muted">
                                                    <i class="fas fa-calendar me-1"></i>
                                                    {{ $forcing->data_forcing->format('d/m/Y H:i') }}
                                                </small>
                                            </p>
                                            
                                            <!-- Empresa/Setor -->
                                            <p class="card-text">
                                                <small class="text-muted">
                                                    <i class="fas fa-building me-1"></i>
                                                    {{ $forcing->user->empresa }} - {{ $forcing->user->setor }}
                                                </small>
                                            </p>
                                        </div>
                                        
                                        <!-- Footer do Card com Ações -->
                                        <div class="card-footer p-2 mt-auto">
                                            <div class="d-flex justify-content-between align-items-center">
                                                <a href="{{ route('forcing.show', $forcing->id) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                                
                                                <!-- Ações específicas por status -->
                                                @if((auth()->user()->perfil === 'liberador' || auth()->user()->perfil === 'admin') && $forcing->status === 'pendente')
                                                    <a href="{{ route('forcing.liberar-page', $forcing) }}" class="btn btn-sm btn-outline-success d-md-none" title="Liberar">
                                                        <i class="fas fa-check"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-success d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#liberarModal{{ $forcing->id }}" title="Liberar">
                                                        <i class="fas fa-check"></i>
                                                    </button>
                                                @endif

                                                @if(auth()->user()->perfil === 'executante' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                                    <a href="{{ route('forcing.executar-page', $forcing) }}" class="btn btn-sm btn-outline-primary d-md-none" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#executarModal{{ $forcing->id }}" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </button>
                                                @elseif(auth()->user()->perfil === 'admin' && $forcing->status_execucao === 'pendente' && $forcing->status === 'liberado')
                                                    <a href="{{ route('forcing.executar-page', $forcing) }}" class="btn btn-sm btn-outline-primary d-md-none" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-primary d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#executarModal{{ $forcing->id }}" title="Registrar Execução">
                                                        <i class="fas fa-tools"></i>
                                                    </button>
                                                @endif

                                                @if($forcing->status === 'forcado')
                                                    <a href="{{ route('forcing.solicitar-retirada-page', $forcing) }}" class="btn btn-sm btn-outline-info d-md-none" title="Solicitar Retirada">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </a>
                                                    <button type="button" class="btn btn-sm btn-outline-info d-none d-md-inline-block" 
                                                            data-bs-toggle="modal" data-bs-target="#solicitarRetiradaModal{{ $forcing->id }}" title="Solicitar Retirada">
                                                        <i class="fas fa-paper-plane"></i>
                                                    </button>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <div id="pagination-container">
        @if($forcings->hasPages())
            <div class="d-flex justify-content-between align-items-center mt-3">
                <div class="text-muted">
                    Mostrando {{ $forcings->firstItem() }} a {{ $forcings->lastItem() }} de {{ $forcings->total() }} forcings
                </div>
                <div>
                    {{ $forcings->appends(request()->query())->links('pagination::bootstrap-4') }}
                </div>
            </div>
        @endif
    </div>

    <!-- Modais para ações dos forcings -->
    <div data-modals-container>
        @foreach($forcings as $forcing)
            <!-- Modal para solicitar retirada -->
            @if($forcing->status === 'forcado')
                <div class="modal fade" id="solicitarRetiradaModal{{ $forcing->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('forcing.solicitar-retirada', $forcing) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Solicitar Retirada</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Forcing:</strong> {{ $forcing->titulo ?? $forcing->tag }}</p>
                                    <p class="text-muted">Você está solicitando a retirada deste forcing. O executante será notificado.</p>
                                    
                                    <div class="mb-3">
                                        <label for="descricao_resolucao{{ $forcing->id }}" class="form-label">Descrição da Resolução <span class="text-danger">*</span></label>
                                        <textarea class="form-control" id="descricao_resolucao{{ $forcing->id }}" 
                                                  name="descricao_resolucao" rows="4" required 
                                                  placeholder="Descreva como foi resolvido o problema que ocasionou o forcing..."></textarea>
                                    </div>
                                    
                                    <div class="mb-3">
                                        <label for="observacoes{{ $forcing->id }}" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes{{ $forcing->id }}" 
                                                  name="observacoes" rows="3" 
                                                  placeholder="Observações adicionais (opcional)"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-info">
                                        <i class="fas fa-paper-plane"></i> Solicitar Retirada
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Modal para retirar forcing definitivamente -->
            @if((auth()->user()->perfil === 'executante' || auth()->user()->perfil === 'admin') && $forcing->status === 'solicitacao_retirada')
                <div class="modal fade" id="retirarModal{{ $forcing->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <form action="{{ route('forcing.retirar', $forcing) }}" method="POST">
                                @csrf
                                <div class="modal-header">
                                    <h5 class="modal-title">Retirar Forcing</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    <p><strong>Forcing:</strong> {{ $forcing->titulo }}</p>
                                    <p class="text-muted">Esta ação finalizará o ciclo do forcing, marcando-o como retirado definitivamente.</p>
                                    <div class="mb-3">
                                        <label for="observacoes_retirada{{ $forcing->id }}" class="form-label">Observações</label>
                                        <textarea class="form-control" id="observacoes_retirada{{ $forcing->id }}" 
                                                  name="observacoes" rows="3" placeholder="Observações sobre a retirada (opcional)"></textarea>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                                    <button type="submit" class="btn btn-dark">
                                        <i class="fas fa-check-double"></i> Retirar Forcing
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            @endif
        @endforeach
    </div>

    <!-- Resumo dos status - ESTATÍSTICAS TOTAIS -->
    <div class="row mt-4">
        <!-- Cards de Status (6 cards) -->
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-secondary text-white h-100" data-status="pendente" title="Clique para filtrar">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['pendente'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-clock"></i> Pendente</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-success text-white h-100" data-status="liberado" title="Clique para filtrar">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['liberado'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-check"></i> Liberado</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-warning text-white h-100" data-status="forcado" title="Clique para filtrar">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['forcado'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-exclamation-triangle"></i> Forçado</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-info text-white h-100" data-status="solicitacao_retirada" title="Clique para filtrar">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['solicitacao_retirada'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-paper-plane"></i> Sol. Retirada</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-dark text-white h-100" data-status="retirado" title="Clique para filtrar">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['retirado'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-check-double"></i> Retirado</p>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-4 col-lg-2 mb-3">
            <div class="card bg-primary text-white h-100" title="Forcing executados">
                <div class="card-body text-center d-flex flex-column justify-content-center">
                    <h3 class="mb-1">{{ $totalStats['executado'] ?? 0 }}</h3>
                    <p class="mb-0"><i class="fas fa-tools"></i> Executados</p>
                </div>
            </div>
        </div>
        
        <!-- Card Total - Ocupa linha inteira em telas grandes -->
        <div class="col-12 mt-3">
            <div class="card text-white" style="background: linear-gradient(135deg, #2c3e50 0%, #34495e 100%); box-shadow: 0 4px 8px rgba(0,0,0,0.2);" title="Total de Forcings no Sistema">
                <div class="card-body text-center py-4">
                    <div class="row align-items-center">
                        <div class="col-md-6">
                            <h2 class="mb-2 text-white fw-bold" style="text-shadow: 2px 2px 4px rgba(0,0,0,0.5); font-size: 3rem;">{{ $totalStats['total'] ?? 0 }}</h2>
                        </div>
                        <div class="col-md-6">
                            <p class="mb-1 text-white h4" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);"><i class="fas fa-list-alt me-2"></i>Total de Forcings</p>
                            <small class="text-white" style="text-shadow: 1px 1px 2px rgba(0,0,0,0.5);">Sistema completo - Todos os status</small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-body text-center">
                    <i class="fas fa-exclamation-triangle fa-3x text-muted mb-3"></i>
                    <h4>Nenhum forcing encontrado</h4>
                    <p class="text-muted">Seja o primeiro a criar um forcing no sistema!</p>
                    <a href="{{ route('forcing.terms') }}" class="btn btn-primary">
                        <i class="fas fa-plus"></i> Criar Primeiro Forcing
                    </a>
                </div>
            </div>
        </div>
    </div>
@endif

</div> <!-- Fechamento do container-fluid -->

<script>
// Função para aplicar filtros rápidos
function aplicarFiltrosRapidos(status) {
    const form = document.getElementById('filtroForm');
    const statusSelect = form.querySelector('select[name="status"]');
    statusSelect.value = status;
    form.submit();
}

// Função para aplicar filtro por busca em tempo real (opcional)
function aplicarBuscaRapida() {
    const busca = document.querySelector('input[name="busca"]');
    if (busca && (busca.value.length >= 2 || busca.value.length === 0)) {
        document.getElementById('filtroForm').submit();
    }
}
</script>

<style>
/* ===== ESTILOS RESPONSIVOS PARA TABELA FULL SCREEN ===== */

/* Container principal responsivo */
.table-responsive-container {
    min-height: 400px;
    display: flex;
    flex-direction: column;
}

.table-responsive-container .card {
    display: flex;
    flex-direction: column;
}

.table-responsive-container .card-body {
    flex: 1;
    padding: 0;
}

/* Tabela responsiva */
.table-responsive {
    overflow: auto;
}

.table-responsive table {
    margin-bottom: 0;
    min-width: 1200px; /* Largura mínima para manter legibilidade */
}

/* Header fixo */
.table-responsive .sticky-top {
    position: sticky;
    top: 0;
    z-index: 10;
}

/* Células da tabela */
.table td, .table th {
    vertical-align: middle;
    padding: 0.75rem 0.5rem;
    white-space: nowrap;
    overflow: hidden;
    text-overflow: ellipsis;
}

/* Colunas específicas com largura fixa */
.table th:nth-child(1), .table td:nth-child(1) { width: 50px; } /* # */
.table th:nth-child(2), .table td:nth-child(2) { min-width: 200px; max-width: 250px; } /* TAG/Descrição */
.table th:nth-child(13), .table td:nth-child(13) { width: 100px; } /* Ações */

/* Badges responsivos */
.badge {
    font-size: 0.7em;
    padding: 0.4em 0.6em;
}

/* Botões de ação compactos */
.btn-group .btn {
    padding: 0.25rem 0.4rem;
    font-size: 0.8rem;
}

/* ===== RESPONSIVIDADE MÓVEL ===== */
@media (max-width: 767.98px) {
    .table-responsive-container {
        min-height: 300px;
    }
    
    .table-responsive table {
        min-width: 800px;
        font-size: 0.85rem;
    }
    
    .table td, .table th {
        padding: 0.5rem 0.25rem;
    }
    
    /* Ocultar colunas menos importantes em mobile */
    .table th:nth-child(6), .table td:nth-child(6), /* Empresa/Setor */
    .table th:nth-child(8), .table td:nth-child(8), /* Liberador */
    .table th:nth-child(10), .table td:nth-child(10), /* Executante */
    .table th:nth-child(12), .table td:nth-child(12) { /* Status Execução */
        display: none;
    }
    
    /* Ajustar larguras em mobile */
    .table th:nth-child(2), .table td:nth-child(2) { 
        min-width: 150px; 
        max-width: 180px; 
    }
    
    .table th:nth-child(4), .table td:nth-child(4) { 
        min-width: 100px; 
    }
    
    .table th:nth-child(5), .table td:nth-child(5) { 
        min-width: 120px; 
    }
}

/* ===== RESPONSIVIDADE TABLET ===== */
@media (min-width: 768px) and (max-width: 1024px) {
    .table-responsive-container {
        min-height: 400px;
    }
    
    .table-responsive table {
        min-width: 1000px;
    }
    
    /* Ocultar algumas colunas em tablet */
    .table th:nth-child(6), .table td:nth-child(6) { /* Empresa/Setor */
        display: none;
    }
}

/* ===== MODO FULLSCREEN ===== */
.table-responsive-container.fullscreen {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    height: 100vh;
    z-index: 1050;
    background: white;
    padding: 1rem;
    overflow: auto; /* Permitir scroll no container fullscreen */
    display: flex;
    flex-direction: column;
}

.table-responsive-container.fullscreen .card {
    border: none;
    box-shadow: none;
    flex: 1;
    min-height: 0; /* Permitir que o card se ajuste ao conteúdo */
}

.table-responsive-container.fullscreen .card-body {
    overflow: auto; /* Permitir scroll no conteúdo do card */
    flex: 1;
}

.table-responsive-container.fullscreen .table-responsive {
    height: auto; /* Altura automática para permitir scroll */
    max-height: none; /* Remover limitação de altura */
}

/* Estilos quando body está em fullscreen */
body.fullscreen-active {
    overflow: hidden; /* Bloquear scroll do body principal */
}

body.fullscreen-active .table-responsive-container.fullscreen {
    overflow: auto; /* Permitir scroll apenas no container fullscreen */
}

/* Scrollbar customizada para fullscreen */
.table-responsive-container.fullscreen::-webkit-scrollbar {
    width: 12px;
    height: 12px;
}

.table-responsive-container.fullscreen::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 6px;
}

.table-responsive-container.fullscreen::-webkit-scrollbar-thumb {
    background: #888;
    border-radius: 6px;
}

.table-responsive-container.fullscreen::-webkit-scrollbar-thumb:hover {
    background: #555;
}

/* ===== ESTILOS GERAIS ===== */
/* Header responsivo */
@media (max-width: 767.98px) {
    .card-body {
        padding: 1rem 0.75rem;
    }
    
    .btn-sm {
        font-size: 0.775rem;
        padding: 0.25rem 0.5rem;
    }
    
    .form-control, .form-select {
        font-size: 16px !important;
        height: auto;
        min-height: 38px;
    }
    
    .form-label.small {
        font-size: 0.8rem;
        margin-bottom: 0.25rem;
    }
    
    .btn-outline-info, .btn-outline-success, .btn-outline-warning {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
}

/* Estilos para tablets */
@media (min-width: 768px) and (max-width: 991.98px) {
    .btn-sm span.d-none {
        display: inline !important;
    }
}

/* Animação suave para colapsos */
.collapsing {
    transition: height 0.2s ease;
}

/* Cards de forcing não são mais clicáveis - hover removido */

/* Cores de fundo claras para cards por status */
.bg-light-secondary {
    background-color: rgba(108, 117, 125, 0.1) !important;
}

.bg-light-success {
    background-color: rgba(25, 135, 84, 0.1) !important;
}

.bg-light-warning {
    background-color: rgba(255, 193, 7, 0.1) !important;
}

.bg-light-info {
    background-color: rgba(13, 202, 240, 0.1) !important;
}

.bg-light-dark {
    background-color: rgba(33, 37, 41, 0.1) !important;
}

/* Garantir altura uniforme dos cards */
.forcing-card {
    min-height: 280px;
    display: flex !important;
    flex-direction: column;
}

.forcing-card .card-body {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
}

.forcing-card .card-body p:last-child {
    margin-bottom: 0;
}

/* Melhor indicador visual para filtros ativos */
.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

/* Badge customizado para contadores */
.badge.bg-primary, .badge.bg-secondary {
    font-size: 0.75em;
    padding: 0.35em 0.65em;
}

/* ===== CORREÇÕES PARA iOS/MOBILE ===== */
/* Correção para modais no iOS */
.modal {
    z-index: 1055 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    overflow: hidden !important;
}

.modal-backdrop {
    z-index: 1050 !important;
    position: fixed !important;
    top: 0 !important;
    left: 0 !important;
    width: 100% !important;
    height: 100% !important;
    background-color: rgba(0, 0, 0, 0.5) !important;
}

.modal-dialog {
    z-index: 1056 !important;
    position: relative !important;
    margin: 1.75rem auto !important;
    max-width: 500px !important;
    width: 90% !important;
}

.modal-content {
    z-index: 1057 !important;
    position: relative !important;
    background-color: #fff !important;
    border: 1px solid rgba(0, 0, 0, 0.2) !important;
    border-radius: 0.3rem !important;
    box-shadow: 0 0.5rem 1rem rgba(0, 0, 0, 0.15) !important;
}

/* Correção específica para Safari iOS */
@supports (-webkit-touch-callout: none) {
    .modal {
        -webkit-overflow-scrolling: touch !important;
        overflow: auto !important;
    }
    
    .modal-dialog {
        -webkit-transform: translate3d(0, 0, 0) !important;
        transform: translate3d(0, 0, 0) !important;
    }
}

/* Correção para viewport no iOS */
@media screen and (max-width: 768px) {
    .modal-dialog {
        margin: 0.5rem !important;
        max-width: calc(100% - 1rem) !important;
        width: calc(100% - 1rem) !important;
    }
    
    .modal-content {
        border-radius: 0.5rem !important;
    }
    
    .modal-header {
        padding: 1rem !important;
        border-bottom: 1px solid #dee2e6 !important;
    }
    
    .modal-body {
        padding: 1rem !important;
    }
    
    .modal-footer {
        padding: 1rem !important;
        border-top: 1px solid #dee2e6 !important;
    }
}

/* Estilos específicos para dispositivos iOS */
.ios-device .modal {
    -webkit-overflow-scrolling: touch !important;
    overflow: auto !important;
}

.ios-device .modal-dialog {
    -webkit-transform: translate3d(0, 0, 0) !important;
    transform: translate3d(0, 0, 0) !important;
    -webkit-backface-visibility: hidden !important;
    backface-visibility: hidden !important;
}

.ios-device .modal-content {
    -webkit-transform: translate3d(0, 0, 0) !important;
    transform: translate3d(0, 0, 0) !important;
}

/* Correção para botões no iOS */
.ios-device .btn {
    -webkit-tap-highlight-color: transparent !important;
    -webkit-touch-callout: none !important;
    -webkit-user-select: none !important;
    -moz-user-select: none !important;
    -ms-user-select: none !important;
    user-select: none !important;
}

/* Correção para inputs no iOS */
.ios-device .form-control,
.ios-device .form-select {
    -webkit-appearance: none !important;
    -moz-appearance: none !important;
    appearance: none !important;
    border-radius: 0.375rem !important;
}

/* ===== MELHORIAS PARA EDIÇÃO DE CAMPOS ===== */
/* Campos de formulário nos modais */
.modal .form-control,
.modal .form-select {
    font-size: 16px !important; /* Previne zoom no iOS */
    padding: 0.75rem 1rem !important;
    border: 2px solid #dee2e6 !important;
    border-radius: 0.5rem !important;
    transition: all 0.3s ease !important;
}

.modal .form-control:focus,
.modal .form-select:focus {
    border-color: #0d6efd !important;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    outline: none !important;
}

.modal .form-label {
    font-weight: 600 !important;
    color: #495057 !important;
    margin-bottom: 0.5rem !important;
}

.modal .form-text {
    font-size: 0.875rem !important;
    color: #6c757d !important;
    margin-top: 0.25rem !important;
}

/* Textarea específico */
.modal textarea.form-control {
    resize: vertical !important;
    min-height: 100px !important;
    line-height: 1.5 !important;
}

/* Select específico */
.modal .form-select {
    background-image: url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 16 16'%3e%3cpath fill='none' stroke='%23343a40' stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='m1 6 7 7 7-7'/%3e%3c/svg%3e") !important;
    background-repeat: no-repeat !important;
    background-position: right 0.75rem center !important;
    background-size: 16px 12px !important;
    padding-right: 2.25rem !important;
}

/* Botões nos modais */
.modal .btn {
    padding: 0.75rem 1.5rem !important;
    font-weight: 600 !important;
    border-radius: 0.5rem !important;
    transition: all 0.3s ease !important;
}

.modal .btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 4px 8px rgba(0, 0, 0, 0.15) !important;
}

/* Responsividade para mobile */
@media (max-width: 768px) {
    .modal .form-control,
    .modal .form-select {
        font-size: 16px !important; /* Previne zoom no iOS */
        padding: 1rem !important;
        min-height: 48px !important; /* Tamanho mínimo para touch */
    }
    
    .modal textarea.form-control {
        min-height: 120px !important;
    }
    
    .modal .btn {
        padding: 1rem 1.5rem !important;
        font-size: 1rem !important;
        min-height: 48px !important;
    }
    
    .modal .form-label {
        font-size: 1rem !important;
        margin-bottom: 0.75rem !important;
    }
}

/* Scrollbar customizada */
.table-responsive::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}

.table-responsive::-webkit-scrollbar-track {
    background: #f1f1f1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb {
    background: #c1c1c1;
    border-radius: 4px;
}

.table-responsive::-webkit-scrollbar-thumb:hover {
    background: #a8a8a8;
}
</style>

@endsection

@section('scripts')
<script>
// Sistema de persistência de filtros
const FILTERS_STORAGE_KEY = 'forcing_filters';

// Salvar filtros no localStorage
function salvarFiltros() {
    try {
        const form = document.getElementById('filtroForm');
        const formData = new FormData(form);
        const filters = {};
        
        for (let [key, value] of formData.entries()) {
            if (value && value !== 'todos' && value !== 'todas') {
                filters[key] = value;
            }
        }
        
        localStorage.setItem(FILTERS_STORAGE_KEY, JSON.stringify(filters));
        console.log('Filtros salvos:', filters);
    } catch (error) {
        console.error('Erro ao salvar filtros:', error);
    }
}

// Carregar filtros do localStorage
function carregarFiltros() {
    try {
        const savedFilters = localStorage.getItem(FILTERS_STORAGE_KEY);
        if (savedFilters) {
            const filters = JSON.parse(savedFilters);
            const form = document.getElementById('filtroForm');
            
            Object.keys(filters).forEach(key => {
                const element = form.querySelector(`[name="${key}"]`);
                if (element) {
                    element.value = filters[key];
                }
            });
            
            console.log('Filtros carregados:', filters);
            return Object.keys(filters).length > 0;
        }
    } catch (error) {
        console.error('Erro ao carregar filtros:', error);
    }
    return false;
}

// Atualizar indicador visual de filtros ativos
function atualizarIndicadorFiltros() {
    try {
        const badge = document.getElementById('filtrosAtivosBadge');
        const form = document.getElementById('filtroForm');
        
        if (!badge || !form) return;
        
        const formData = new FormData(form);
        let filtrosAtivos = 0;
        
        for (let [key, value] of formData.entries()) {
            if (value && value !== 'todos' && value !== 'todas') {
                filtrosAtivos++;
            }
        }
        
        if (filtrosAtivos > 0) {
            badge.classList.remove('d-none');
            badge.innerHTML = `<i class="fas fa-check"></i> ${filtrosAtivos} Ativo${filtrosAtivos > 1 ? 's' : ''}`;
        } else {
            badge.classList.add('d-none');
        }
    } catch (error) {
        console.error('Erro ao atualizar indicador de filtros:', error);
    }
}

// Limpar todos os filtros
function limparTodosFiltros() {
    try {
        // Limpar localStorage
        localStorage.removeItem(FILTERS_STORAGE_KEY);
        
        // Resetar formulário
        const form = document.getElementById('filtroForm');
        form.reset();
        
        // Definir valores padrão
        const statusSelect = form.querySelector('select[name="status"]');
        const areaSelect = form.querySelector('select[name="area"]');
        const situacaoSelect = form.querySelector('select[name="situacao"]');
        const criadorSelect = form.querySelector('select[name="criador"]');
        
        if (statusSelect) statusSelect.value = 'todos';
        if (areaSelect) areaSelect.value = 'todas';
        if (situacaoSelect) situacaoSelect.value = 'todas';
        if (criadorSelect) criadorSelect.value = 'todos';
        
        // Atualizar indicador
        atualizarIndicadorFiltros();
        
        console.log('Filtros limpos');
        
        // Redirecionar para página sem filtros
        window.location.href = form.action;
    } catch (error) {
        console.error('Erro ao limpar filtros:', error);
    }
}

// Mostrar filtros ativos em um modal
function mostrarFiltrosAtivos() {
    try {
        const form = document.getElementById('filtroForm');
        const formData = new FormData(form);
        const filtrosAtivos = [];
        
        // Mapear nomes dos campos para labels amigáveis
        const labels = {
            'busca': 'Busca',
            'status': 'Status',
            'area': 'Área',
            'situacao': 'Situação',
            'criador': 'Criador',
            'data_inicio': 'Data Início',
            'data_fim': 'Data Fim'
        };
        
        for (let [key, value] of formData.entries()) {
            if (value && value !== 'todos' && value !== 'todas') {
                const label = labels[key] || key;
                filtrosAtivos.push(`${label}: <strong>${value}</strong>`);
            }
        }
        
        if (filtrosAtivos.length > 0) {
            const mensagem = `
                <div class="alert alert-info">
                    <h6><i class="fas fa-filter"></i> Filtros Ativos:</h6>
                    <ul class="mb-0">
                        ${filtrosAtivos.map(filtro => `<li>${filtro}</li>`).join('')}
                    </ul>
                </div>
                <p class="text-muted">Os filtros são mantidos automaticamente até que você clique em "Limpar".</p>
            `;
            
            // Usar alert do Bootstrap se disponível, senão alert nativo
            if (typeof bootstrap !== 'undefined') {
                // Criar modal dinâmico
                const modalHTML = `
                    <div class="modal fade" id="filtrosModal" tabindex="-1">
                        <div class="modal-dialog">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title">Filtros Ativos</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                </div>
                                <div class="modal-body">
                                    ${mensagem}
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                                    <button type="button" class="btn btn-warning" onclick="limparTodosFiltros()" data-bs-dismiss="modal">
                                        <i class="fas fa-times"></i> Limpar Filtros
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Remover modal existente se houver
                const existingModal = document.getElementById('filtrosModal');
                if (existingModal) {
                    existingModal.remove();
                }
                
                // Adicionar novo modal
                document.body.insertAdjacentHTML('beforeend', modalHTML);
                
                // Mostrar modal
                const modal = new bootstrap.Modal(document.getElementById('filtrosModal'));
                modal.show();
            } else {
                alert(`Filtros Ativos:\n\n${filtrosAtivos.join('\n')}\n\nOs filtros são mantidos automaticamente até que você clique em "Limpar".`);
            }
        } else {
            alert('Nenhum filtro está ativo no momento.');
        }
    } catch (error) {
        console.error('Erro ao mostrar filtros ativos:', error);
    }
}

// Função simples para filtros rápidos
function aplicarFiltrosRapidos(status) {
    try {
        const form = document.getElementById('filtroForm');
        const statusSelect = form.querySelector('select[name="status"]');
        if (statusSelect) {
            statusSelect.value = status;
            salvarFiltros(); // Salvar antes de submeter
            form.submit();
        }
    } catch (error) {
        console.error('Erro ao aplicar filtro:', error);
    }
}

// Função para alternar modo fullscreen
function toggleFullscreen() {
    const container = document.querySelector('.table-responsive-container');
    const btn = document.querySelector('[onclick="toggleFullscreen()"]');
    const icon = btn.querySelector('i');
    
    if (container.classList.contains('fullscreen')) {
        // Sair do fullscreen
        container.classList.remove('fullscreen');
        icon.className = 'fas fa-expand';
        btn.title = 'Tela cheia';
        document.body.style.overflow = '';
        document.body.classList.remove('fullscreen-active');
    } else {
        // Entrar no fullscreen
        container.classList.add('fullscreen');
        icon.className = 'fas fa-compress';
        btn.title = 'Sair da tela cheia';
        document.body.classList.add('fullscreen-active');
        // Não bloquear scroll do body - permitir scroll dentro do container
        document.body.style.overflow = '';
    }
}

// Função para ajustar altura da tabela (removida - tabela agora se expande naturalmente)

// Correções específicas para iOS
function corrigirModaisIOS() {
    // Detectar iOS
    const isIOS = /iPad|iPhone|iPod/.test(navigator.userAgent) && !window.MSStream;
    
    if (isIOS) {
        // Corrigir z-index dos modais
        const modals = document.querySelectorAll('.modal');
        modals.forEach(modal => {
            modal.style.zIndex = '1055';
            modal.style.position = 'fixed';
            modal.style.top = '0';
            modal.style.left = '0';
            modal.style.width = '100%';
            modal.style.height = '100%';
        });
        
        // Corrigir backdrop
        const backdrops = document.querySelectorAll('.modal-backdrop');
        backdrops.forEach(backdrop => {
            backdrop.style.zIndex = '1050';
            backdrop.style.position = 'fixed';
            backdrop.style.top = '0';
            backdrop.style.left = '0';
            backdrop.style.width = '100%';
            backdrop.style.height = '100%';
        });
        
        // Prevenir scroll do body quando modal estiver aberto
        document.addEventListener('show.bs.modal', function(e) {
            document.body.style.overflow = 'hidden';
            document.body.style.position = 'fixed';
            document.body.style.width = '100%';
        });
        
        document.addEventListener('hidden.bs.modal', function(e) {
            document.body.style.overflow = '';
            document.body.style.position = '';
            document.body.style.width = '';
        });
    }
}

// Inicialização básica
document.addEventListener('DOMContentLoaded', function() {
    console.log('Sistema de Forcing carregado');
    
    // Aplicar correções para iOS
    corrigirModaisIOS();
    
    // Correção adicional para viewport no iOS
    if (/iPad|iPhone|iPod/.test(navigator.userAgent)) {
        // Forçar recálculo de viewport
        const viewport = document.querySelector('meta[name="viewport"]');
        if (viewport) {
            viewport.setAttribute('content', 'width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no');
        }
        
        // Adicionar classe específica para iOS
        document.body.classList.add('ios-device');
    }
    
    // Carregar filtros salvos
    const temFiltrosSalvos = carregarFiltros();
    
    // Atualizar indicador visual de filtros
    atualizarIndicadorFiltros();
    
    // Cards de forcing não são mais clicáveis - funcionalidade removida
    document.querySelectorAll('.forcing-card').forEach(card => {
        card.style.cursor = 'default';
        // Removido event listener de clique para evitar refresh da página
    });
    
    // Auto-expandir filtros se necessário
    const urlParams = new URLSearchParams(window.location.search);
    const temFiltrosURL = urlParams.has('status') || urlParams.has('busca') || urlParams.has('area');
    
    if ((temFiltrosURL || temFiltrosSalvos) && typeof bootstrap !== 'undefined') {
        const filtrosCollapse = document.getElementById('filtrosCollapse');
        if (filtrosCollapse) {
            new bootstrap.Collapse(filtrosCollapse, { show: true });
        }
    }
    
    // Adicionar eventos para salvar filtros automaticamente
    const form = document.getElementById('filtroForm');
    if (form) {
        // Salvar filtros quando o formulário for submetido
        form.addEventListener('submit', function() {
            salvarFiltros();
        });
        
        // Salvar filtros quando campos mudarem (com debounce)
        let saveTimeout;
        const inputs = form.querySelectorAll('input, select');
        inputs.forEach(input => {
            input.addEventListener('change', function() {
                clearTimeout(saveTimeout);
                saveTimeout = setTimeout(() => {
                    salvarFiltros();
                    atualizarIndicadorFiltros();
                }, 500);
            });
        });
    }
    
    // Tabela agora se expande naturalmente sem limitação de altura
    
    // Sair do fullscreen com ESC
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const container = document.querySelector('.table-responsive-container');
            if (container && container.classList.contains('fullscreen')) {
                toggleFullscreen();
            }
        }
    });
});
</script>

<style>
@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.7; }
    100% { opacity: 1; }
}

.table-warning.highlight {
    background-color: #fff3cd !important;
    border-color: #ffc107 !important;
}
</style>
@endsection
