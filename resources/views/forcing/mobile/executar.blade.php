@extends('layouts.app')

@section('title', 'Registrar Execução')

@section('content')
<div class="container-fluid px-3">
    <!-- Header Mobile -->
    <div class="d-flex align-items-center mb-4">
        <a href="{{ route('forcing.index') }}" class="btn btn-outline-secondary me-3">
            <i class="fas fa-arrow-left"></i>
        </a>
        <div>
            <h1 class="h4 mb-0">
                <i class="fas fa-tools text-primary me-2"></i>
                Registrar Execução
            </h1>
            <small class="text-muted">Registre a execução do forcing</small>
        </div>
    </div>

    <!-- Informações do Forcing -->
    <div class="card mb-4">
        <div class="card-header bg-primary text-white">
            <h5 class="mb-0">
                <i class="fas fa-info-circle me-2"></i>
                Informações do Forcing
            </h5>
        </div>
        <div class="card-body">
            <div class="row g-3">
                <div class="col-12">
                    <label class="form-label fw-bold">TAG:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->tag }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Descrição:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->descricao_equipamento }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Área:</label>
                    <p class="form-control-plaintext bg-light p-2 rounded">{{ $forcing->area }}</p>
                </div>
                <div class="col-12">
                    <label class="form-label fw-bold">Status:</label>
                    <span class="badge bg-success fs-6">{{ ucfirst($forcing->status) }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Formulário de Execução -->
    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">
                <i class="fas fa-edit me-2"></i>
                Dados da Execução
            </h5>
        </div>
        <div class="card-body">
            <form action="{{ route('forcing.registrar-execucao', $forcing) }}" method="POST">
                @csrf
                
                <div class="mb-4">
                    <label for="local_execucao" class="form-label">
                        <i class="fas fa-map-marker-alt me-1"></i>
                        Local de Execução <span class="text-danger">*</span>
                    </label>
                    <select class="form-select form-select-lg" id="local_execucao" name="local_execucao" required>
                        <option value="">Selecione o local...</option>
                        <option value="supervisorio">🖥️ Supervisório</option>
                        <option value="plc">⚙️ PLC</option>
                        <option value="local">📍 Local</option>
                    </select>
                    <div class="form-text">Escolha onde o forcing será executado</div>
                </div>

                <div class="mb-4">
                    <label for="observacoes_execucao" class="form-label">
                        <i class="fas fa-comment-alt me-1"></i>
                        Observações da Execução
                    </label>
                    <textarea class="form-control form-control-lg" id="observacoes_execucao" 
                              name="observacoes_execucao" rows="5" 
                              placeholder="Descreva detalhes sobre a execução do forcing (opcional)"></textarea>
                    <div class="form-text">Informações adicionais sobre como foi executado o forcing</div>
                </div>

                <!-- Botões de Ação -->
                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg" id="btnExecutar">
                        <i class="fas fa-tools me-2"></i>
                        <span class="btn-text">Registrar Execução</span>
                    </button>
                    <a href="{{ route('forcing.index') }}" class="btn btn-outline-secondary btn-lg" id="btnCancelar">
                        <i class="fas fa-times me-2"></i>
                        Cancelar
                    </a>
                </div>
            </form>
        </div>
    </div>

    <!-- Aviso de Confirmação -->
    <div class="alert alert-info mt-4">
        <i class="fas fa-info-circle me-2"></i>
        <strong>Atenção:</strong> Ao registrar a execução, o forcing será marcado como executado e ficará disponível para retirada.
    </div>
</div>

<style>
/* Estilos específicos para mobile */
@media (max-width: 768px) {
    .container-fluid {
        padding: 1rem !important;
    }
    
    .card {
        border-radius: 1rem !important;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1) !important;
    }
    
    .card-header {
        border-radius: 1rem 1rem 0 0 !important;
        padding: 1rem !important;
    }
    
    .card-body {
        padding: 1.5rem !important;
    }
    
    .btn-lg {
        padding: 1rem 1.5rem !important;
        font-size: 1.1rem !important;
        border-radius: 0.75rem !important;
    }
    
    .form-control-lg,
    .form-select-lg {
        font-size: 16px !important; /* Previne zoom no iOS */
        padding: 1rem !important;
        border-radius: 0.75rem !important;
        border: 2px solid #dee2e6 !important;
    }
    
    .form-control-lg:focus,
    .form-select-lg:focus {
        border-color: #0d6efd !important;
        box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25) !important;
    }
    
    .form-label {
        font-size: 1rem !important;
        font-weight: 600 !important;
        margin-bottom: 0.75rem !important;
    }
    
    .form-text {
        font-size: 0.9rem !important;
        margin-top: 0.5rem !important;
    }
    
    .badge {
        font-size: 0.9rem !important;
        padding: 0.5rem 1rem !important;
    }
    
    .alert {
        border-radius: 0.75rem !important;
        padding: 1rem !important;
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const form = document.querySelector('form[action*="/registrar-execucao"]');
    const btnExecutar = document.getElementById('btnExecutar');
    const btnCancelar = document.getElementById('btnCancelar');
    
    if (form && btnExecutar) {
        form.addEventListener('submit', function(e) {
            // Validar campos obrigatórios
            const localExecucao = document.getElementById('local_execucao');
            if (!localExecucao.value) {
                e.preventDefault();
                localExecucao.focus();
                localExecucao.classList.add('is-invalid');
                return;
            }
            
            // Mostrar loading no botão
            btnExecutar.disabled = true;
            btnExecutar.classList.add('btn-loading');
            btnExecutar.innerHTML = `
                <div class="loading-spinner loading-executar"></div>
                <span class="btn-text">Executando...</span>
            `;
            
            // Desabilitar botão de cancelar
            btnCancelar.style.pointerEvents = 'none';
            btnCancelar.style.opacity = '0.5';
            
            // Mostrar overlay de loading
            const overlay = document.createElement('div');
            overlay.className = 'loading-overlay show';
            overlay.innerHTML = `
                <div class="loading-overlay-content">
                    <div class="loading-overlay-spinner loading-executar"></div>
                    <div class="loading-overlay-text">Registrando execução...</div>
                    <div class="mt-2">
                        <small class="text-muted">Por favor, aguarde...</small>
                    </div>
                </div>
            `;
            document.body.appendChild(overlay);
            
            // Simular processamento e redirecionamento
            setTimeout(() => {
                // Mostrar mensagem de sucesso
                const successOverlay = document.createElement('div');
                successOverlay.className = 'loading-overlay show';
                successOverlay.innerHTML = `
                    <div class="loading-overlay-content">
                        <div class="text-success mb-3">
                            <i class="fas fa-check-circle fa-3x"></i>
                        </div>
                        <div class="loading-overlay-text">Execução registrada com sucesso!</div>
                        <div class="mt-2">
                            <small class="text-muted">Redirecionando para detalhes...</small>
                        </div>
                    </div>
                `;
                
                // Substituir overlay de loading pelo de sucesso
                overlay.remove();
                document.body.appendChild(successOverlay);
                
                // Redirecionar após 800ms
                setTimeout(() => {
                    window.location.href = "{{ route('forcing.show', $forcing->id) }}";
                }, 800);
            }, 500); // Simular processamento de 500ms
        });
    }
});
</script>
@endsection
