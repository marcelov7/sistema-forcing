@extends('layouts.app')

@section('title', 'Nova Alteração Elétrica - Sistema de Forcing')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <h2 class="h3 mb-0">
                        <i class="fas fa-bolt text-warning"></i>
                        Nova Alteração Elétrica
                    </h2>
                    <p class="text-muted mb-0">Controle de Alterações Elétricas e Lógicas</p>
                </div>
                <a href="{{ route('alteracoes.index') }}" class="btn btn-outline-secondary">
                    <i class="fas fa-arrow-left"></i> Voltar
                </a>
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
                                BR-RE-XXXX - Versão 1.0<br>
                                Publicado em: {{ now()->format('d/m/Y') }}
                            </small>
                        </div>
                    </div>
                </div>
                
                <div class="card-body">
                    <form method="POST" action="{{ route('alteracoes.store') }}" id="alteracaoForm">
                        @csrf
                        
                        <!-- Informações do Solicitante -->
                        <div class="row mb-4">
                            <div class="col-md-4">
                                <label for="solicitante" class="form-label fw-bold">
                                    <i class="fas fa-user text-primary"></i> Solicitante
                                </label>
                                <input type="text" 
                                       class="form-control @error('solicitante') is-invalid @enderror" 
                                       id="solicitante" 
                                       name="solicitante" 
                                       value="{{ old('solicitante', auth()->user()->name) }}" 
                                       placeholder="Nome do solicitante"
                                       readonly
                                       style="background-color: #f8f9fa;">
                                <small class="text-muted">
                                    <i class="fas fa-info-circle"></i> Preenchido automaticamente com seu nome
                                </small>
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
                                       value="{{ old('departamento') }}" 
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
                                       value="{{ old('data_solicitacao', now()->format('Y-m-d')) }}" 
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
                                      required>{{ old('descricao_alteracao') }}</textarea>
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
                                      required>{{ old('motivo_alteracao') }}</textarea>
                            @error('motivo_alteracao')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>


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
                            <a href="{{ route('alteracoes.index') }}" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                            
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-save"></i> Criar Alteração
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
