@extends('layouts.app')

@section('title', 'Editar ' . $alteracao->numero_documento . ' - Sistema de Forcing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-edit text-warning"></i>
                        Editar Alteração Elétrica
                    </h2>
                    <p class="text-muted mb-0">{{ $alteracao->numero_documento }} - Versão {{ $alteracao->versao }}</p>
                </div>
                <div class="btn-group">
                    <a href="{{ route('alteracoes.show', $alteracao) }}" class="btn btn-outline-secondary">
                        <i class="fas fa-arrow-left"></i> Voltar
                    </a>
                    <a href="{{ route('alteracoes.index') }}" class="btn btn-outline-primary">
                        <i class="fas fa-list"></i> Lista
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário -->
    <div class="row">
        <div class="col-lg-8 mx-auto">
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
                    <form method="POST" action="{{ route('alteracoes.update', $alteracao) }}" id="alteracaoForm">
                        @csrf
                        @method('PUT')
                        
                        <!-- Informações do Solicitante -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="solicitante" class="form-label fw-bold">
                                    <i class="fas fa-user text-primary"></i> Solicitante
                                </label>
                                @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                                    <input type="text" 
                                           class="form-control @error('solicitante') is-invalid @enderror" 
                                           id="solicitante" 
                                           name="solicitante" 
                                           value="{{ old('solicitante', $alteracao->solicitante) }}" 
                                           placeholder="Nome do solicitante"
                                           required>
                                    <small class="text-muted">
                                        <i class="fas fa-edit"></i> Editável apenas por administradores
                                    </small>
                                @else
                                    <input type="text" 
                                           class="form-control" 
                                           value="{{ $alteracao->solicitante }}" 
                                           readonly
                                           style="background-color: #f8f9fa;">
                                    <small class="text-muted">
                                        <i class="fas fa-lock"></i> Apenas administradores podem editar
                                    </small>
                                @endif
                                @error('solicitante')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="departamento" class="form-label fw-bold">
                                    <i class="fas fa-building text-primary"></i> Departamento
                                </label>
                                <input type="text" 
                                       class="form-control @error('departamento') is-invalid @enderror" 
                                       id="departamento" 
                                       name="departamento" 
                                       value="{{ old('departamento', $alteracao->departamento) }}" 
                                       placeholder="Nome do departamento"
                                       required>
                                @error('departamento')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                            
                            <div class="col-md-4">
                                <label for="data_solicitacao" class="form-label fw-bold">
                                    <i class="fas fa-calendar text-primary"></i> Data
                                </label>
                                <input type="date" 
                                       class="form-control @error('data_solicitacao') is-invalid @enderror" 
                                       id="data_solicitacao" 
                                       name="data_solicitacao" 
                                       value="{{ old('data_solicitacao', $alteracao->data_solicitacao->format('Y-m-d')) }}" 
                                       required>
                                @error('data_solicitacao')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>


                        <hr class="my-4">

                        <!-- Descrição da Alteração -->
                        <div class="mb-4">
                            <label for="descricao_alteracao" class="form-label fw-bold">
                                <i class="fas fa-edit text-primary"></i> 
                                DESCRIÇÃO DA ALTERAÇÃO NECESSÁRIA:
                            </label>
                            <textarea class="form-control @error('descricao_alteracao') is-invalid @enderror" 
                                      id="descricao_alteracao" 
                                      name="descricao_alteracao" 
                                      rows="6" 
                                      placeholder="Descreva detalhadamente a alteração elétrica ou lógica necessária..."
                                      required>{{ old('descricao_alteracao', $alteracao->descricao_alteracao) }}</textarea>
                            @error('descricao_alteracao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <hr class="my-4">

                        <!-- Motivo da Alteração -->
                        <div class="mb-4">
                            <label for="motivo_alteracao" class="form-label fw-bold">
                                <i class="fas fa-question-circle text-primary"></i> 
                                MOTIVO DA ALTERAÇÃO:
                            </label>
                            <textarea class="form-control @error('motivo_alteracao') is-invalid @enderror" 
                                      id="motivo_alteracao" 
                                      name="motivo_alteracao" 
                                      rows="6" 
                                      placeholder="Explique o motivo e justificativa para esta alteração..."
                                      required>{{ old('motivo_alteracao', $alteracao->motivo_alteracao) }}</textarea>
                            @error('motivo_alteracao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


                        <!-- Status (apenas para admins) -->
                        @if(auth()->user()->perfil === 'admin' || auth()->user()->is_super_admin)
                            <div class="mb-4">
                                <label for="status" class="form-label fw-bold">
                                    <i class="fas fa-cog text-primary"></i> 
                                    Status:
                                </label>
                                <select class="form-select @error('status') is-invalid @enderror" 
                                        id="status" 
                                        name="status">
                                    <option value="pendente" {{ old('status', $alteracao->status) == 'pendente' ? 'selected' : '' }}>Pendente</option>
                                    <option value="em_analise" {{ old('status', $alteracao->status) == 'em_analise' ? 'selected' : '' }}>Em Análise</option>
                                    <option value="aprovada" {{ old('status', $alteracao->status) == 'aprovada' ? 'selected' : '' }}>Aprovada</option>
                                    <option value="rejeitada" {{ old('status', $alteracao->status) == 'rejeitada' ? 'selected' : '' }}>Rejeitada</option>
                                    <option value="implementada" {{ old('status', $alteracao->status) == 'implementada' ? 'selected' : '' }}>Implementada</option>
                                </select>
                                @error('status')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        @endif

                        <!-- Termo de Concordância -->
                        <div class="alert alert-info">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="concordo" required>
                                <label class="form-check-label fw-bold" for="concordo">
                                    Estou ciente das alterações acima descritas e concordo que permanecerão válidas por tempo indeterminado até que uma nova versão deste documento seja validada pelos responsáveis.
                                </label>
                            </div>
                        </div>

                        <!-- Botões -->
                        <div class="d-flex justify-content-between">
                            <a href="{{ route('alteracoes.show', $alteracao) }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Salvar Alterações
                            </button>
                        </div>
                    </form>
                </div>
            </div>
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

.form-control, .form-select {
    border: 2px solid #e9ecef;
    transition: border-color 0.3s ease;
}

.form-control:focus, .form-select:focus {
    border-color: #0d6efd;
    box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
}

textarea.form-control {
    resize: vertical;
    min-height: 120px;
}

.btn-primary {
    background: linear-gradient(135deg, #0d6efd, #0056b3);
    border: none;
    padding: 12px 30px;
    font-weight: 600;
}

.btn-primary:hover {
    background: linear-gradient(135deg, #0056b3, #004085);
    transform: translateY(-2px);
    box-shadow: 0 5px 15px rgba(13, 110, 253, 0.4);
}

.alert-info {
    background: linear-gradient(135deg, #d1ecf1, #bee5eb);
    border: 2px solid #17a2b8;
    border-radius: 8px;
}

.form-check-input:checked {
    background-color: #0d6efd;
    border-color: #0d6efd;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Validação do formulário
    const form = document.getElementById('alteracaoForm');
    const concordo = document.getElementById('concordo');
    
    form.addEventListener('submit', function(e) {
        if (!concordo.checked) {
            e.preventDefault();
            alert('Você deve concordar com os termos para continuar.');
            concordo.focus();
            return false;
        }
    });
    
    // Auto-resize para textareas
    const textareas = document.querySelectorAll('textarea');
    textareas.forEach(textarea => {
        textarea.addEventListener('input', function() {
            this.style.height = 'auto';
            this.style.height = (this.scrollHeight) + 'px';
        });
    });
});
</script>
@endsection
