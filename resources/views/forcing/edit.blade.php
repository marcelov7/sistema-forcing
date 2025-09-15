{{-- filepath: c:\xampp\htdocs\Forcing\resources\views\forcing\edit.blade.php --}}
@extends('layouts.app')

@section('title', 'Editar Forcing - Sistema de Controle de Forcing')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <!-- Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="mb-1">
                        <i class="fas fa-edit text-warning me-2"></i>
                        Editar Forcing
                    </h2>
                    <nav aria-label="breadcrumb">
                        <ol class="breadcrumb">
                            <li class="breadcrumb-item">
                                <a href="{{ route('forcing.index') }}">Lista de Forcings</a>
                            </li>
                            <li class="breadcrumb-item active">Editar Forcing #{{ $forcing->id }}</li>
                        </ol>
                    </nav>
                </div>
                <div>
                    <span class="badge bg-{{ $forcing->status_cor }} fs-6">
                        {{ $forcing->status_texto }}
                    </span>
                </div>
            </div>

            <!-- Card Principal -->
            <div class="card shadow-sm">
                <div class="card-header bg-primary text-white">
                    <h5 class="card-title mb-0">
                        <i class="fas fa-cog me-2"></i>
                        Dados do Forcing - TAG: {{ $forcing->tag }}
                    </h5>
                </div>

                <div class="card-body">
                    <!-- Alerts -->
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="fas fa-check-circle me-2"></i>
                            {{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i>
                            {{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <!-- Informações do Forcing -->
                    <div class="row mb-4">
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-info-circle text-primary me-2"></i>
                                        Informações do Forcing
                                    </h6>
                                    <p class="mb-1"><strong>Criado em:</strong> {{ $forcing->data_forcing_formatada ?? $forcing->created_at->format('d/m/Y H:i:s') }}</p>
                                    <p class="mb-1"><strong>Solicitante:</strong> {{ $forcing->user->name }}</p>
                                    <p class="mb-1"><strong>Unidade:</strong> {{ $forcing->unit->name ?? 'N/A' }}</p>
                                    <p class="mb-0"><strong>Status Execução:</strong> 
                                        <span class="badge bg-{{ $forcing->status_execucao === 'executado' ? 'success' : 'warning' }}">
                                            {{ ucfirst($forcing->status_execucao ?? 'pendente') }}
                                        </span>
                                    </p>
                                </div>
                            </div>
                        </div>
                        
                        @if($forcing->data_liberacao)
                        <div class="col-md-6">
                            <div class="card bg-light">
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <i class="fas fa-check-circle text-success me-2"></i>
                                        Informações de Liberação
                                    </h6>
                                    <p class="mb-1"><strong>Liberado em:</strong> {{ $forcing->data_liberacao_formatada ?? $forcing->data_liberacao->format('d/m/Y H:i:s') }}</p>
                                    <p class="mb-0"><strong>Liberado por:</strong> {{ $forcing->liberador->name ?? 'N/A' }}</p>
                                </div>
                            </div>
                        </div>
                        @endif
                    </div>

                    <!-- Formulário de Edição -->
                    <form method="POST" action="{{ route('forcing.update', $forcing) }}" class="needs-validation" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <!-- Coluna Esquerda -->
                            <div class="col-lg-6">
                                <!-- TAG -->
                                <div class="mb-3">
                                    <label for="tag" class="form-label">
                                        <i class="fas fa-tag me-1"></i>
                                        TAG do Equipamento <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('tag') is-invalid @enderror" 
                                           id="tag" 
                                           name="tag" 
                                           value="{{ old('tag', $forcing->tag) }}" 
                                           required
                                           placeholder="Ex: P-101, V-205, FCV-301">
                                    @error('tag')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Descrição do Equipamento -->
                                <div class="mb-3">
                                    <label for="descricao_equipamento" class="form-label">
                                        <i class="fas fa-tools me-1"></i>
                                        Descrição do Equipamento <span class="text-danger">*</span>
                                    </label>
                                    <textarea class="form-control @error('descricao_equipamento') is-invalid @enderror" 
                                              id="descricao_equipamento" 
                                              name="descricao_equipamento" 
                                              rows="3" 
                                              required
                                              placeholder="Descreva o equipamento (tipo, função, localização...)">{{ old('descricao_equipamento', $forcing->descricao_equipamento) }}</textarea>
                                    @error('descricao_equipamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Área -->
                                <div class="mb-3">
                                    <label for="area" class="form-label">
                                        <i class="fas fa-map-marker-alt me-1"></i>
                                        Área <span class="text-danger">*</span>
                                    </label>
                                    <input type="text" 
                                           class="form-control @error('area') is-invalid @enderror" 
                                           id="area" 
                                           name="area" 
                                           value="{{ old('area', $forcing->area) }}" 
                                           required
                                           placeholder="Ex: Produção, Manutenção, Utilidades...">
                                    @error('area')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Unidade (apenas para admins) -->
                                @can('admin')
                                <div class="mb-3">
                                    <label for="unit_id" class="form-label">
                                        <i class="fas fa-building me-1"></i>
                                        Unidade
                                    </label>
                                    <select class="form-select @error('unit_id') is-invalid @enderror" 
                                            id="unit_id" 
                                            name="unit_id">
                                        <option value="">Selecione a unidade</option>
                                        @foreach($units ?? [] as $unit)
                                            <option value="{{ $unit->id }}" 
                                                    {{ old('unit_id', $forcing->unit_id) == $unit->id ? 'selected' : '' }}>
                                                {{ $unit->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('unit_id')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endcan
                            </div>

                            <!-- Coluna Direita -->
                            <div class="col-lg-6">
                                <!-- Situação do Equipamento -->
                                <div class="mb-3">
                                    <label for="situacao_equipamento" class="form-label">
                                        <i class="fas fa-exclamation-triangle me-1"></i>
                                        Situação do Equipamento <span class="text-danger">*</span>
                                    </label>
                                    <select class="form-select @error('situacao_equipamento') is-invalid @enderror" 
                                            id="situacao_equipamento" 
                                            name="situacao_equipamento" 
                                            required>
                                        <option value="">Selecione a situação...</option>
                                        <option value="desativado" {{ old('situacao_equipamento', $forcing->situacao_equipamento) == 'desativado' ? 'selected' : '' }}>Desativado</option>
                                        <option value="ativacao_futura" {{ old('situacao_equipamento', $forcing->situacao_equipamento) == 'ativacao_futura' ? 'selected' : '' }}>Ativação Futura</option>
                                        <option value="em_atividade" {{ old('situacao_equipamento', $forcing->situacao_equipamento) == 'em_atividade' ? 'selected' : '' }}>Em Atividade</option>
                                    </select>
                                    @error('situacao_equipamento')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Observações -->
                                <div class="mb-3">
                                    <label for="observacoes" class="form-label">
                                        <i class="fas fa-comment me-1"></i>
                                        Observações
                                    </label>
                                    <textarea class="form-control @error('observacoes') is-invalid @enderror" 
                                              id="observacoes" 
                                              name="observacoes" 
                                              rows="3"
                                              placeholder="Observações adicionais (opcional)...">{{ old('observacoes', $forcing->observacoes) }}</textarea>
                                    @error('observacoes')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>

                                <!-- Status (apenas para liberadores e admins) -->
                                @can('liberar', $forcing)
                                <div class="mb-3">
                                    <label for="status" class="form-label">
                                        <i class="fas fa-flag me-1"></i>
                                        Status do Forcing
                                    </label>
                                    <select class="form-select @error('status') is-invalid @enderror" 
                                            id="status" 
                                            name="status">
                                        @php
                                            $statusOptions = [
                                                'pendente' => 'Pendente',
                                                'liberado' => 'Liberado',
                                                'forcado' => 'Forçado',
                                                'solicitacao_retirada' => 'Solicitação de Retirada',
                                                'retirado' => 'Retirado'
                                            ];
                                        @endphp
                                        @foreach($statusOptions as $value => $label)
                                            <option value="{{ $value }}" 
                                                    {{ old('status', $forcing->status) === $value ? 'selected' : '' }}>
                                                {{ $label }}
                                            </option>
                                        @endforeach
                                    </select>
                                    @error('status')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                </div>
                                @endcan
                            </div>
                        </div>

                        <!-- Campos específicos baseado no status -->
                        <div id="status-fields">
                            @if($forcing->status === 'liberado' || $forcing->status === 'forcado')
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-info">
                                        <div class="card-header bg-info text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-check-circle me-2"></i>
                                                Informações de Liberação
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="observacoes_liberacao" class="form-label">
                                                    Observações da Liberação
                                                    @if($forcing->liberado_por && $forcing->liberado_por !== auth()->id())
                                                        <small class="text-muted">(Apenas {{ $forcing->liberador->name ?? 'o liberador responsável' }} pode editar)</small>
                                                    @endif
                                                </label>
                                                @if($forcing->liberado_por && $forcing->liberado_por === auth()->id())
                                                    {{-- Liberador responsável pode editar --}}
                                                    <textarea class="form-control" 
                                                              id="observacoes_liberacao" 
                                                              name="observacoes_liberacao" 
                                                              rows="3"
                                                              placeholder="Observações sobre a liberação...">{{ old('observacoes_liberacao', $forcing->observacoes_liberacao) }}</textarea>
                                                @elseif(auth()->user()->perfil === 'admin')
                                                    {{-- Admin pode editar --}}
                                                    <textarea class="form-control" 
                                                              id="observacoes_liberacao" 
                                                              name="observacoes_liberacao" 
                                                              rows="3"
                                                              placeholder="Observações sobre a liberação...">{{ old('observacoes_liberacao', $forcing->observacoes_liberacao) }}</textarea>
                                                    <small class="text-info">
                                                        <i class="fas fa-user-shield"></i> Você está editando como administrador
                                                    </small>
                                                @else
                                                    {{-- Outros usuários apenas visualizam --}}
                                                    <textarea class="form-control" 
                                                              id="observacoes_liberacao" 
                                                              name="observacoes_liberacao_readonly" 
                                                              rows="3"
                                                              readonly
                                                              style="background-color: #f8f9fa;">{{ $forcing->observacoes_liberacao ?? 'Nenhuma observação registrada.' }}</textarea>
                                                    <input type="hidden" name="observacoes_liberacao" value="{{ $forcing->observacoes_liberacao }}">
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(in_array($forcing->status, ['forcado', 'solicitacao_retirada', 'retirado']))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-success">
                                        <div class="card-header bg-success text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-cog me-2"></i>
                                                Informações de Execução
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="observacoes_execucao" class="form-label">
                                                    Observações da Execução
                                                </label>
                                                <textarea class="form-control" 
                                                          id="observacoes_execucao" 
                                                          name="observacoes_execucao" 
                                                          rows="3"
                                                          placeholder="Observações sobre a execução do forcing...">{{ old('observacoes_execucao', $forcing->observacoes_execucao) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif

                            @if(in_array($forcing->status, ['solicitacao_retirada', 'retirado']))
                            <div class="row mt-4">
                                <div class="col-12">
                                    <div class="card border-secondary">
                                        <div class="card-header bg-secondary text-white">
                                            <h6 class="mb-0">
                                                <i class="fas fa-undo me-2"></i>
                                                Informações de Retirada
                                            </h6>
                                        </div>
                                        <div class="card-body">
                                            <div class="mb-3">
                                                <label for="descricao_resolucao" class="form-label">
                                                    Descrição da Resolução
                                                </label>
                                                <textarea class="form-control" 
                                                          id="descricao_resolucao" 
                                                          name="descricao_resolucao" 
                                                          rows="3"
                                                          placeholder="Descreva como o problema foi resolvido...">{{ old('descricao_resolucao', $forcing->descricao_resolucao) }}</textarea>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endif
                        </div>

                        <!-- Botões de Ação -->
                        <div class="row mt-4">
                            <div class="col-12">
                                <div class="d-flex justify-content-between">
                                    <div>
                                        <a href="{{ route('forcing.index') }}" class="btn btn-secondary">
                                            <i class="fas fa-arrow-left me-2"></i>
                                            Voltar
                                        </a>
                                    </div>
                                    <div>
                                        <button type="submit" class="btn btn-primary">
                                            <i class="fas fa-save me-2"></i>
                                            Salvar Alterações
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Sistema de Controle de Forcing - JavaScript para edição
document.addEventListener('DOMContentLoaded', function() {
    // Validação de formulário Bootstrap
    const form = document.querySelector('.needs-validation');
    if (form) {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    }

    // Mostrar/ocultar campos baseado no status
    const statusSelect = document.getElementById('status');
    if (statusSelect) {
        statusSelect.addEventListener('change', function() {
            updateStatusFields(this.value);
        });
    }

    function updateStatusFields(status) {
        const statusFields = document.getElementById('status-fields');
        if (!statusFields) return;

        // Lógica para mostrar campos específicos baseado no status
        // Implementar conforme necessário para Sistema de Controle de Forcing
        console.log('Status alterado para:', status);
    }
});
</script>
@endpush
