@extends('layouts.app')

@section('title', 'Lista de Forcing')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h1><i class="fas fa-exclamation-triangle"></i> Controle de Forcing</h1>
    <div class="d-flex gap-2">
        <button id="refreshTableBtn" class="btn btn-outline-primary btn-sm" title="Atualizar Lista">
            <i class="fas fa-sync-alt" id="refreshIcon"></i> 
            <span class="d-none d-md-inline">Atualizar</span>
        </button>
        <a href="{{ route('forcing.terms') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Novo Forcing
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
            <h6 class="mb-0"><i class="fas fa-filter"></i> Filtros</h6>
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
                            <a href="{{ route('forcing.index') }}" class="btn btn-outline-secondary btn-sm">
                                <i class="fas fa-times"></i> <span class="d-none d-sm-inline">Limpar</span>
                            </a>
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
    <div class="row">
        <div class="col-12">
            <div class="card shadow">
                <div class="card-body p-0">
                    <div id="table-container">
                        @include('forcing.partials.table', compact('forcings'))
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Paginação -->
    <div id="pagination-container">
        @include('forcing.partials.pagination', compact('forcings'))
    </div>

    <!-- Modais para ações dos forcings -->
    <div data-modals-container>
        @include('forcing.partials.modals', compact('forcings'))
    </div>

    <!-- Resumo dos status -->
    <div class="row mt-4">
        <div class="col-md-2">
            <div class="card text-center border-secondary">
                <div class="card-body">
                    <h3 class="text-secondary mb-1">{{ \App\Models\Forcing::where('status', 'pendente')->count() }}</h3>
                    <p class="card-text small mb-0">⏳ Pendentes</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-success">
                <div class="card-body">
                    <h3 class="text-success mb-1">{{ \App\Models\Forcing::where('status', 'liberado')->count() }}</h3>
                    <p class="card-text small mb-0">✅ Liberados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-warning">
                <div class="card-body">
                    <h3 class="text-warning mb-1">{{ \App\Models\Forcing::where('status', 'forcado')->count() }}</h3>
                    <p class="card-text small mb-0">⚠️ Forçados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-info">
                <div class="card-body">
                    <h3 class="text-info mb-1">{{ \App\Models\Forcing::where('status', 'solicitacao_retirada')->count() }}</h3>
                    <p class="card-text small mb-0">✈️ Sol. Retirada</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-dark">
                <div class="card-body">
                    <h3 class="text-dark mb-1">{{ \App\Models\Forcing::where('status', 'retirado')->count() }}</h3>
                    <p class="card-text small mb-0">✅✅ Retirados</p>
                </div>
            </div>
        </div>
        <div class="col-md-2">
            <div class="card text-center border-primary">
                <div class="card-body">
                    <h3 class="text-primary mb-1">{{ \App\Models\Forcing::count() }}</h3>
                    <p class="card-text small mb-0">📊 Total</p>
                </div>
            </div>
        </div>
    </div>
@else
    <div class="alert alert-info text-center">
        <h4><i class="fas fa-search"></i> Nenhum forcing encontrado</h4>
        <p class="mb-0">Não há forcing que atendam aos critérios de busca.</p>
        <hr>
        <a href="{{ route('forcing.index') }}" class="btn btn-outline-primary">
            <i class="fas fa-refresh"></i> Ver Todos
        </a>
        <a href="{{ route('forcing.terms') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Criar Novo Forcing
        </a>
    </div>
@endif

@endsection

@section('scripts')
<script>
// Função para filtros rápidos
function aplicarFiltrosRapidos(status) {
    document.querySelector('select[name="status"]').value = status;
    document.getElementById('filtroForm').submit();
}

// AJAX Refresh da Tabela
function refreshTable() {
    const btn = document.getElementById('refreshTableBtn');
    const icon = document.getElementById('refreshIcon');
    const tableContainer = document.getElementById('table-container');
    const paginationContainer = document.getElementById('pagination-container');
    
    // Desabilitar botão e mostrar loading
    btn.disabled = true;
    icon.classList.add('fa-spin');
    
    // Adicionar efeito visual à tabela
    if (tableContainer) {
        tableContainer.style.opacity = '0.6';
    }
    
    // Preparar dados do formulário de filtros
    const formData = new FormData(document.getElementById('filtroForm'));
    const params = new URLSearchParams(formData);
    
    // Fazer requisição AJAX
    fetch(`{{ route('forcing.refresh-table') }}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Content-Type': 'application/x-www-form-urlencoded',
        },
        body: params
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualizar tabela
            if (tableContainer && data.html) {
                tableContainer.innerHTML = data.html;
            }
            
            // Atualizar paginação
            if (paginationContainer && data.pagination) {
                paginationContainer.innerHTML = data.pagination;
            }
            
            // Atualizar modais
            const modalsContainer = document.querySelector('[data-modals-container]');
            if (modalsContainer && data.modals) {
                modalsContainer.innerHTML = data.modals;
            }
            
            // Reativar modais do Bootstrap
            if (typeof bootstrap !== 'undefined') {
                document.querySelectorAll('[data-bs-toggle="modal"]').forEach(element => {
                    new bootstrap.Modal(document.querySelector(element.getAttribute('data-bs-target')));
                });
            }
            
            // Mostrar notificação de sucesso
            showUpdateNotification(data.total, data.timestamp);
        } else {
            throw new Error(data.message || 'Erro desconhecido');
        }
    })
    .catch(error => {
        console.error('Erro ao atualizar tabela:', error);
        showErrorNotification('Erro ao atualizar a lista. Tente novamente.');
    })
    .finally(() => {
        // Restaurar botão e tabela
        btn.disabled = false;
        icon.classList.remove('fa-spin');
        
        if (tableContainer) {
            tableContainer.style.opacity = '1';
        }
    });
}

// Adicionar evento ao botão de refresh
document.addEventListener('DOMContentLoaded', function() {
    const refreshBtn = document.getElementById('refreshTableBtn');
    if (refreshBtn) {
        refreshBtn.addEventListener('click', refreshTable);
    }
});

// Função para mostrar notificação de sucesso
function showUpdateNotification(total, timestamp) {
    // Remover notificação anterior se existir
    const existingToast = document.getElementById('updateToast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Criar nova notificação
    const toastHTML = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div id="updateToast" class="toast show" role="alert">
                <div class="toast-header bg-success text-white">
                    <i class="fas fa-check-circle me-2"></i>
                    <strong class="me-auto">Lista Atualizada</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    <strong>Lista atualizada com sucesso!</strong><br>
                    <small class="text-muted">
                        <i class="fas fa-list"></i> Total: ${total} registros<br>
                        <i class="fas fa-clock"></i> ${timestamp}
                    </small>
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    // Auto-remover após 3 segundos
    setTimeout(() => {
        const toast = document.getElementById('updateToast');
        if (toast) {
            toast.remove();
        }
    }, 3000);
}

// Função para mostrar notificação de erro
function showErrorNotification(message) {
    // Remover notificação anterior se existir
    const existingToast = document.getElementById('errorToast');
    if (existingToast) {
        existingToast.remove();
    }
    
    // Criar nova notificação de erro
    const toastHTML = `
        <div class="position-fixed top-0 end-0 p-3" style="z-index: 11">
            <div id="errorToast" class="toast show" role="alert">
                <div class="toast-header bg-danger text-white">
                    <i class="fas fa-exclamation-triangle me-2"></i>
                    <strong class="me-auto">Erro</strong>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="toast"></button>
                </div>
                <div class="toast-body">
                    ${message}
                </div>
            </div>
        </div>
    `;
    
    document.body.insertAdjacentHTML('beforeend', toastHTML);
    
    // Auto-remover após 5 segundos
    setTimeout(() => {
        const toast = document.getElementById('errorToast');
        if (toast) {
            toast.remove();
        }
    }, 5000);
}

// Opcional: Auto-refresh a cada 30 segundos (descomentea se necessário)
// setInterval(refreshTable, 30000);
</script>
@endsection
